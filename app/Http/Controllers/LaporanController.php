<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\ReturPembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman daftar laporan
     */
    public function index()
    {
        return view('laporan.index');
    }

    /**
     * Laporan Pembelian Tunai
     */
     public function pembelianTunai(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        // Set default date range if not provided
        $tanggalMulai = $request->tanggal_mulai 
            ? Carbon::parse($request->tanggal_mulai)->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $tanggalAkhir = $request->tanggal_akhir 
            ? Carbon::parse($request->tanggal_akhir)->endOfDay() 
            : Carbon::now()->endOfDay();

        // Get pembelian data
        $pembelianTunai = Pembelian::with(['supplier', 'detailPembelian.obat', 'users'])
            ->where('jenis_pembayaran', 'tunai')
            ->whereBetween('tanggal_pembelian', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();

        // Calculate total pembelian
        $totalPembelian = $pembelianTunai->sum('total');
        
        // If request is for PDF export
        if ($request->has('export') && $request->export == 'pdf') {
            // Generate PDF (you need to implement this part if needed)
            // Here I'm assuming a simple redirect back for now
            return redirect()->back()->with('info', 'Export PDF functionality will be implemented later');
        }

        return view('laporan.pembelian_tunai', compact('pembelianTunai', 'totalPembelian', 'tanggalMulai', 'tanggalAkhir'));
    }

    /**
     * Laporan Pembelian Kredit
     */
    public function pembelianKredit(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'nullable|in:semua,lunas,belum_lunas',
        ]);

        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai)->startOfDay() : Carbon::now()->startOfMonth();
        $tanggalAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir)->endOfDay() : Carbon::now()->endOfDay();
        $status = $request->status ?? 'semua';

        $query = Pembelian::with(['supplier', 'detailPembelian.obat', 'pembayaran'])
            ->where('jenis_pembayaran', 'kredit')
            ->whereBetween('tanggal_pembelian', [$tanggalMulai, $tanggalAkhir]);

        if ($status === 'lunas') {
            $query->where('status', 'lunas');
        } elseif ($status === 'belum_lunas') {
            $query->where('status', '!=', 'lunas');
        }

        $pembelianKredit = $query->orderBy('tanggal_pembelian', 'desc')->get();

        $totalPembelian = $pembelianKredit->sum('total');
        $totalLunas = $pembelianKredit->where('status', 'lunas')->sum('total');
        $totalBelumLunas = $pembelianKredit->where('status', '!=', 'lunas')->sum('total');
        
        // Export to CSV if requested
        if ($request->export == 'csv') {
            $headings = ['No', 'No Faktur', 'Tanggal', 'Supplier', 'Jumlah Item', 'Total Harga', 'Status'];
            $data = [];
            
            foreach ($pembelianKredit as $index => $pembelian) {
                $data[] = [
                    $index + 1,
                    $pembelian->kode_pembelian,
                    Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y'),
                    $pembelian->supplier->nama_supplier,
                    $pembelian->detailPembelian->count(),
                    $pembelian->total,
                    ucfirst($pembelian->status)
                ];
            }
            
            return $this->exportToCSV($data, $headings, 'pembelian_kredit_' . date('Y-m-d') . '.csv');
        }
        
        // Export to PDF if requested
        if ($request->export == 'pdf') {
            return view('laporan.pembelian_kredit_pdf', compact(
                'pembelianKredit', 
                'totalPembelian', 
                'totalLunas',
                'totalBelumLunas',
                'tanggalMulai', 
                'tanggalAkhir', 
                'status'
            ));
        }

        return view('laporan.pembelian_kredit', compact(
            'pembelianKredit', 
            'totalPembelian', 
            'totalLunas',
            'totalBelumLunas',
            'tanggalMulai', 
            'tanggalAkhir', 
            'status'
        ));
    }

    /**
     * Laporan Retur Pembelian
     */
    public function returPembelian(Request $request)
{
    $request->validate([
        'tanggal_mulai' => 'nullable|date',
        'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        'supplier_id' => 'nullable|exists:suppliers,id',
    ]);

    $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai)->startOfDay() : Carbon::now()->startOfMonth();
    $tanggalAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir)->endOfDay() : Carbon::now()->endOfDay();
    $supplierId = $request->supplier_id;

    $query = ReturPembelian::with(['penerimaanPembelian.pembelian.supplier', 'items.obat', 'penerimaanPembelian.pembelian'])
        ->whereBetween('tanggal_retur', [$tanggalMulai, $tanggalAkhir]);

    if ($supplierId) {
        $query->whereHas('penerimaanPembelian.pembelian', function($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        });
    }

    $returPembelian = $query->orderBy('tanggal_retur', 'desc')->get();
    $suppliers = Supplier::orderBy('nama_supplier')->get();

    $totalNilaiRetur = $returPembelian->sum('total_retur');
    
    // Export to CSV if requested
    if ($request->export == 'csv') {
        $headings = ['No', 'No Retur', 'Tanggal', 'No Faktur', 'Supplier', 'Jumlah Item', 'Total Nilai'];
        $data = [];
        
        foreach ($returPembelian as $index => $retur) {
            $data[] = [
                $index + 1,
                $retur->id, // Assuming there's no specific retur code
                Carbon::parse($retur->tanggal_retur)->format('d/m/Y'),
                $retur->penerimaanPembelian->pembelian->kode_pembelian,
                $retur->penerimaanPembelian->pembelian->supplier->nama_supplier,
                $retur->items->count(),
                $retur->total_retur
            ];
        }
        
        return $this->exportToCSV($data, $headings, 'retur_pembelian_' . date('Y-m-d') . '.csv');
    }
    
    // Export to PDF if requested
    if ($request->export == 'pdf') {
        return view('laporan.retur_pembelian_pdf', compact(
            'returPembelian',
            'totalNilaiRetur',
            'tanggalMulai', 
            'tanggalAkhir',
            'supplierId',
            'suppliers'
        ));
    }

    return view('laporan.retur_pembelian', compact(
        'returPembelian',
        'totalNilaiRetur',
        'tanggalMulai', 
        'tanggalAkhir',
        'supplierId',
        'suppliers'
    ));
}

    /**
     * Laporan Penjualan
     */
    public function penjualan(Request $request)
{
    $request->validate([
        'tanggal_mulai' => 'nullable|date',
        'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        'jenis_penjualan' => 'nullable|in:semua,dengan_resep,tanpa_resep',
    ]);

    $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai)->startOfDay() : Carbon::now()->startOfMonth();
    $tanggalAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir)->endOfDay() : Carbon::now()->endOfDay();
    $jenisPenjualan = $request->jenis_penjualan ?? 'semua';

    $query = Penjualan::with(['details.obat', 'user'])
        ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);

    if ($jenisPenjualan !== 'semua') {
        $query->where('jenis_penjualan', $jenisPenjualan);
    }

    $penjualan = $query->orderBy('tanggal', 'desc')->get();

    $totalPenjualan = $penjualan->sum('total_harga');
    $totalDenganResep = $penjualan->where('jenis_penjualan', 'dengan_resep')->sum('total_harga');
    $totalTanpaResep = $penjualan->where('jenis_penjualan', 'tanpa_resep')->sum('total_harga');
    
    // Export to CSV if requested
    if ($request->export == 'csv') {
        $headings = ['No', 'No Nota', 'Tanggal', 'Jenis', 'Total Harga', 'Status', 'Petugas'];
        $data = [];
        
        foreach ($penjualan as $index => $item) {
            $data[] = [
                $index + 1,
                $item->nomor_nota,
                Carbon::parse($item->tanggal)->format('d/m/Y'),
                $item->jenis_penjualan == 'dengan_resep' ? 'Dengan Resep' : 'Tanpa Resep',
                $item->total_harga,
                $item->status_pembayaran == 'sudah_dibayar' ? 'Sudah Dibayar' : 'Belum Dibayar',
                $item->user->name
            ];
        }
        
        return $this->exportToCSV($data, $headings, 'penjualan_' . date('Y-m-d') . '.csv');
    }
    
    // Export to PDF if requested
    if ($request->export == 'pdf') {
        return view('laporan.penjualan_pdf', compact(
            'penjualan',
            'totalPenjualan',
            'totalDenganResep',
            'totalTanpaResep',
            'tanggalMulai', 
            'tanggalAkhir',
            'jenisPenjualan'
        ));
    }

    return view('laporan.penjualan', compact(
        'penjualan',
        'totalPenjualan',
        'totalDenganResep',
        'totalTanpaResep',
        'tanggalMulai', 
        'tanggalAkhir',
        'jenisPenjualan'
    ));
}

    /**
     * Laporan Obat Paling Laku
     */
    public function obatPalingLaku(Request $request)
{
    $request->validate([
        'tanggal_mulai' => 'nullable|date',
        'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        'limit' => 'nullable|integer|min:1|max:100',
    ]);

    $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai)->startOfDay() : Carbon::now()->startOfMonth();
    $tanggalAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir)->endOfDay() : Carbon::now()->endOfDay();
    $limit = $request->limit ?? 10;

    // Query untuk mendapatkan obat yang paling laku
    $obatPalingLaku = DB::table('penjualan_details')
        ->join('penjualans', 'penjualan_details.penjualan_id', '=', 'penjualans.id')
        ->join('obats', 'penjualan_details.obat_id', '=', 'obats.id')
        ->select(
            'obats.id',
            'obats.kode_obat',
            'obats.nama_obat',
            'obats.jenis_obat',
            'obats.harga_jual',
            DB::raw('SUM(penjualan_details.jumlah) as total_terjual'),
            DB::raw('SUM(penjualan_details.subtotal) as total_pendapatan')
        )
        ->whereBetween('penjualans.tanggal', [$tanggalMulai, $tanggalAkhir])
        ->groupBy('obats.id', 'obats.kode_obat', 'obats.nama_obat', 'obats.jenis_obat', 'obats.harga_jual')
        ->orderBy('total_terjual', 'desc')
        ->limit($limit)
        ->get();

    // Export to CSV if requested
    if ($request->export == 'csv') {
        $headings = ['No', 'Kode Obat', 'Nama Obat', 'Jenis', 'Harga Jual', 'Total Terjual', 'Total Pendapatan'];
        $data = [];
        
        foreach ($obatPalingLaku as $index => $obat) {
            $data[] = [
                $index + 1,
                $obat->kode_obat,
                $obat->nama_obat,
                $obat->jenis_obat,
                $obat->harga_jual,
                $obat->total_terjual,
                $obat->total_pendapatan
            ];
        }
        
        return $this->exportToCSV($data, $headings, 'obat_paling_laku_' . date('Y-m-d') . '.csv');
    }
    
    // Export to PDF if requested
    if ($request->export == 'pdf') {
        return view('laporan.obat_paling_laku_pdf', compact(
            'obatPalingLaku',
            'tanggalMulai', 
            'tanggalAkhir',
            'limit'
        ));
    }

    return view('laporan.obat_paling_laku', compact(
        'obatPalingLaku',
        'tanggalMulai', 
        'tanggalAkhir',
        'limit'
    ));
}

    /**
     * Laporan Persediaan Obat
     */
  public function persediaanObat(Request $request)
{
    $request->validate([
        'jenis_obat' => 'nullable|string',
        'status_stok' => 'nullable|in:semua,tersedia,kosong,hampir_habis',
        'sort_by' => 'nullable|in:kode,nama,stok,kadaluarsa',
        'sort_order' => 'nullable|in:asc,desc',
    ]);

    $jenisObat = $request->jenis_obat;
    $statusStok = $request->status_stok ?? 'semua';
    $sortBy = $request->sort_by ?? 'nama';
    $sortOrder = $request->sort_order ?? 'asc';

    $query = Obat::query();

    // Filter berdasarkan jenis obat
    if ($jenisObat) {
        $query->where('jenis_obat', $jenisObat);
    }

    // Filter berdasarkan status stok
    switch ($statusStok) {
        case 'tersedia':
            $query->where('stok', '>', 0);
            break;
        case 'kosong':
            $query->where('stok', 0);
            break;
        case 'hampir_habis':
            $query->where('stok', '>', 0)->where('stok', '<=', 10);
            break;
    }

    // Mapping untuk sorting
    $sortMapping = [
        'kode' => 'kode_obat',
        'nama' => 'nama_obat',
        'stok' => 'stok',
        'kadaluarsa' => 'kadaluarsa',
    ];

    $obats = $query->orderBy($sortMapping[$sortBy], $sortOrder)->get();

    // Ambil daftar jenis obat yang unik
    $jenisObatList = Obat::select('jenis_obat')->distinct()->orderBy('jenis_obat')->pluck('jenis_obat');

    // Summary persediaan
    $totalObat = $obats->count();
    $totalStok = $obats->sum('stok');
    $totalNilai = $obats->sum(function($obat) {
        return $obat->stok * $obat->harga_beli;
    });
    $obatTersedia = $obats->where('stok', '>', 0)->count();
    $obatKosong = $obats->where('stok', 0)->count();
    $obatHampirHabis = $obats->where('stok', '>', 0)->where('stok', '<=', 10)->count();
    $obatKadaluarsa = $obats->where('kadaluarsa', '<', Carbon::now())->count();
    
    // Export to CSV if requested
    if ($request->export == 'csv') {
        $headings = ['No', 'Kode Obat', 'Nama Obat', 'Jenis', 'Stok', 'Harga Beli', 'Harga Jual', 'Kadaluarsa', 'Status'];
        $data = [];
        
        foreach ($obats as $index => $obat) {
            // Determine status
            $status = "Tersedia";
            if ($obat->stok <= 0) {
                $status = "Kosong";
            } elseif ($obat->stok <= 10) {
                $status = "Hampir Habis";
            }
            if ($obat->kadaluarsa < Carbon::now()) {
                $status = "Kadaluarsa";
            }
            
            $data[] = [
                $index + 1,
                $obat->kode_obat,
                $obat->nama_obat,
                $obat->jenis_obat,
                $obat->stok,
                $obat->harga_beli,
                $obat->harga_jual,
                Carbon::parse($obat->kadaluarsa)->format('d/m/Y'),
                $status
            ];
        }
        
        return $this->exportToCSV($data, $headings, 'persediaan_obat_' . date('Y-m-d') . '.csv');
    }
    
    // Export to PDF if requested
    if ($request->export == 'pdf') {
        return view('laporan.persediaan_obat_pdf', compact(
            'obats',
            'jenisObat',
            'statusStok',
            'sortBy',
            'sortOrder',
            'jenisObatList',
            'totalObat',
            'totalStok',
            'totalNilai',
            'obatTersedia',
            'obatKosong',
            'obatHampirHabis',
            'obatKadaluarsa'
        ));
    }

    return view('laporan.persediaan_obat', compact(
        'obats',
        'jenisObat',
        'statusStok',
        'sortBy',
        'sortOrder',
        'jenisObatList',
        'totalObat',
        'totalStok',
        'totalNilai',
        'obatTersedia',
        'obatKosong',
        'obatHampirHabis',
        'obatKadaluarsa'
    ));
}

        private function exportToCSV($data, $headings, $filename)
    {
        $callback = function() use ($data, $headings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headings);
            
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        return Response::stream($callback, 200, $headers);
    }
}
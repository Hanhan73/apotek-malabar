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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;


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
            'bulan' => 'nullable|date_format:Y-m', // Format: YYYY-MM
        ]);
{
    $request->validate([
        'bulan' => 'nullable|date_format:Y-m', // Format: YYYY-MM
        'supplier_id' => 'nullable|exists:suppliers,id',
    ]);

        // Default bulan berjalan jika tidak diisi
        $bulan = $request->bulan ?? Carbon::now()->format('Y-m');
        
        // Parse bulan dan tentukan tanggal awal-akhir bulan
        $tanggalMulai = Carbon::parse($bulan)->startOfMonth();
        $tanggalAkhir = Carbon::parse($bulan)->endOfMonth();
    // Default bulan berjalan jika tidak diisi
    $bulan = $request->bulan ?? Carbon::now()->format('Y-m');
    $supplierId = $request->supplier_id;
    
    // Parse bulan dan tentukan tanggal awal-akhir bulan
    $tanggalMulai = Carbon::parse($bulan)->startOfMonth();
    $tanggalAkhir = Carbon::parse($bulan)->endOfMonth();

        $pembelianTunai = Pembelian::with(['supplier', 'detailPembelian.obat', 'users'])
            ->where('jenis_pembayaran', 'tunai')
            ->whereBetween('tanggal_pembelian', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('tanggal_pembelian')
            ->get();
    $query = Pembelian::with(['supplier', 'detailPembelian.obat', 'user'])
        ->where('jenis_pembayaran', 'tunai')
        ->whereBetween('tanggal_pembelian', [$tanggalMulai, $tanggalAkhir]);

        $totalPembelian = $pembelianTunai->sum('total');
        
        if ($request->has('export') && $request->export == 'pdf') {
            return Pdf::loadView('laporan.pembelian_tunai_pdf', [
                'pembelianTunai' => $pembelianTunai,
                'totalPembelian' => $totalPembelian,
                'bulan' => $bulan, // Kirim bulan ke view
                'tanggalMulai' => $tanggalMulai,
                'tanggalAkhir' => $tanggalAkhir
            ])->download('laporan_pembelian_tunai_'.$bulan.'.pdf');
        }
    // Filter berdasarkan supplier jika dipilih
    if ($supplierId) {
        $query->where('supplier_id', $supplierId);
    }

        return view('laporan.pembelian_tunai', compact(
            'pembelianTunai', 
            'totalPembelian',
            'bulan',
            'tanggalMulai',
            'tanggalAkhir'
        ));
    $pembelianTunai = $query->orderBy('tanggal_pembelian')->get();
    $suppliers = Supplier::orderBy('nama_supplier')->get();

    $totalPembelian = $pembelianTunai->sum('total');
    
    // Export PDF
    if ($request->has('export') && $request->export == 'pdf') {
        return Pdf::loadView('laporan.pembelian_tunai_pdf', [
            'pembelianTunai' => $pembelianTunai,
            'totalPembelian' => $totalPembelian,
            'bulan' => $bulan,
            'supplierId' => $supplierId,
            'suppliers' => $suppliers,
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir
        ])
        ->setPaper('a4', 'landscape')
        ->download('laporan_pembelian_tunai_'.$bulan.'.pdf');
    }

    return view('laporan.pembelian_tunai', compact(
        'pembelianTunai',
        'totalPembelian',
        'bulan',
        'supplierId',
        'suppliers',
        'tanggalMulai',
        'tanggalAkhir'
    ));
    }
}
public function pembelianKredit(Request $request)
{
    $request->validate([
        'bulan' => 'nullable|date_format:Y-m',
        'status' => 'nullable|in:semua,lunas,belum_lunas',
    ]);

    $bulan = $request->bulan ?? now()->format('Y-m');
    $status = $request->status ?? 'semua';
    
    $tanggalMulai = Carbon::parse($bulan)->startOfMonth();
    $tanggalAkhir = Carbon::parse($bulan)->endOfMonth();

    $query = Pembelian::with(['supplier', 'detailPembelian'])
        ->where('jenis_pembayaran', 'kredit')
        ->whereBetween('tanggal_pembelian', [$tanggalMulai, $tanggalAkhir]);

$pembelianKredit = $query->get()
    ->map(function ($item) {
        $item->sisa_hutang = $item->sisa_pembayaran;
        $item->status_pembayaran = $item->sisa_pembayaran <= 0 ? 'lunas' : 'belum_lunas';

        Log::debug('Calculated values:', [
            'total' => $item->total,
            'sisa' => $item->sisa_pembayaran,
            'total_dibayar' => $item->total_dibayar, // ✅ sekarang pasti 200000
            'status_pembayaran' => $item->status_pembayaran,
        ]);

        return $item;
    });
    // Log filtering activity
    Log::info('Filtering pembelian kredit by status: '.$status);

    if ($status !== 'semua') {
        $pembelianKredit = $pembelianKredit->filter(function($item) use ($status) {
            $matches = $status === 'lunas' 
                ? $item->status_pembayaran === 'lunas'
                : $item->status_pembayaran !== 'lunas';
            
            if (!$matches) {
                Log::debug('Excluding pembelian:', [
                    'id' => $item->id,
                    'kode' => $item->kode_pembelian,
                    'status' => $item->status_pembayaran
                ]);
            }
            
            return $matches;
        });
    }

    // Log totals calculation
    Log::info('Calculating totals', [
        'total_pembelian' => $pembelianKredit->sum('total'),
        'total_dibayar' => $pembelianKredit->sum('total_dibayar'),
        'total_sisa_hutang' => $pembelianKredit->sum('sisa_pembayaran'),
        'count' => $pembelianKredit->count()
    ]);

    // Calculate totals
    $totalPembelian = $pembelianKredit->sum('total');             // Semua tagihan
    $totalDibayar = $pembelianKredit->sum('total_dibayar');       // Sudah dibayar
    $totalSisaHutang = $pembelianKredit->sum('sisa_hutang'); 

    if ($request->has('export') && $request->export == 'pdf') {
        return Pdf::loadView('laporan.pembelian_kredit_pdf', compact(
            'pembelianKredit', 'totalPembelian', 'totalDibayar', 'totalSisaHutang',
            'bulan', 'status', 'tanggalMulai', 'tanggalAkhir'
        ))
        ->setPaper('a4', 'landscape')
        ->download('laporan_pembelian_kredit_'.$bulan.'.pdf');
    }

    return view('laporan.pembelian_kredit', compact(
        'pembelianKredit', 'totalPembelian', 'totalDibayar', 'totalSisaHutang',
        'bulan', 'status', 'tanggalMulai', 'tanggalAkhir'
    ));
}
    /**
     * Laporan Retur Pembelian
     */
    public function returPembelian(Request $request)
{
    $request->validate([
        'bulan' => 'nullable|date_format:Y-m', // Format: YYYY-MM
        'supplier_id' => 'nullable|exists:suppliers,id',
    ]);

    // Default bulan berjalan jika tidak diisi
    $bulan = $request->bulan ?? Carbon::now()->format('Y-m');
    $supplierId = $request->supplier_id;
    
    // Parse bulan dan tentukan tanggal awal-akhir bulan
    $tanggalMulai = Carbon::parse($bulan)->startOfMonth();
    $tanggalAkhir = Carbon::parse($bulan)->endOfMonth();

    $query = ReturPembelian::with([
            'penerimaanPembelian.pembelian.supplier', 
            'penerimaanPembelian.pembelian', 
            'items.obat', 
            'user'
        ])
        ->whereBetween('tanggal_retur', [$tanggalMulai, $tanggalAkhir]);

    // Filter berdasarkan supplier jika dipilih
    if ($supplierId) {
        $query->whereHas('pembelian', function($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        });
    }

    $returPembelian = $query->orderBy('tanggal_retur', 'desc')->get();
    $suppliers = Supplier::orderBy('nama_supplier')->get();

    $totalNilaiRetur = $returPembelian->sum('total_retur');
        $bulan = $request->bulan ?? Carbon::now()->format('Y-m');

    // Export PDF
    if ($request->has('export') && $request->export == 'pdf') {
        return Pdf::loadView('laporan.retur_pembelian_pdf', [
            'returPembelian' => $returPembelian,
            'totalNilaiRetur' => $totalNilaiRetur,
            'bulan' => $bulan,
            'supplierId' => $supplierId,
            'suppliers' => $suppliers,
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir
        ])
        ->setPaper('a4', 'landscape')
        ->download('laporan_retur_pembelian_'.$bulan.'.pdf');
    }

    return view('laporan.retur_pembelian', compact(
        'returPembelian',
        'totalNilaiRetur',
        'bulan',
        'supplierId',
        'suppliers',
        'tanggalMulai',
        'tanggalAkhir'
    ));
}

    /**
     * Laporan Penjualan
     */
    public function penjualan(Request $request)
{
    $request->validate([
        'bulan' => 'nullable|date_format:Y-m', // Format: YYYY-MM
        'jenis_penjualan' => 'nullable|in:semua,dengan_resep,tanpa_resep',
    ]);

    // Default bulan berjalan jika tidak diisi
    $bulan = $request->bulan ?? Carbon::now()->format('Y-m');
    $jenisPenjualan = $request->jenis_penjualan ?? 'semua';
    
    // Parse bulan dan tentukan tanggal awal-akhir bulan
    $tanggalMulai = Carbon::parse($bulan)->startOfMonth();
    $tanggalAkhir = Carbon::parse($bulan)->endOfMonth();

    $query = Penjualan::with(['details.obat', 'user'])
        ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);

    // Filter berdasarkan jenis penjualan
    if ($jenisPenjualan !== 'semua') {
        $query->where('jenis_penjualan', $jenisPenjualan);
    }

    $penjualan = $query->orderBy('tanggal')->get();

    // Hitung total-total
    $totalPenjualan = $penjualan->sum('total_harga');
    $totalDenganResep = $penjualan->where('jenis_penjualan', 'dengan_resep')->sum('total_harga');
    $totalTanpaResep = $penjualan->where('jenis_penjualan', 'tanpa_resep')->sum('total_harga');
    
    // Export PDF
    if ($request->has('export') && $request->export == 'pdf') {
        return Pdf::loadView('laporan.penjualan_pdf', [
            'penjualan' => $penjualan,
            'totalPenjualan' => $totalPenjualan,
            'totalDenganResep' => $totalDenganResep,
            'totalTanpaResep' => $totalTanpaResep,
            'bulan' => $bulan,
            'jenisPenjualan' => $jenisPenjualan,
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir
        ])
        ->setPaper('a4', 'portrait')
        ->download('laporan_penjualan_'.$bulan.'.pdf');
    }

    return view('laporan.penjualan', compact(
        'penjualan',
        'totalPenjualan',
        'totalDenganResep',
        'totalTanpaResep',
        'bulan',
        'jenisPenjualan',
        'tanggalMulai',
        'tanggalAkhir'
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

    $totalPenjualan = $obatPalingLaku->sum('total_terjual');
    $totalPendapatan = $obatPalingLaku->sum('total_pendapatan');
            $bulan = $request->bulan ?? Carbon::now()->format('Y-m');

    // Export PDF
    if ($request->has('export') && $request->export == 'pdf') {
        return Pdf::loadView('laporan.obat_paling_laku_pdf', [
            'obatPalingLaku' => $obatPalingLaku,
            'totalPenjualan' => $totalPenjualan,
            'totalPendapatan' => $totalPendapatan,
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir,
            'limit' => $limit
        ])
        ->setPaper('a4', 'portrait')
        ->download('laporan_obat_paling_laku_'.date('Y-m-d').'.pdf');
    }

    return view('laporan.obat_paling_laku', compact(
        'obatPalingLaku',
        'totalPenjualan',
        'totalPendapatan',
        'bulan',
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

    // Sorting
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
    
    // Export PDF
    if ($request->has('export') && $request->export == 'pdf') {
        return Pdf::loadView('laporan.persediaan_obat_pdf', [
            'obats' => $obats,
            'jenisObat' => $jenisObat,
            'statusStok' => $statusStok,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'jenisObatList' => $jenisObatList,
            'totalObat' => $totalObat,
            'totalStok' => $totalStok,
            'totalNilai' => $totalNilai,
            'obatTersedia' => $obatTersedia,
            'obatKosong' => $obatKosong,
            'obatHampirHabis' => $obatHampirHabis,
            'obatKadaluarsa' => $obatKadaluarsa
        ])
        ->setPaper('a4', 'landscape')
        ->download('laporan_persediaan_obat_'.date('Y-m-d').'.pdf');
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



/**
 * Generate PDF file for report
 *
 * @param string $view View name
 * @param array $data Data untuk view
 * @param string $filename Nama file PDF
 * @return \Illuminate\Http\Response
 */
private function generatePdf($view, $data, $filename)
{
    $pdf = PDF::loadView($view, $data);
    $pdf->setPaper('a4', 'portrait');
    
    return $pdf->download($filename.'.pdf');
}

/**
 * Export to CSV
 *
 * @param array $data Array of data rows
 * @param array $headings Column headings
 * @param string $filename Filename
 * @return \Illuminate\Http\Response
 */
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



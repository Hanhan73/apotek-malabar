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

        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai)->startOfDay() : Carbon::now()->startOfMonth();
        $tanggalAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir)->endOfDay() : Carbon::now()->endOfDay();

        $pembelianTunai = Pembelian::with(['supplier', 'details.obat'])
            ->where('jenis_pembayaran', 'tunai')
            ->whereBetween('tanggal_pembelian', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();

        $totalPembelian = $pembelianTunai->sum('total_harga');
        
        // Jika request adalah untuk export PDF atau print
        if ($request->has('export')) {
            return view('laporan.pembelian_tunai_cetak', compact('pembelianTunai', 'totalPembelian', 'tanggalMulai', 'tanggalAkhir'));
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

        $query = Pembelian::with(['supplier', 'details.obat', 'pembayaran'])
            ->where('jenis_pembayaran', 'kredit')
            ->whereBetween('tanggal_pembelian', [$tanggalMulai, $tanggalAkhir]);

        if ($status === 'lunas') {
            $query->where('status_pembayaran', 'lunas');
        } elseif ($status === 'belum_lunas') {
            $query->where('status_pembayaran', 'belum_lunas');
        }

        $pembelianKredit = $query->orderBy('tanggal_pembelian', 'desc')->get();

        $totalPembelian = $pembelianKredit->sum('total_harga');
        $totalLunas = $pembelianKredit->where('status_pembayaran', 'lunas')->sum('total_harga');
        $totalBelumLunas = $pembelianKredit->where('status_pembayaran', 'belum_lunas')->sum('total_harga');

        // Jika request adalah untuk export PDF atau print
        if ($request->has('export')) {
            return view('laporan.pembelian_kredit_cetak', compact(
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

        $query = ReturPembelian::with(['supplier', 'details.obat', 'pembelian'])
            ->whereBetween('tanggal_retur', [$tanggalMulai, $tanggalAkhir]);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $returPembelian = $query->orderBy('tanggal_retur', 'desc')->get();
        $suppliers = Supplier::orderBy('nama')->get();

        $totalNilaiRetur = $returPembelian->sum('total_nilai_retur');

        // Jika request adalah untuk export PDF atau print
        if ($request->has('export')) {
            return view('laporan.retur_pembelian_cetak', compact(
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
        
        // Jika request adalah untuk export PDF atau print
        if ($request->has('export')) {
            return view('laporan.penjualan_cetak', compact(
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

        // Jika request adalah untuk export PDF atau print
        if ($request->has('export')) {
            return view('laporan.obat_paling_laku_cetak', compact(
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

        // Jika request adalah untuk export PDF atau print
        if ($request->has('export')) {
            return view('laporan.persediaan_obat_cetak', compact(
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
}
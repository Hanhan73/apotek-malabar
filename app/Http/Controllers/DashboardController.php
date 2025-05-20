<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total obat
        $totalObat = Obat::count();
        
        // Obat hampir habis
        $obatHampirHabis = Obat::where('stok', '>', 0)
            ->where('stok', '<=', 10)
            ->count();
            
        // Daftar obat hampir habis (5 teratas)
        $obatHampirHabisList = Obat::where('stok', '>', 0)
            ->where('stok', '<=', 10)
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();
            
        // Pembelian bulan ini
        $pembelianBulanIni = Pembelian::whereMonth('tanggal_pembelian', Carbon::now()->month)
            ->whereYear('tanggal_pembelian', Carbon::now()->year)
            ->count();
            
        // Penjualan bulan ini
        $penjualanBulanIni = Penjualan::whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->count();
            
        // Transaksi terakhir
        $transaksiTerakhir = Penjualan::latest('tanggal')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalObat',
            'obatHampirHabis',
            'obatHampirHabisList',
            'pembelianBulanIni',
            'penjualanBulanIni',
            'transaksiTerakhir'
        ));
    }
}
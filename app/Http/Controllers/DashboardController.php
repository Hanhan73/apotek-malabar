<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Pembelian;
use App\Models\Penjualan;

class DashboardController extends Controller
{
    public function index()
    {
        $totalObat = Obat::count();
        $obatHampirHabis = Obat::where('stok', '<', 10)->count();
        $pembelianBulanIni = Pembelian::whereMonth('tanggal_pembelian', now()->month)->count();

        return view('dashboard', compact(
            'totalObat',
            'obatHampirHabis',
            'pembelianBulanIni',
        ));
    }
}

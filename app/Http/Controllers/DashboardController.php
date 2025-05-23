<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

public function index()
{
    $user = Auth::user();
    $role = $user->role; // Pastikan field `role` ada di tabel users

    $data = [];

    if (in_array($role, ['admin', 'pemilik'])) {
        $data['pembelianBulanIni'] = Pembelian::whereMonth('tanggal_pembelian', Carbon::now()->month)
            ->whereYear('tanggal_pembelian', Carbon::now()->year)
            ->count();

        $data['penjualanBulanIni'] = Penjualan::whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->count();
    }

    if (in_array($role, ['admin', 'apoteker'])) {
        $data['totalObat'] = Obat::count();
        $data['obatHampirHabis'] = Obat::where('stok', '>', 0)
            ->where('stok', '<=', 10)
            ->count();
        $data['obatHampirHabisList'] = Obat::where('stok', '>', 0)
            ->where('stok', '<=', 10)
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();
    }

    if (in_array($role, ['admin', 'apoteker', 'asisten_apoteker'])) {
        $data['transaksiTerakhir'] = Penjualan::latest('tanggal')
            ->limit(5)
            ->get();
    }

    return view('dashboard', $data);
}

}
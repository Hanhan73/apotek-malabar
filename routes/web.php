<?php


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PenerimaanPembelianController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\ReturPembelianController;
use App\Http\Controllers\PembayaranPembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Data Master
    Route::resource('obat', ObatController::class);
    Route::resource('supplier', SupplierController::class);

    // Pembelian
    Route::resource('pembelian', PembelianController::class);
    Route::resource('penerimaan-pembelian', PenerimaanPembelianController::class);
    Route::resource('retur-pembelian', ReturPembelianController::class);
    Route::resource('pembayaran-pembelian', PembayaranPembelianController::class);

    // Penjualan
    Route::resource('penjualan', PenjualanController::class);
    
    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pembelian-tunai', [LaporanController::class, 'pembelianTunai'])->name('laporan.pembelian-tunai');
    Route::get('/laporan/pembelian-kredit', [LaporanController::class, 'pembelianKredit'])->name('laporan.pembelian-kredit');
    Route::get('/laporan/retur-pembelian', [LaporanController::class, 'returPembelian'])->name('laporan.retur-pembelian');
    Route::get('/laporan/penjualan', [LaporanController::class, 'penjualan'])->name('laporan.penjualan');
    Route::get('/laporan/obat-paling-laku', [LaporanController::class, 'obatPalingLaku'])->name('laporan.obat-paling-laku');
    Route::get('/laporan/persediaan-obat', [LaporanController::class, 'persediaanObat'])->name('laporan.persediaan-obat');

    Route::post('/pembelian/{id}/terima', [PenerimaanPembelianController::class, 'store'])
        ->name('penerimaan.store');
    Route::get('/pembelian/{id}/terima', [PenerimaanPembelianController::class, 'create'])
        ->name('penerimaan.create');
    Route::get('/pembelian/{id}/detail', function($id) {
    $pembelian = Pembelian::with(['detailPembelian.obat'])->findOrFail($id);
    return response()->json($pembelian);
    });

});

require __DIR__.'/auth.php';

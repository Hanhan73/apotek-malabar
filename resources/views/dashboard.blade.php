@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
    
    <!-- Cards Row -->
    <div class="row">
        <!-- Total Obat Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Obat</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalObat ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-capsule fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Obat Hampir Habis Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Obat Hampir Habis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $obatHampirHabis ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pembelian Bulan Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pembelian Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pembelianBulanIni ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penjualan Bulan Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Penjualan Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $penjualanBulanIni ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Obat Hampir Habis -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Obat Hampir Habis</h6>
                    <a href="{{ route('obat.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Obat</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($obatHampirHabisList) && count($obatHampirHabisList) > 0)
                                    @foreach($obatHampirHabisList as $obat)
                                    <tr>
                                        <td>{{ $obat->kode_obat }}</td>
                                        <td>{{ $obat->nama_obat }}</td>
                                        <td>{{ $obat->stok }}</td>
                                        <td>
                                            <span class="badge bg-warning">Hampir Habis</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi Terakhir -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Transaksi Terakhir</h6>
                    <a href="{{ route('penjualan.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No Nota</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($transaksiTerakhir) && count($transaksiTerakhir) > 0)
                                    @foreach($transaksiTerakhir as $transaksi)
                                    <tr>
                                        <td>{{ $transaksi->nomor_nota }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}</td>
                                        <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                        <td>
                                            @if($transaksi->status_pembayaran == 'sudah_dibayar')
                                                <span class="badge bg-success">Lunas</span>
                                            @else
                                                <span class="badge bg-warning">Belum Lunas</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid var(--primary-color) !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid var(--success-color) !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid var(--info-color) !important;
    }
    
    .border-left-warning {
        border-left: 0.25rem solid var(--warning-color) !important;
    }
    
    .text-gray-300 {
        color: #dddfeb !important;
    }
    
    .text-gray-800 {
        color: #5a5c69 !important;
    }
    
    .fa-2x {
        font-size: 2rem;
    }
</style>
@endpush
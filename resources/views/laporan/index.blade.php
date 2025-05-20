@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Laporan Apotek Malabar</h1>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-primary mb-3">
                        <i class="bi bi-cash text-white"></i>
                    </div>
                    <h5 class="card-title">Laporan Pembelian Tunai</h5>
                    <p class="card-text">Laporan transaksi pembelian obat secara tunai dari supplier.</p>
                    <a href="{{ route('laporan.pembelian-tunai') }}" class="btn btn-primary">
                        <i class="bi bi-file-earmark-text"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-info mb-3">
                        <i class="bi bi-credit-card text-white"></i>
                    </div>
                    <h5 class="card-title">Laporan Pembelian Kredit</h5>
                    <p class="card-text">Laporan transaksi pembelian obat secara kredit dari supplier.</p>
                    <a href="{{ route('laporan.pembelian-kredit') }}" class="btn btn-info">
                        <i class="bi bi-file-earmark-text"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-warning mb-3">
                        <i class="bi bi-arrow-return-left text-white"></i>
                    </div>
                    <h5 class="card-title">Laporan Retur Pembelian</h5>
                    <p class="card-text">Laporan pengembalian obat ke supplier karena berbagai alasan.</p>
                    <a href="{{ route('laporan.retur-pembelian') }}" class="btn btn-warning">
                        <i class="bi bi-file-earmark-text"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-success mb-3">
                        <i class="bi bi-cart-check text-white"></i>
                    </div>
                    <h5 class="card-title">Laporan Penjualan</h5>
                    <p class="card-text">Laporan transaksi penjualan obat kepada pelanggan.</p>
                    <a href="{{ route('laporan.penjualan') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-text"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-danger mb-3">
                        <i class="bi bi-bar-chart text-white"></i>
                    </div>
                    <h5 class="card-title">Laporan Obat Paling Laku</h5>
                    <p class="card-text">Laporan obat yang paling banyak terjual dalam periode tertentu.</p>
                    <a href="{{ route('laporan.obat-paling-laku') }}" class="btn btn-danger">
                        <i class="bi bi-file-earmark-text"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-secondary mb-3">
                        <i class="bi bi-box-seam text-white"></i>
                    </div>
                    <h5 class="card-title">Laporan Persediaan Obat</h5>
                    <p class="card-text">Laporan stok obat yang tersedia di Apotek Malabar.</p>
                    <a href="{{ route('laporan.persediaan-obat') }}" class="btn btn-secondary">
                        <i class="bi bi-file-earmark-text"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 4rem;
        width: 4rem;
        border-radius: 50%;
    }
    
    .icon-circle i {
        font-size: 2rem;
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush
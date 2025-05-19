@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Laporan Apotek Malabar</h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Laporan Pembelian Tunai</h5>
                                    <p class="card-text">Laporan transaksi pembelian obat secara tunai dari supplier.</p>
                                    <a href="{{ route('laporan.pembelian-tunai') }}" class="btn btn-primary">Lihat Laporan</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Laporan Pembelian Kredit</h5>
                                    <p class="card-text">Laporan transaksi pembelian obat secara kredit dari supplier.</p>
                                    <a href="{{ route('laporan.pembelian-kredit') }}" class="btn btn-primary">Lihat Laporan</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Laporan Retur Pembelian</h5>
                                    <p class="card-text">Laporan pengembalian obat ke supplier karena berbagai alasan.</p>
                                    <a href="{{ route('laporan.retur-pembelian') }}" class="btn btn-primary">Lihat Laporan</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Laporan Penjualan</h5>
                                    <p class="card-text">Laporan transaksi penjualan obat kepada pelanggan.</p>
                                    <a href="{{ route('laporan.penjualan') }}" class="btn btn-primary">Lihat Laporan</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Laporan Obat Paling Laku</h5>
                                    <p class="card-text">Laporan obat yang paling banyak terjual dalam periode tertentu.</p>
                                    <a href="{{ route('laporan.obat-paling-laku') }}" class="btn btn-primary">Lihat Laporan</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Laporan Persediaan Obat</h5>
                                    <p class="card-text">Laporan stok obat yang tersedia di Apotek Malabar.</p>
                                    <a href="{{ route('laporan.persediaan-obat') }}" class="btn btn-primary">Lihat Laporan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
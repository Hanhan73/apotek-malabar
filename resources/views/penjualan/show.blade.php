@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Transaksi Penjualan</h5>
                    <div>
                        @if($penjualan->status_pembayaran == 'belum_dibayar')
                            <a href="{{ route('penjualan.edit', $penjualan->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="fw-bold">Informasi Transaksi</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%">Nomor Nota</td>
                                        <td width="5%">:</td>
                                        <td>{{ $penjualan->nomor_nota }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal</td>
                                        <td>:</td>
                                        <td>{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Penjualan</td>
                                        <td>:</td>
                                        <td>
                                            @if ($penjualan->jenis_penjualan == 'dengan_resep')
                                                <span class="badge bg-info">Dengan Resep</span>
                                            @else
                                                <span class="badge bg-secondary">Tanpa Resep</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Petugas</td>
                                        <td>:</td>
                                        <td>{{ $penjualan->user->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4">Detail Obat</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode Obat</th>
                                    <th>Nama Obat</th>
                                    <th>Harga Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penjualan->details as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->obat->kode_obat }}</td>
                                        <td>{{ $detail->obat->nama_obat }}</td>
                                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Total:</td>
                                    <td>Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print, .no-print * {
            display: none !important;
        }
    }
</style>
@endpush
@endsection
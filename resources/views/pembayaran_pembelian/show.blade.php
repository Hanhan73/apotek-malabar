@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Detail Pembayaran Pembelian</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('pembayaran-pembelian.index') }}">Pembayaran Pembelian</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6>Informasi Pembelian</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">No. Pembelian</td>
                                        <td>: {{ $pembayaran->penerimaanPembelian->pembelian->kode_pembelian }}</td>
                                    </tr>
                                    <tr>
                                        <td>Supplier</td>
                                        <td>: {{ $pembayaran->penerimaanPembelian->pembelian->supplier->nama_supplier }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Harga</td>
                                        <td>: Rp {{ number_format($pembayaran->penerimaanPembelian->pembelian->total, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6>Detail Pembayaran</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">Tanggal Bayar</td>
                                        <td>: {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah Bayar</td>
                                        <td>: Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Metode Pembayaran</td>
                                        <td>: {{ ucfirst($pembayaran->metode_pembayaran) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>
                                            : <span class="badge bg-{{ $pembayaran->status == 'lunas' ? 'success' : 'warning' }}">
                                                {{ ucfirst($pembayaran->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dibuat Oleh</td>
                                        <td>: {{ $pembayaran->user->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detail Barang</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Kode Obat</th>
                                    <th width="30%">Nama Obat</th>
                                    <th width="10%">Jumlah</th>
                                    <th width="15%">Harga Satuan</th>
                                    <th width="15%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembayaran->penerimaanPembelian->pembelian->detailPembelian as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->obat->kode_obat }}</td>
                                    <td>{{ $item->obat->nama_obat }}</td>
                                    <td class="text-center">{{ $item->jumlah }} {{ $item->obat->satuan }}</td>
                                    <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="5" class="text-right">Total</th>
                                    <th class="text-right">Rp {{ number_format($pembayaran->penerimaanPembelian->pembelian->total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            
            @if($pembayaran->penerimaanPembelian->pembayaran->count() > 1)
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Riwayat Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Tanggal Bayar</th>
                                    <th width="20%">Jumlah</th>
                                    <th width="20%">Sisa Hutang</th>
                                    <th width="15%">Status</th>
                                    <th width="25%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembayaran->penerimaanPembelian->pembayaran as $index => $bayar)
                                <tr class="{{ $bayar->id == $pembayaran->id ? 'bg-light' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d/m/Y') }}</td>
                                    <td>Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($bayar->sisa_hutang, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $bayar->status == 'lunas' ? 'success' : 'warning' }}">
                                            {{ ucfirst($bayar->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $bayar->catatan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('pembayaran-pembelian.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <div>
                    <a href="{{ route('pembayaran-pembelian.edit', $pembayaran->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('pembayaran-pembelian.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Pembayaran Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detail Pembayaran Pembelian</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Informasi Pembayaran
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>No. Pembelian:</strong> {{ $pembayaran->penerimaanPembelian->pembelian->kode_pembelian ?? '-' }}</p>
                    <p><strong>Supplier:</strong> {{ $pembayaran->penerimaanPembelian->pembelian->supplier->nama_supplier }}</p>
                    <p><strong>Total Harga:</strong> Rp {{ number_format($pembayaran->penerimaanPembelian->pembelian->total, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tanggal Bayar:</strong> {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</p>
                    <p><strong>Jumlah Bayar:</strong> Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</p>
                    <p><strong>Metode Pembayaran:</strong> {{ ucfirst($pembayaran->metode_pembayaran) }}</p>
                    <p>
                        <strong>Status:</strong> 
                        
                        <strong class="badge badge-{{ $pembayaran->status == 'lunas' ? 'success' : 'warning' }}">
                            {{ ucfirst($pembayaran->status) }}
                        </strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Detail Barang
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayaran->penerimaanPembelian->pembelian->detailPembelian as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->obat->nama_obat }} ({{ $item->obat->kode_obat }})</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Total</th>
                        <th>Rp {{ number_format($pembayaran->penerimaanPembelian->pembelian->total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="text-right">
        <a href="{{ route('pembayaran-pembelian.edit', $pembayaran->id) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('pembayaran-pembelian.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Pembayaran Pembelian</h2>
    <a href="{{ route('pembayaran-pembelian.create') }}" class="btn btn-primary mb-3">Tambah Pembayaran</a>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Pembelian</th>
                        <th>Supplier</th>
                        <th>Tanggal Bayar</th>
                        <th>Jumlah Bayar</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayarans as $key => $pembayaran)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $pembayaran->penerimaanPembelian->pembelian->kode_pembelian ?? '-' }}</td>
                        <td>{{ $pembayaran->penerimaanPembelian->pembelian->supplier->nama_supplier ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($pembayaran->metode_pembayaran) }}</td>
                        <td>
                            <span class="badge badge-{{ $pembayaran->status == 'lunas' ? 'success' : 'warning' }}">
                                {{ ucfirst($pembayaran->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('pembayaran-pembelian.show', $pembayaran->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                                <a href="{{ route('pembayaran-pembelian.edit', $pembayaran->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{ $pembayarans->links() }}
        </div>
    </div>
</div>
@endsection
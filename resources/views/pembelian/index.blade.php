@extends('layouts.app')

@section('title', 'Data Pembelian')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Pembelian Obat</h5>
        <a href="{{ route('pembelian.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Tambah Pembelian
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Supplier</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembelian as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->kode_pembelian }}</td>
                        <td>{{ $item->supplier->nama_supplier }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        <td>
                            @switch($item->status)
                                @case('dipesan') <span class="badge bg-secondary">Dipesan</span> @break
                                @case('dikirim') <span class="badge bg-info">Dikirim</span> @break
                                @case('diterima') <span class="badge bg-success">Diterima</span> @break
                                @case('diretur') <span class="badge bg-warning">Diretur</span> @break
                                @case('lunas') <span class="badge bg-primary">Lunas</span> @break
                            @endswitch
                        </td>
                        <td>
                            <a href="{{ route('pembelian.show', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('pembelian.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="mt-3">
            {{ $pembelian->links() }}
        </div>
    </div>
</div>
@endsection
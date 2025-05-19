@extends('layouts.app')

@section('title', 'Daftar Penerimaan Pembelian')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Daftar Penerimaan Pembelian Obat</h5>
        <a href="{{ route('penerimaan-pembelian.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Tambah Penerimaan
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode Pembelian</th>
                        <th>Supplier</th>
                        <th>Tanggal Penerimaan</th>
                        <th>Diterima Oleh</th>
                        <th>Jumlah Item</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penerimaans as $index => $penerimaan)
                    <tr>
                        <td class="text-center">{{ $penerimaans->firstItem() + $index }}</td>
                        <td>{{ $penerimaan->pembelian->kode_pembelian }}</td>
                        <td>{{ $penerimaan->pembelian->supplier->nama_supplier }}</td>
                        <td>{{ \Carbon\Carbon::parse($penerimaan->tanggal_penerimaan)->format('d/m/Y') }}</td>
                        <td>{{ $penerimaan->user->name }}</td>
                        <td class="text-center">{{ $penerimaan->items->count() }}</td>
                        <td class="text-center">
                            <a href="{{ route('penerimaan-pembelian.show', $penerimaan->id) }}" 
                               class="btn btn-sm btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('penerimaan-pembelian.edit', $penerimaan->id) }}" 
                               class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $penerimaans->links() }}
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Daftar Retur Pembelian')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Daftar Retur Pembelian Obat</h5>
        <a href="{{ route('retur-pembelian.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Tambah Retur
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode Penerimaan</th>
                        <th>Supplier</th>
                        <th>Tanggal Retur</th>
                        <th>Total Retur</th>
                        <th>Jumlah Item</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returs as $index => $retur)
                    <tr>
                        <td class="text-center">{{ $returs->firstItem() + $index }}</td>
                        <td>{{ $retur->penerimaanPembelian->pembelian->kode_pembelian }}</td>
                        <td>{{ $retur->penerimaanPembelian->pembelian->supplier->nama_supplier }}</td>
                        <td>{{\Carbon\Carbon::parse($retur->tanggal_retur)->format('d/m/Y') }}</td>
                        <td class="text-end">Rp {{ number_format($retur->total_retur, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $retur->items->count() }}</td>
                        <td class="text-center">
                            <a href="{{ route('retur-pembelian.show', $retur->id) }}" 
                               class="btn btn-sm btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('retur-pembelian.edit', $retur->id) }}" 
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
            {{ $returs->links() }}
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Detail Retur Pembelian')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Detail Retur Pembelian Obat</h5>
        <div>
            <a href="{{ route('retur-pembelian.edit', $retur->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('retur-pembelian.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="border p-3 rounded bg-light">
                    <h6 class="font-weight-bold text-primary">Informasi Retur</h6>
                    <hr>
                    <div class="row">
                        <div class="col-5">Kode Penerimaan</div>
                        <div class="col-7 font-weight-bold">: {{ $retur->penerimaanPembelian->pembelian->kode_pembelian }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Supplier</div>
                        <div class="col-7">: {{ $retur->penerimaanPembelian->pembelian->supplier->nama_supplier }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Tanggal Retur</div>
                        <div class="col-7">: {{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border p-3 rounded bg-light">
                    <h6 class="font-weight-bold text-primary">Informasi Pembuat</h6>
                    <hr>
                    <div class="row">
                        <div class="col-5">Dibuat Oleh</div>
                        <div class="col-7">: {{ $retur->user->name }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Tanggal Dibuat</div>
                        <div class="col-7">: {{ $retur->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Obat</th>
                        <th width="15%">Kode Obat</th>
                        <th width="10%">Jumlah Diterima</th>
                        <th width="10%">Jumlah Dikembalikan</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="15%">Subtotal</th>
                        <th width="15%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($retur->items as $index => $item)
                    @php
                        $penerimaanItem = $retur->penerimaanPembelian->items->where('obat_id', $item->obat_id)->first();
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->obat->nama_obat }}</td>
                        <td class="text-center">{{ $item->obat->kode_obat }}</td>
                        <td class="text-center">{{ $penerimaanItem ? $penerimaanItem->jumlah_diterima : '-' }}</td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td class="text-end">Rp {{ number_format($penerimaanItem ? $penerimaanItem->harga_satuan : 0, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format(($penerimaanItem ? $penerimaanItem->harga_satuan : 0) * $item->jumlah, 0, ',', '.') }}</td>
                        <td>{{ $item->keterangan }}</td>
                    </tr>
                    @endforeach
                    <tr class="table-active">
                        <td colspan="6" class="text-end font-weight-bold">Total Retur</td>
                        <td class="text-end font-weight-bold">Rp {{ number_format($retur->total_retur, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <h6 class="font-weight-bold">Alasan Retur:</h6>
            <div class="border p-3 rounded bg-light">
                {{ $retur->alasan_retur }}
            </div>
        </div>
    </div>
</div>
@endsection
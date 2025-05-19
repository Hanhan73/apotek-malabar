@extends('layouts.app')

@section('title', 'Detail Penerimaan Pembelian')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Detail Penerimaan Pembelian Obat</h5>
        <div>
            <a href="{{ route('penerimaan-pembelian.edit', $penerimaan->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('penerimaan-pembelian.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="border p-3 rounded bg-light">
                    <h6 class="font-weight-bold text-primary">Informasi Penerimaan</h6>
                    <hr>
                    <div class="row">
                        <div class="col-5">Kode Pembelian</div>
                        <div class="col-7 font-weight-bold">: {{ $penerimaan->pembelian->kode_pembelian }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Supplier</div>
                        <div class="col-7">: {{ $penerimaan->pembelian->supplier->nama_supplier }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Tanggal Penerimaan</div>
                        <div class="col-7">: {{ $penerimaan->tanggal_penerimaan->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border p-3 rounded bg-light">
                    <h6 class="font-weight-bold text-primary">Informasi Pembuat</h6>
                    <hr>
                    <div class="row">
                        <div class="col-5">Diterima Oleh</div>
                        <div class="col-7">: {{ $penerimaan->user->name }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Tanggal Dibuat</div>
                        <div class="col-7">: {{ $penerimaan->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($penerimaan->updated_at != $penerimaan->created_at)
                    <div class="row mt-2">
                        <div class="col-5">Terakhir Diupdate</div>
                        <div class="col-7">: {{ $penerimaan->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
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
                        <th width="10%">Jumlah Dipesan</th>
                        <th width="10%">Jumlah Diterima</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="15%">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penerimaan->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->obat->nama_obat }}</td>
                        <td class="text-center">{{ $item->obat->kode_obat }}</td>
                        <td class="text-center">
                            @php
                                $detailPembelian = $penerimaan->pembelian->detailPembelian
                                    ->where('obat_id', $item->obat_id)->first();
                            @endphp
                            {{ $detailPembelian ? $detailPembelian->jumlah : '-' }}
                        </td>
                        <td class="text-center">{{ $item->jumlah_diterima }}</td>
                        <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->jumlah_diterima * $item->harga_satuan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="table-active">
                        <td colspan="6" class="text-end font-weight-bold">Total Penerimaan</td>
                        <td class="text-end font-weight-bold">
                            Rp {{ number_format($penerimaan->items->sum(function($item) { 
                                return $item->jumlah_diterima * $item->harga_satuan; 
                            }), 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($penerimaan->catatan)
        <div class="mt-3">
            <h6 class="font-weight-bold">Catatan:</h6>
            <div class="border p-3 rounded bg-light">
                {{ $penerimaan->catatan }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
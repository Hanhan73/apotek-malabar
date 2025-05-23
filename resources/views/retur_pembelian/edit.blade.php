@extends('layouts.app')

@section('title', 'Edit Retur Pembelian')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Edit Retur Pembelian Obat</h5>
        <a href="{{ route('retur-pembelian.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('retur-pembelian.update', $retur->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode Pembelian</label>
                    <input type="text" class="form-control" value="{{ $penerimaan->pembelian->kode_pembelian }}" readonly>
                </div>
                <div class="col-md-3">
                    <label for="tanggal_retur" class="form-label">Tanggal Retur*</label>
                    <input type="date" class="form-control @error('tanggal_retur') is-invalid @enderror"
                           id="tanggal_retur" name="tanggal_retur"
                           value="{{ old('tanggal_retur', \Carbon\Carbon::parse($retur->tanggal_retur)->format('Y-m-d')) }}" required>
                    @error('tanggal_retur')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Daftar Obat yang Dikembalikan*</label>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="35%">Nama Obat</th>
                                <th width="15%">Jumlah Diterima</th>
                                <th width="15%">Jumlah Retur</th>
                                <th width="15%">Harga Satuan</th>
                                <th width="20%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($retur->items as $item)
                                @php
                                    $penerimaanItem = $penerimaan->items->where('obat_id', $item->obat_id)->first();
                                @endphp
                                <tr>
                                    <td>
                                        {{ $item->obat->nama_obat }} ({{ $item->obat->kode_obat }})
                                        <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="text-center">
                                        {{ $penerimaanItem ? $penerimaanItem->jumlah_diterima : '-' }}
                                    </td>
                                    <td>
                                        <input type="number" class="form-control"
                                               name="items[{{ $item->id }}][jumlah]"
                                               value="{{ old('items.'.$item->id.'.jumlah', $item->jumlah) }}"
                                               min="1"
                                               max="{{ $penerimaanItem ? $penerimaanItem->jumlah_diterima : '' }}"
                                               required>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($penerimaanItem->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <input type="text" class="form-control"
                                               name="items[{{ $item->id }}][keterangan]"
                                               value="{{ old('items.'.$item->id.'.keterangan', $item->keterangan) }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-3">
                <label for="alasan_retur" class="form-label">Alasan Retur*</label>
                <textarea class="form-control @error('alasan_retur') is-invalid @enderror"
                          id="alasan_retur" name="alasan_retur" rows="3" required>{{ old('alasan_retur', $retur->alasan_retur) }}</textarea>
                @error('alasan_retur')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Retur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

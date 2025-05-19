@extends('layouts.app')

@section('title', 'Edit Penerimaan Pembelian')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Edit Penerimaan Pembelian Obat</h5>
        <a href="{{ route('penerimaan-pembelian.show', $penerimaan->id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('penerimaan-pembelian.update', $penerimaan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode Pembelian</label>
                    <input type="text" class="form-control" 
                           value="{{ $penerimaan->pembelian->kode_pembelian }}" readonly>
                </div>
                
                <div class="col-md-3">
                    <label for="tanggal_penerimaan" class="form-label">Tanggal Penerimaan*</label>
                    <input type="date" class="form-control @error('tanggal_penerimaan') is-invalid @enderror" 
                           id="tanggal_penerimaan" name="tanggal_penerimaan" 
                           value="{{ old('tanggal_penerimaan', $penerimaan->tanggal_penerimaan->format('Y-m-d')) }}" required>
                    @error('tanggal_penerimaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Daftar Obat yang Diterima*</label>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="40%">Nama Obat</th>
                                <th width="15%">Jumlah Dipesan</th>
                                <th width="15%">Jumlah Diterima</th>
                                <th width="15%">Harga Satuan</th>
                                <th width="15%">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penerimaan->items as $item)
                            @php
                                $detailPembelian = $penerimaan->pembelian->detailPembelian
                                    ->where('obat_id', $item->obat_id)->first();
                            @endphp
                            <tr>
                                <td>
                                    {{ $item->obat->nama_obat }} ({{ $item->obat->kode_obat }})
                                    <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                </td>
                                <td class="text-center">{{ $detailPembelian ? $detailPembelian->jumlah : '-' }}</td>
                                <td>
                                    <input type="number" class="form-control" 
                                           name="items[{{ $item->id }}][jumlah_diterima]" 
                                           min="1" max="{{ $detailPembelian ? $detailPembelian->jumlah : '' }}" 
                                           value="{{ old('items.'.$item->id.'.jumlah_diterima', $item->jumlah_diterima) }}" required>
                                </td>
                                <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($item->jumlah_diterima * $item->harga_satuan, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan" rows="3">{{ old('catatan', $penerimaan->catatan) }}</textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
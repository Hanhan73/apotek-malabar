@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Pembayaran Pembelian</h2>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('pembayaran-pembelian.update', $pembayaran->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="penerimaan_pembelian_id">Penerimaan Pembelian</label>
                    <input type="text" class="form-control" value="Pembelian #{{ $pembayaran->penerimaanPembelian->pembelian->no_pembelian ?? $pembayaran->penerimaanPembelian->pembelian->id }}" readonly>
                    <small class="form-text text-muted">Tidak dapat mengubah penerimaan pembelian</small>
                </div>
                
                <div class="form-group">
                    <label for="tanggal_bayar">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control" required 
                           value="{{ old('tanggal_bayar', $pembayaran->tanggal_bayar->format('Y-m-d')) }}">
                </div>
                
                <div class="form-group">
                    <label for="jumlah_bayar">Jumlah Bayar</label>
                    <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control" required min="1"
                           value="{{ old('jumlah_bayar', $pembayaran->jumlah_bayar) }}">
                    <small class="form-text text-muted">Total harga: Rp {{ number_format($pembayaran->penerimaanPembelian->pembelian->total_harga, 0, ',', '.') }}</small>
                </div>
                
                <div class="form-group">
                    <label for="metode_pembayaran">Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                        <option value="tunai" {{ $pembayaran->metode_pembayaran == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="kredit" {{ $pembayaran->metode_pembayaran == 'kredit' ? 'selected' : '' }}>Kredit</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status Pembayaran</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="lunas" {{ $pembayaran->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="belum lunas" {{ $pembayaran->status == 'belum lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('pembayaran-pembelian.show', $pembayaran->id) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
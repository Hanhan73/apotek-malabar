@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Edit Pembayaran Pembelian</h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('pembayaran-pembelian.update', $pembayaran->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="penerimaan_pembelian_id">Penerimaan Pembelian</label>
                                    <input type="text" class="form-control bg-light" 
                                           value="Pembelian #{{ $pembayaran->penerimaanPembelian->pembelian->kode_pembelian }}" readonly>
                                    <small class="form-text text-muted">Supplier: {{ $pembayaran->penerimaanPembelian->pembelian->supplier->nama_supplier }}</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_bayar">Tanggal Bayar*</label>
                                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control" required 
                                           value="{{ old('tanggal_bayar', $pembayaran->tanggal_bayar->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="metode_pembayaran">Metode Pembayaran*</label>
                                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                                        <option value="tunai" {{ $pembayaran->metode_pembayaran == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                        <option value="kredit" {{ $pembayaran->metode_pembayaran == 'kredit' ? 'selected' : '' }}>Kredit</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status Pembayaran*</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="lunas" {{ $pembayaran->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                        <option value="belum lunas" {{ $pembayaran->status == 'belum lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jumlah_bayar">Jumlah Bayar (Rp)*</label>
                                    <input type="text" name="jumlah_bayar" id="jumlah_bayar" class="form-control" required
                                           value="{{ old('jumlah_bayar', number_format($pembayaran->jumlah_bayar, 0, ',', '.')) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Informasi Pembelian</label>
                                    <div class="bg-light p-2 rounded">
                                        <small class="d-block">Total: Rp {{ number_format($pembayaran->penerimaanPembelian->pembelian->total, 0, ',', '.') }}</small>
                                        <small class="d-block">Sisa: Rp {{ number_format($pembayaran->sisa_hutang, 0, ',', '.') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="catatan">Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3">{{ old('catatan', $pembayaran->catatan) }}</textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pembayaran-pembelian.show', $pembayaran->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format input jumlah bayar
    const jumlahBayarInput = document.getElementById('jumlah_bayar');
    
    jumlahBayarInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        this.value = value ? 'Rp ' + parseInt(value).toLocaleString('id-ID') : '';
    });
});
</script>
@endsection
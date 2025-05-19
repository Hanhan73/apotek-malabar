@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Pembayaran Pembelian</h2>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('pembayaran-pembelian.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="penerimaan_pembelian_id">Penerimaan Pembelian</label>
                    <select name="penerimaan_pembelian_id" id="penerimaan_pembelian_id" class="form-control" required>
                        <option value="">Pilih Penerimaan Pembelian</option>
                        @foreach($penerimaans as $penerimaan)
                            <option value="{{ $penerimaan->id }}" 
                                data-total="{{ $penerimaan->pembelian->total_harga }}"
                                data-supplier="{{ $penerimaan->pembelian->supplier->nama_supplier }}">
                                Pembelian #{{ $penerimaan->pembelian->no_pembelian ?? $penerimaan->pembelian->id }} - 
                                {{ $penerimaan->pembelian->supplier->nama_supplier }} - 
                                Rp {{ number_format($penerimaan->pembelian->total, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tanggal_bayar">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control" required 
                           value="{{ old('tanggal_bayar', date('Y-m-d')) }}">
                </div>
                                
                <div class="form-group">
                    <label for="jumlah_bayar">Jumlah Bayar</label>
                    <input type="text" name="jumlah_bayar" id="jumlah_bayar" class="form-control" required>
                    <small class="form-text text-muted">Masukkan jumlah pembayaran dalam Rupiah</small>
                </div>
                
                <div class="form-group">
                    <label for="metode_pembayaran">Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                        <option value="tunai">Tunai</option>
                        <option value="kredit">Kredit</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status Pembayaran</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="lunas">Lunas</option>
                        <option value="belum lunas">Belum Lunas</option>
                    </select>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('pembayaran-pembelian.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const jumlahBayarInput = document.getElementById('jumlah_bayar');
    
    // Format Rupiah saat input
    jumlahBayarInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        this.value = value ? 'Rp ' + parseInt(value).toLocaleString('id-ID') : '';
    });
    
    // Pastikan format dihilangkan saat submit
    form.addEventListener('submit', function() {
        jumlahBayarInput.value = jumlahBayarInput.value.replace(/\D/g, '');
    });
    
    // Set nilai awal jika ada data old
    @if(old('jumlah_bayar'))
        jumlahBayarInput.value = 'Rp {{ number_format(old('jumlah_bayar'), 0, ',', '.') }}';
    @endif
});     
</script>
@endsection

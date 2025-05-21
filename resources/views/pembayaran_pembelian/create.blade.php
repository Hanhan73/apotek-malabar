@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Pembayaran Pembelian</h2>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('pembayaran-pembelian.store') }}" method="POST">
                @csrf
                
<div class="form-group mb-3">
    <label for="penerimaan_pembelian_id">Penerimaan Pembelian*</label>
    <select name="penerimaan_pembelian_id" id="penerimaan_pembelian_id" class="form-control" required>
        <option value="">Pilih Penerimaan Pembelian</option>
        @foreach($penerimaans as $penerimaan)
            <option value="{{ $penerimaan->id }}" 
                data-total="{{ $penerimaan->pembelian->total }}"
                data-terbayar="{{ $penerimaan->pembayaran->sum('jumlah_bayar') ?? 0 }}"
                data-sisa="{{ $penerimaan->pembelian->total - ($penerimaan->pembayaran->sum('jumlah_bayar') ?? 0) }}"
                data-supplier="{{ $penerimaan->pembelian->supplier->nama_supplier }}">
                {{ $penerimaan->pembelian->kode_pembelian }} - 
                {{ $penerimaan->pembelian->supplier->nama_supplier }} - 
                Sisa: Rp {{ number_format($penerimaan->pembelian->total - ($penerimaan->pembayaran->sum('jumlah_bayar') ?? 0), 0, ',', '.') }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group mb-3">
    <label for="tanggal_bayar">Tanggal Bayar*</label>
    <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control" required 
           value="{{ old('tanggal_bayar', date('Y-m-d')) }}">
</div>

<div class="form-group mb-3">
    <label for="jumlah_bayar">Jumlah Bayar*</label>
    <div class="input-group">
        <span class="input-group-text">Rp</span>
        <input type="text" name="jumlah_bayar" id="jumlah_bayar" class="form-control" required>
    </div>
    <small id="sisa-info" class="form-text text-muted">Sisa hutang: Rp 0</small>
</div>

<div class="form-group mb-3">
    <label for="catatan">Catatan</label>
    <textarea name="catatan" id="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const penerimaanSelect = document.getElementById('penerimaan_pembelian_id');
    const sisaInfo = document.getElementById('sisa-info');
    
    penerimaanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const sisa = selectedOption.getAttribute('data-sisa');
            sisaInfo.textContent = 'Sisa hutang: Rp ' + parseInt(sisa).toLocaleString('id-ID');
            
            // Jika ingin mengatur jumlah bayar secara otomatis
            document.getElementById('jumlah_bayar').value = 'Rp ' + parseInt(sisa).toLocaleString('id-ID');
        } else {
            sisaInfo.textContent = 'Sisa hutang: Rp 0';
        }
    });
    
    // Format input jumlah bayar
    const jumlahBayarInput = document.getElementById('jumlah_bayar');
    jumlahBayarInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        this.value = value ? 'Rp ' + parseInt(value).toLocaleString('id-ID') : '';
    });
});
</script>
@endsection

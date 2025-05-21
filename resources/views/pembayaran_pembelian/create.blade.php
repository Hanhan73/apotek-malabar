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
        @php
            // Gunakan optional() untuk menghindari error null
            $totalDibayar = optional($penerimaan->pembayaran)->sum('jumlah_bayar') ?? 0;
            $sisaHutang = $penerimaan->pembelian->total - $totalDibayar;
        @endphp
        @if($sisaHutang > 0)
            <option value="{{ $penerimaan->id }}" 
                data-total="{{ $penerimaan->pembelian->total }}"
                data-terbayar="{{ $totalDibayar }}"
                data-sisa="{{ $sisaHutang }}"
                data-supplier="{{ $penerimaan->pembelian->supplier->nama_supplier }}">
                {{ $penerimaan->pembelian->kode_pembelian }} - 
                {{ $penerimaan->pembelian->supplier->nama_supplier }} - 
                Total: Rp {{ number_format($penerimaan->pembelian->total, 0, ',', '.') }} - 
                Sisa: Rp {{ number_format($sisaHutang, 0, ',', '.') }}
            </option>
        @endif
    @endforeach
</select>
                </div>

                <div class="form-group mb-3">
                    <label for="tanggal_bayar">Tanggal Bayar*</label>
                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control" required 
                           value="{{ old('tanggal_bayar', date('Y-m-d')) }}">
                </div>

                <div class="form-group mb-3">
                    <label for="metode_pembayaran">Metode Pembayaran*</label>
                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                        <option value="tunai">Tunai</option>
                        <option value="kredit">Kredit</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="jumlah_bayar">Jumlah Bayar*</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="jumlah_bayar" id="jumlah_bayar" class="form-control" required
                               value="{{ old('jumlah_bayar') }}">
                    </div>
                    <small id="sisa-info" class="form-text text-muted">Sisa hutang: Rp 0</small>
                </div>

                <div class="form-group mb-3">
                    <label for="catatan">Catatan</label>
                    <textarea name="catatan" id="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
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
    const penerimaanSelect = document.getElementById('penerimaan_pembelian_id');
    const sisaInfo = document.getElementById('sisa-info');
    const jumlahBayarInput = document.getElementById('jumlah_bayar');
    
    penerimaanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const sisa = selectedOption.getAttribute('data-sisa');
            sisaInfo.textContent = 'Sisa hutang: Rp ' + parseInt(sisa).toLocaleString('id-ID');
            
            // Set nilai default jumlah bayar sama dengan sisa hutang
            jumlahBayarInput.value = 'Rp ' + parseInt(sisa).toLocaleString('id-ID');
        } else {
            sisaInfo.textContent = 'Sisa hutang: Rp 0';
            jumlahBayarInput.value = '';
        }
    });
    
    // Format input jumlah bayar
    jumlahBayarInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        this.value = value ? 'Rp ' + parseInt(value).toLocaleString('id-ID') : '';
    });
});
</script>
@endsection
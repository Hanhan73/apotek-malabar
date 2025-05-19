@extends('layouts.app')

@section('title', 'Edit Obat')
@section('content')
<div class="card">
    <div class="card-header">Edit Data Obat</div>
    <div class="card-body">
        <form action="{{ route('obat.update', $obat->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_obat" class="form-label">Nama Obat</label>
                    <input type="text" class="form-control" id="nama_obat" name="nama_obat" value = "{{ $obat->nama_obat }}" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="jenis_obat" class="form-label">Jenis Obat</label>
                    <select class="form-select" id="jenis_obat" name="jenis_obat" required>
                        <option value="">-- Pilih Jenis Obat --</option>
                        <option value="bebas" @selected(old('jenis_obat', $obat->jenis_obat) == 'bebas')>Bebas</option>
                        <option value="herbal" @selected(old('jenis_obat', $obat->jenis_obat) == 'herbal')>Herbal</option>
                        <option value="psikotropik" @selected(old('jenis_obat', $obat->jenis_obat) == 'psikotropik')>Psikotropik</option>
                        <option value="suplemen" @selected(old('jenis_obat', $obat->jenis_obat) == 'suplemen')>Suplemen</option>
                        <option value="bebas_terbatas" @selected(old('jenis_obat', $obat->jenis_obat) == 'bebas_terbatas')>Bebas Terbatas</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" class="form-control" id="stok" name="stok" min="0" value = "{{ $obat->stok }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
                    <input type="date" class="form-control" id="kadaluarsa" name="kadaluarsa" 
                        value="{{ $obat->kadaluarsa ? \Carbon\Carbon::parse($obat->kadaluarsa)->format('Y-m-d') : '' }}" 
                        required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="harga_beli" class="form-label">Harga Beli</label>
                    <input type="number" class="form-control" id="harga_beli" name="harga_beli" value = "{{ $obat->harga_beli }}" min="0" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="harga_jual" class="form-label">Harga Jual</label>
                    <input type="number" class="form-control" id="harga_jual" name="harga_jual" value = "{{ $obat->harga_jual }}" min="0" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('obat.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
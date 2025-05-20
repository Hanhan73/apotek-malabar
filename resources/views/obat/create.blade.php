@extends('layouts.app')

@section('title', 'Tambah Obat')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Tambah Obat Baru</h1>
        <a href="{{ route('obat.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Obat</h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('obat.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_obat" class="form-label">Nama Obat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_obat') is-invalid @enderror" 
                               id="nama_obat" name="nama_obat" value="{{ old('nama_obat') }}" required>
                        @error('nama_obat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <label for="jenis_obat" class="form-label">Jenis Obat <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenis_obat') is-invalid @enderror" 
                                id="jenis_obat" name="jenis_obat" required>
                            <option value="">-- Pilih Jenis Obat --</option>
                            <option value="bebas" {{ old('jenis_obat') == 'bebas' ? 'selected' : '' }}>Bebas</option>
                            <option value="herbal" {{ old('jenis_obat') == 'herbal' ? 'selected' : '' }}>Herbal</option>
                            <option value="psikotropik" {{ old('jenis_obat') == 'psikotropik' ? 'selected' : '' }}>Psikotropik</option>
                            <option value="suplemen" {{ old('jenis_obat') == 'suplemen' ? 'selected' : '' }}>Suplemen</option>
                            <option value="bebas_terbatas" {{ old('jenis_obat') == 'bebas_terbatas' ? 'selected' : '' }}>Bebas Terbatas</option>
                        </select>
                        @error('jenis_obat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('stok') is-invalid @enderror" 
                               id="stok" name="stok" min="0" value="{{ old('stok', 0) }}" required>
                        @error('stok')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('harga_beli') is-invalid @enderror" 
                                   id="harga_beli" name="harga_beli" min="1" value="{{ old('harga_beli') }}" required>
                        </div>
                        @error('harga_beli')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('harga_jual') is-invalid @enderror" 
                                   id="harga_jual" name="harga_jual" min="1" value="{{ old('harga_jual') }}" required>
                        </div>
                        @error('harga_jual')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kadaluarsa" class="form-label">Tanggal Kadaluarsa <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('kadaluarsa') is-invalid @enderror" 
                               id="kadaluarsa" name="kadaluarsa" value="{{ old('kadaluarsa') }}" required>
                        @error('kadaluarsa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="reset" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-danger {
        color: #e74a3b !important;
    }
    
    .form-label {
        font-weight: 600;
    }
</style>
@endpush
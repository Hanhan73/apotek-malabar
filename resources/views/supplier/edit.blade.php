@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Data Supplier</h5>
        <a href="{{ route('supplier.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
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

        <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kode_supplier" class="form-label">Kode Supplier</label>
                    <input type="text" class="form-control" 
                           id="kode_supplier" value="{{ $supplier->kode_supplier }}" readonly>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="nama_supplier" class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_supplier') is-invalid @enderror" 
                           id="nama_supplier" name="nama_supplier" 
                           value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required>
                    @error('nama_supplier')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telepon" class="form-label">Telepon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('telepon') is-invalid @enderror" 
                           id="telepon" name="telepon" 
                           value="{{ old('telepon', $supplier->telepon) }}" required>
                    @error('telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                              id="alamat" name="alamat" rows="3" required>{{ old('alamat', $supplier->alamat) }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="reset" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format phone number input
        const phoneInput = document.getElementById('telepon');
        phoneInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9\-+]/g, '');
        });
    });
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Laporan Persediaan Obat</h5>
                    <div>
                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('laporan.persediaan-obat') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="jenis_obat" class="form-label">Jenis Obat</label>
                                <select class="form-control" id="jenis_obat" name="jenis_obat">
                                    <option value="">Semua Jenis</option>
                                    @foreach($jenisObatList as $jenis)
                                        <option value="{{ $jenis }}" {{ $jenisObat == $jenis ? 'selected' : '' }}>
                                            {{ $jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status_stok" class="form-label">Status Stok</label>
                                <select class="form-control" id="status_stok" name="status_stok">
                                    <option value="semua" {{ $statusStok == 'semua' ? 'selected' : '' }}>Semua</option>
                                    <option value="tersedia" {{ $statusStok == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                    <option value="kosong" {{ $statusStok == 'kosong' ? 'selected' : '' }}>Kosong</option>
                                    <option value="hampir_habis" {{ $statusStok == 'hampir_habis' ? 'selected' : '' }}>Hampir Habis</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="sort_by" class="form-label">Urutkan</label>
                                <select class="form-control" id="sort_by" name="sort_by">
                                    <option value="kode" {{ $sortBy == 'kode' ? 'selected' : '' }}>Kode Obat</option>
                                    <option value="nama" {{ $sortBy == 'nama' ? 'selected' : '' }}>Nama Obat</option>
                                    <option value="stok" {{ $sortBy == 'stok' ? 'selected' : '' }}>Stok</option>
                                    <option value="kadaluarsa" {{ $sortBy == 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="sort_order" class="form-label">Urutan</label>
                                <select class="form-control" id="sort_order" name="sort_order">
                                    <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Naik (A-Z)</option>
                                    <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Turun (Z-A)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('laporan.persediaan-obat') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                        <div class="mt-2 text-end">
                            <button type="submit" name="export" value="pdf" class="btn btn-success" onclick="window.open(this.form.action + '?' + new URLSearchParams(new FormData(this.form)).toString() + '&export=pdf', '_blank'); return false;">
                                <i class="bi bi-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </form>

                    <div class="alert alert-info">
                        <strong>Jenis:</strong> {{ $jenisObat ? $jenisObat : 'Semua' }} | 
                        <strong>Status:</strong> 
                        @if($statusStok == 'semua')
                            Semua
                        @elseif($statusStok == 'tersedia')
                            Tersedia
                        @elseif($statusStok == 'kosong')
                            Kosong
                        @else
                            Hampir Habis
                        @endif
                        | 
                        <strong>Urutan:</strong> 
                        @if($sortBy == 'kode')
                            Kode Obat
                        @elseif($sortBy == 'nama')
                            Nama Obat
                        @elseif($sortBy == 'stok')
                            Stok
                        @else
                            Kadaluarsa
                        @endif
                        ({{ $sortOrder == 'asc' ? 'A-Z' : 'Z-A' }})
                    </div>


                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode Obat</th>
                                    <th>Nama Obat</th>
                                    <th>Jenis</th>
                                    <th>Stok</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Kadaluarsa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($obats as $index => $obat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $obat->kode_obat }}</td>
                                    <td>{{ $obat->nama_obat }}</td>
                                    <td>{{ $obat->jenis_obat }}</td>
                                    <td>{{ $obat->stok }}</td>
                                    <td>Rp {{ number_format($obat->harga_beli, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}</td>
                                    <td>
                                        @if($obat->kadaluarsa)
                                            {{ \Carbon\Carbon::parse($obat->kadaluarsa)->format('d/m/Y') }}
                                            @if(\Carbon\Carbon::parse($obat->kadaluarsa)->isPast())
                                                <span class="badge bg-danger">Kadaluarsa</span>
                                            @elseif(\Carbon\Carbon::parse($obat->kadaluarsa)->diffInMonths(now()) <= 3)
                                                <span class="badge bg-warning">Segera Kadaluarsa</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($obat->stok <= 0)
                                            <span class="badge bg-danger">Kosong</span>
                                        @elseif($obat->stok <= 10)
                                            <span class="badge bg-warning">Hampir Habis</span>
                                        @else
                                            <span class="badge bg-success">Tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data obat</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .btn {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: white !important;
            color: black !important;
        }
        
        .table {
            width: 100% !important;
        }
    }
</style>
@endpush
@endsection
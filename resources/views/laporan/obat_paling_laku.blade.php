@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Laporan Obat Paling Laku</h5>
                    <div>
                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('laporan.obat-paling-laku') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="bulan" class="form-label">Pilih Bulan</label>
                                <input type="month" class="form-control" id="bulan" name="bulan" 
                                    value="{{ request('bulan', date('Y-m')) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="limit" class="form-label">Jumlah Data</label>
                                <select class="form-control" id="limit" name="limit">
                                    <option value="5" {{ $limit == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('laporan.obat-paling-laku') }}" class="btn btn-outline-secondary">Reset</a>
                                <button type="submit" name="export" value="pdf" class="btn btn-success" onclick="window.open(this.form.action + '?' + new URLSearchParams(new FormData(this.form)).toString() + '&export=pdf', '_blank'); return false;">
                                    <i class="bi bi-file-pdf"></i> Export PDF
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-info">
                        <strong>Periode:</strong> Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }} | 
                        <strong>Menampilkan:</strong> {{ $limit }} obat paling laku
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode Obat</th>
                                    <th>Nama Obat</th>
                                    <th>Jenis</th>
                                    <th>Harga Jual</th>
                                    <th>Total Terjual</th>
                                    <th>Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($obatPalingLaku as $index => $obat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $obat->kode_obat }}</td>
                                    <td>{{ $obat->nama_obat }}</td>
                                    <td>{{ $obat->jenis_obat }}</td>
                                    <td>Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}</td>
                                    <td>{{ $obat->total_terjual }}</td>
                                    <td>Rp {{ number_format($obat->total_pendapatan, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data penjualan obat pada periode ini</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
@endsection
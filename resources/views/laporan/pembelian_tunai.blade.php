@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Laporan Pembelian Tunai</h5>
                    <div>
                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                            <form method="GET" action="{{ route('laporan.pembelian-tunai') }}" class="mb-4">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label for="bulan" class="form-label">Pilih Bulan</label>
                                        <input type="month" class="form-control" id="bulan" name="bulan" 
                                            value="{{ request('bulan', date('Y-m')) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <button type="submit" name="export" value="pdf" class="btn btn-success">
                                            <i class="bi bi-file-pdf"></i> Export PDF
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="alert alert-info">
                                <strong>Periode:</strong> Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode Pembelian</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Jumlah Item</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pembelianTunai as $index => $pembelian)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pembelian->kode_pembelian }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</td>
                                    <td>{{ $pembelian->supplier->nama_supplier }}</td>
                                    <td>{{ $pembelian->detailPembelian->count() }}</td>
                                    <td>Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($pembelian->status == 'dipesan')
                                            <span class="badge bg-secondary">Dipesan</span>
                                        @elseif($pembelian->status == 'dikirim')
                                            <span class="badge bg-info">Dikirim</span>
                                        @elseif($pembelian->status == 'diterima')
                                            <span class="badge bg-success">Diterima</span>
                                        @elseif($pembelian->status == 'diretur')
                                            <span class="badge bg-warning">Diretur</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $pembelian->id }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data pembelian tunai pada periode ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Total Pembelian Tunai:</td>
                                    <td colspan="3" class="fw-bold">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail untuk setiap pembelian -->
@foreach ($pembelianTunai as $pembelian)
<div class="modal fade" id="detailModal{{ $pembelian->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $pembelian->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $pembelian->id }}">Detail Pembelian #{{ $pembelian->kode_pembelian }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Supplier:</strong> {{ $pembelian->supplier->nama_supplier }}</p>
                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</p>
                        <p><strong>Status:</strong> 
                            @if($pembelian->status == 'dipesan')
                                <span class="badge bg-secondary">Dipesan</span>
                            @elseif($pembelian->status == 'dikirim')
                                <span class="badge bg-info">Dikirim</span>
                            @elseif($pembelian->status == 'diterima')
                                <span class="badge bg-success">Diterima</span>
                            @elseif($pembelian->status == 'diretur')
                                <span class="badge bg-warning">Diretur</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Kode Pembelian:</strong> {{ $pembelian->kode_pembelian }}</p>
                        <p><strong>Total Harga:</strong> Rp {{ number_format($pembelian->total, 0, ',', '.') }}</p>
                        <p><strong>Dibuat Oleh:</strong> {{ $pembelian->users->name }}</p>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Daftar Obat</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Obat</th>
                                <th>Nama Obat</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembelian->detailPembelian as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->obat->kode_obat }}</td>
                                <td>{{ $detail->obat->nama_obat }}</td>
                                <td>{{ $detail->jumlah }}</td>
                                <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total:</td>
                                <td class="fw-bold">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('styles')
<style>
    @media print {
        .btn, .modal, .modal-backdrop {
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
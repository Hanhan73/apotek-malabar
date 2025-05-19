@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Laporan Retur Pembelian</h5>
                    <div>
                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('laporan.retur-pembelian') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai', $tanggalMulai->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir', $tanggalAkhir->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select class="form-control" id="supplier_id" name="supplier_id">
                                    <option value="">Semua Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $supplierId == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('laporan.retur-pembelian') }}" class="btn btn-outline-secondary">Reset</a>
                                <button type="submit" name="export" value="pdf" class="btn btn-success" onclick="window.open(this.form.action + '?' + new URLSearchParams(new FormData(this.form)).toString() + '&export=pdf', '_blank'); return false;">
                                    <i class="bi bi-file-pdf"></i> Export PDF
                                </button>
                                <button type="button" class="btn btn-info" onclick="window.print()">
                                    <i class="bi bi-printer"></i> Print
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-info">
                        <strong>Periode:</strong> {{ $tanggalMulai->format('d/m/Y') }} - {{ $tanggalAkhir->format('d/m/Y') }} | 
                        <strong>Supplier:</strong> {{ $supplierId ? $suppliers->firstWhere('id', $supplierId)->nama : 'Semua Supplier' }}
                    </div>

                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Total Nilai Retur:</h6>
                            <h4 class="mt-2">Rp {{ number_format($totalNilaiRetur, 0, ',', '.') }}</h4>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Retur</th>
                                    <th>Tanggal</th>
                                    <th>No. Faktur</th>
                                    <th>Supplier</th>
                                    <th>Jumlah Item</th>
                                    <th>Total Nilai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($returPembelian as $index => $retur)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $retur->nomor_retur }}</td>
                                    <td>{{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d/m/Y') }}</td>
                                    <td>{{ $retur->pembelian ? $retur->pembelian->no_faktur : '-' }}</td>
                                    <td>{{ $retur->supplier->nama }}</td>
                                    <td>{{ $retur->details->count() }}</td>
                                    <td>Rp {{ number_format($retur->total_nilai_retur, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $retur->id }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data retur pembelian pada periode ini</td>
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

<!-- Modal Detail untuk setiap retur -->
@foreach ($returPembelian as $retur)
<div class="modal fade" id="detailModal{{ $retur->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $retur->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $retur->id }}">Detail Retur Pembelian #{{ $retur->nomor_retur }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Supplier:</strong> {{ $retur->supplier->nama }}</p>
                        <p><strong>Tanggal Retur:</strong> {{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d/m/Y') }}</p>
                        <p><strong>No. Faktur Pembelian:</strong> {{ $retur->pembelian ? $retur->pembelian->no_faktur : '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>No. Retur:</strong> {{ $retur->nomor_retur }}</p>
                        <p><strong>Total Nilai Retur:</strong> Rp {{ number_format($retur->total_nilai_retur, 0, ',', '.') }}</p>
                        <p><strong>Alasan Retur:</strong> {{ $retur->alasan_retur ?: '-' }}</p>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Daftar Obat Diretur</h6>
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
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($retur->details as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->obat->kode_obat }}</td>
                                <td>{{ $detail->obat->nama_obat }}</td>
                                <td>{{ $detail->jumlah }}</td>
                                <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                <td>{{ $detail->keterangan ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total:</td>
                                <td colspan="2" class="fw-bold">Rp {{ number_format($retur->total_nilai_retur, 0, ',', '.') }}</td>
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
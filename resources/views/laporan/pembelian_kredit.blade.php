@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Laporan Pembelian Kredit</h5>
                    <div>
                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('laporan.pembelian-kredit') }}" class="mb-4">
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
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="semua" {{ $status == 'semua' ? 'selected' : '' }}>Semua</option>
                                    <option value="lunas" {{ $status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                    <option value="belum_lunas" {{ $status == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('laporan.pembelian-kredit') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <strong>Status:</strong> {{ $status == 'semua' ? 'Semua' : ($status == 'lunas' ? 'Lunas' : 'Belum Lunas') }}
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Pembelian Kredit</h6>
                                    <h4 class="mt-2">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Lunas</h6>
                                    <h4 class="mt-2">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Belum Lunas</h6>
                                    <h4 class="mt-2">Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Faktur</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pembelianKredit as $index => $pembelian)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pembelian->no_faktur }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</td>
                                    <td>{{ $pembelian->supplier->nama }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_jatuh_tempo)->format('d/m/Y') }}</td>
                                    <td>Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($pembelian->status_pembayaran == 'lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-warning">Belum Lunas</span>
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
                                    <td colspan="8" class="text-center">Tidak ada data pembelian kredit pada periode ini</td>
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

<!-- Modal Detail untuk setiap pembelian -->
@foreach ($pembelianKredit as $pembelian)
<div class="modal fade" id="detailModal{{ $pembelian->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $pembelian->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $pembelian->id }}">Detail Pembelian Kredit #{{ $pembelian->no_faktur }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Supplier:</strong> {{ $pembelian->supplier->nama }}</p>
                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</p>
                        <p><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($pembelian->tanggal_jatuh_tempo)->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>No. Faktur:</strong> {{ $pembelian->no_faktur }}</p>
                        <p><strong>Total Harga:</strong> Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</p>
                        <p>
                            <strong>Status:</strong> 
                            @if($pembelian->status_pembayaran == 'lunas')
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <span class="badge bg-warning">Belum Lunas</span>
                            @endif
                        </p>
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
                            @foreach ($pembelian->details as $index => $detail)
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
                                <td class="fw-bold">Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($pembelian->status_pembayaran == 'lunas' && $pembelian->pembayaran)
                <h6 class="fw-bold mt-4 mb-3">Informasi Pembayaran</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Tanggal Pembayaran</th>
                            <td>{{ \Carbon\Carbon::parse($pembelian->pembayaran->tanggal_pembayaran)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Dibayar</th>
                            <td>Rp {{ number_format($pembelian->pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Metode Pembayaran</th>
                            <td>{{ ucfirst($pembelian->pembayaran->metode_pembayaran) }}</td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $pembelian->pembayaran->keterangan ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
                @endif
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
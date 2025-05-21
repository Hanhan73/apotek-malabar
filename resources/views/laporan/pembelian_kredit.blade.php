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
                                <label for="bulan" class="form-label">Bulan</label>
                                <input type="month" class="form-control" id="bulan" name="bulan" 
                                       value="{{ request('bulan', date('Y-m')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status Pembayaran</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                    <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('laporan.pembelian-kredit') }}" class="btn btn-outline-secondary">Reset</a>
                                <button type="submit" name="export" value="pdf" class="btn btn-success">
                                    <i class="bi bi-file-pdf"></i> Export PDF
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-info">
                        <strong>Periode:</strong> Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}
                        @if($status != 'semua')
                            | Status: {{ $status == 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                        @endif
                    </div>

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
                                    <th>Status Pembayaran</th>
                                    <th>Jumlah Dibayar</th>
                                    <th>Sisa Hutang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pembelianKredit as $index => $pembelian)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pembelian->kode_pembelian }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</td>
                                    <td>{{ $pembelian->supplier->nama_supplier }}</td>
                                    <td>{{ $pembelian->detailPembelian->count() }}</td>
                                    <td>Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($pembelian->status_pembayaran == 'lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-warning">Belum Lunas</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($pembelian->pembayaran->sum('jumlah')), 0, ',', '.' }}</td>
                                    <td>Rp {{ number_format($pembelian->total - $pembelian->pembayaran->sum('jumlah')), 0, ',', '.' }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $pembelian->id }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data pembelian kredit pada periode ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                                    <td colspan="2" class="text-end fw-bold">Total Lunas:</td>
                                    <td class="fw-bold">Rp {{ number_format($totalLunas, 0, ',', '.') }}</td>
                                    <td class="fw-bold">Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
@foreach ($pembelianKredit as $pembelian)
<div class="modal fade" id="detailModal{{ $pembelian->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $pembelian->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $pembelian->id }}">Detail Pembelian #{{ $pembelian->kode_pembelian }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Isi modal detail -->
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
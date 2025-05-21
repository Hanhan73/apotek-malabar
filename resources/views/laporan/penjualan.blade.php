@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Laporan Penjualan</h5>
                    <div>
                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('laporan.penjualan') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="bulan" class="form-label">Bulan</label>
                                <input type="month" class="form-control" id="bulan" name="bulan" 
                                       value="{{ request('bulan', date('Y-m')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="jenis_penjualan" class="form-label">Jenis Penjualan</label>
                                <select class="form-control" id="jenis_penjualan" name="jenis_penjualan">
                                    <option value="semua" {{ request('jenis_penjualan') == 'semua' ? 'selected' : '' }}>Semua Jenis</option>
                                    <option value="dengan_resep" {{ request('jenis_penjualan') == 'dengan_resep' ? 'selected' : '' }}>Dengan Resep</option>
                                    <option value="tanpa_resep" {{ request('jenis_penjualan') == 'tanpa_resep' ? 'selected' : '' }}>Tanpa Resep</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('laporan.penjualan') }}" class="btn btn-outline-secondary">Reset</a>
                                <button type="submit" name="export" value="pdf" class="btn btn-success">
                                    <i class="bi bi-file-pdf"></i> Export PDF
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-info">
                        <strong>Periode:</strong> Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}
                        @if($jenisPenjualan != 'semua')
                            | Jenis: {{ $jenisPenjualan == 'dengan_resep' ? 'Dengan Resep' : 'Tanpa Resep' }}
                        @endif
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Penjualan</h5>
                                    <p class="card-text">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Dengan Resep</h5>
                                    <p class="card-text">Rp {{ number_format($totalDenganResep, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Tanpa Resep</h5>
                                    <p class="card-text">Rp {{ number_format($totalTanpaResep, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Nota</th>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Jumlah Item</th>
                                    <th>Total Harga</th>
                                    <th>Status Pembayaran</th>
                                    <th>Kasir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penjualan as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->nomor_nota }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($item->jenis_penjualan == 'dengan_resep')
                                            <span class="badge bg-info">Dengan Resep</span>
                                        @else
                                            <span class="badge bg-primary">Tanpa Resep</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->details->count() }}</td>
                                    <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($item->status_pembayaran == 'sudah_dibayar')
                                            <span class="badge bg-success">Sudah Dibayar</span>
                                        @else
                                            <span class="badge bg-warning">Belum Dibayar</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data penjualan pada periode ini</td>
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

<!-- Modal Detail -->
@foreach ($penjualan as $item)
<div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $item->id }}">Detail Penjualan #{{ $item->nomor_nota }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>No. Nota:</strong> {{ $item->nomor_nota }}</p>
                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i') }}</p>
                        <p><strong>Jenis:</strong> 
                            @if($item->jenis_penjualan == 'dengan_resep')
                                Dengan Resep
                            @else
                                Tanpa Resep
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Harga:</strong> Rp {{ number_format($item->total_harga, 0, ',', '.') }}</p>
                        <p><strong>Status Pembayaran:</strong> 
                            @if($item->status_pembayaran == 'sudah_dibayar')
                                <span class="badge bg-success">Sudah Dibayar</span>
                            @else
                                <span class="badge bg-warning">Belum Dibayar</span>
                            @endif
                        </p>
                        <p><strong>Kasir:</strong> {{ $item->user->name }}</p>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Daftar Obat</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                            @foreach ($item->details as $index => $detail)
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
                                <td class="fw-bold">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
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
@endsection
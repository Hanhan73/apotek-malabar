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
                                    <td>Rp {{ number_format($pembelian->total_dibayar, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($pembelian->sisa_hutang, 0, ',', '.') }}</td>
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
                                <td colspan="2" class="text-end fw-bold">Total Dibayar:</td>
                                <td class="fw-bold">Rp {{ number_format($pembelianKredit->sum('total_dibayar'), 0, ',', '.') }}</td>
                                <td class="fw-bold">Rp {{ number_format($pembelianKredit->sum('sisa_hutang'), 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-end fw-bold">Total Lunas:</td>
                                <td colspan="3" class="fw-bold">{{ $pembelianKredit->where('status_pembayaran', 'lunas')->count() }} Transaksi</td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-end fw-bold">Total Belum Lunas:</td>
                                <td colspan="3" class="fw-bold">{{ $pembelianKredit->where('status_pembayaran', '!=', 'lunas')->count() }} Transaksi</td>
                            </tr>
                        </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($pembelianKredit as $pembelian)
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
                            @if($pembelian->status_pembayaran == 'lunas')
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <span class="badge bg-warning">Belum Lunas</span>
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

                <!-- Riwayat Pembayaran -->
                @if($pembelian->pembayaran->count() > 0)
                <div class="mt-4">
                    <h6 class="fw-bold mb-3">Riwayat Pembayaran</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="20%">Tanggal Bayar</th>
                                    <th width="20%">Jumlah Bayar</th>
                                    <th width="15%">Metode</th>
                                    <th width="10%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembelian->pembayaran->sortBy('tanggal_bayar') as $index => $bayar)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d/m/Y') }}</td>
                                    <td class="text-end">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td>{{ $bayar->metode_pembayaran }}</td>
                                    <td class="text-center">
                                        @if($bayar->status == 'lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-warning">Belum Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">Total Dibayar:</td>
                                    <td colspan="3" class="text-end fw-bold">Rp {{ number_format($pembelian->pembayaran->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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


@endsection
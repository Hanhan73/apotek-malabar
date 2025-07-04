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
                                <label for="bulan" class="form-label">Bulan</label>
                                <input type="month" class="form-control" id="bulan" name="bulan" 
                                       value="{{ request('bulan', date('Y-m')) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select class="form-control" id="supplier_id" name="supplier_id">
                                    <option value="">Semua Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" 
                                            {{ $supplierId == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('laporan.retur-pembelian') }}" class="btn btn-outline-secondary">Reset</a>
                                <button type="submit" name="export" value="pdf" class="btn btn-success">
                                    <i class="bi bi-file-pdf"></i> Export PDF
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-info">
                        <strong>Periode:</strong> Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}
                        @if($supplierId)
                            | Supplier: {{ $suppliers->firstWhere('id', $supplierId)->nama_supplier }}
                        @endif
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
                                    <th>Total Nilai Retur</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($returPembelian as $index => $retur)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>RET-{{ str_pad($retur->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d/m/Y') }}</td>
                                    <td>{{ $retur->pembelian->kode_pembelian ?? '-'}}</td>
                                    <td>{{ $retur->pembelian->supplier->nama_supplier ?? '-'}}</td>
                                    <td>{{ $retur->items->count() }}</td>
                                    <td>Rp {{ number_format($retur->total_retur, 0, ',', '.') }}</td>
                                    <td>{{ $retur->user->name }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $retur->id }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data retur pembelian pada periode ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">Total Nilai Retur:</td>
                                    <td colspan="3" class="fw-bold">Rp {{ number_format($totalNilaiRetur, 0, ',', '.') }}</td>
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
@foreach ($returPembelian as $retur)
<div class="modal fade" id="detailModal{{ $retur->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $retur->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $retur->id }}">Detail Retur #RET-{{ str_pad($retur->id, 5, '0', STR_PAD_LEFT) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>No. Faktur:</strong> {{ $retur->pembelian->kode_pembelian ?? '-'}}</p>
                        <p><strong>Supplier:</strong> {{ $retur->pembelian->supplier->nama_supplier ?? '-'}}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Tanggal Retur:</strong> {{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d/m/Y H:i') }}</p>
                        <p><strong>Petugas:</strong> {{ $retur->user->name }}</p>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Daftar Obat Diretur</h6>
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
                                <th>Alasan Retur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($retur->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->obat->kode_obat }}</td>
                                <td>{{ $item->obat->nama_obat }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                <td>{{ $item->alasan_retur }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total Retur:</td>
                                <td colspan="2" class="fw-bold">Rp {{ number_format($retur->total_retur, 0, ',', '.') }}</td>
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
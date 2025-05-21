@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detail Pembayaran Pembelian</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Informasi Pembayaran
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>No. Pembelian:</strong> {{ $pembayaran->penerimaanPembelian->pembelian->kode_pembelian ?? '-' }}</p>
                    <p><strong>Supplier:</strong> {{ $pembayaran->penerimaanPembelian->pembelian->supplier->nama_supplier }}</p>
                    <p><strong>Total Harga:</strong> Rp {{ number_format($pembayaran->penerimaanPembelian->pembelian->total, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tanggal Bayar:</strong> {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</p>
                    <p><strong>Jumlah Bayar:</strong> Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</p>
                    <p><strong>Metode Pembayaran:</strong> {{ ucfirst($pembayaran->metode_pembayaran) }}</p>
                    <p>
                        <strong>Status:</strong> 
                        
                        <strong class="badge badge-{{ $pembayaran->status == 'lunas' ? 'success' : 'warning' }}">
                            {{ ucfirst($pembayaran->status) }}
                        </strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Detail Barang
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayaran->penerimaanPembelian->pembelian->detailPembelian as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->obat->nama_obat }} ({{ $item->obat->kode_obat }})</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Total</th>
                        <th>Rp {{ number_format($pembayaran->penerimaanPembelian->pembelian->total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if($pembelian->jenis_pembayaran == 'kredit')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-warning text-dark">
        <h6 class="m-0 font-weight-bold">Informasi Kredit</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="row mb-2">
                    <div class="col-md-5">Total Pembelian</div>
                    <div class="col-md-7 font-weight-bold">: Rp {{ number_format($pembelian->total, 0, ',', '.') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-5">Total Pembayaran</div>
                    <div class="col-md-7">: Rp {{ number_format($pembelian->total - ($pembelian->sisa_pembayaran ?? $pembelian->total), 0, ',', '.') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-5">Sisa Hutang</div>
                    <div class="col-md-7 font-weight-bold text-{{ ($pembelian->sisa_pembayaran ?? $pembelian->total) > 0 ? 'danger' : 'success' }}">
                        : Rp {{ number_format($pembelian->sisa_pembayaran ?? $pembelian->total, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row mb-2">
                    <div class="col-md-5">Tanggal Pembelian</div>
                    <div class="col-md-7">: {{ $pembelian->tanggal_pembelian->format('d/m/Y') }}</div>
                </div>
                @if($pembelian->tanggal_jatuh_tempo)
                <div class="row mb-2">
                    <div class="col-md-5">Jatuh Tempo</div>
                    <div class="col-md-7">: {{ \Carbon\Carbon::parse($pembelian->tanggal_jatuh_tempo)->format('d/m/Y') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-5">Status</div>
                    <div class="col-md-7">
                        @if(($pembelian->sisa_pembayaran ?? $pembelian->total) <= 0)
                            <span class="badge bg-success">Lunas</span>
                        @elseif($pembelian->tanggal_jatuh_tempo && \Carbon\Carbon::parse($pembelian->tanggal_jatuh_tempo) < now())
                            <span class="badge bg-danger">Telat {{ now()->diffInDays(\Carbon\Carbon::parse($pembelian->tanggal_jatuh_tempo)) }} hari</span>
                        @else
                            <span class="badge bg-warning">Belum Lunas</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@if($pembelian->penerimaan && $pembelian->penerimaan->pembayaran->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-info text-white">
        <h6 class="m-0 font-weight-bold">Riwayat Pembayaran</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Bayar</th>
                        <th>Jumlah</th>
                        <th>Sisa Hutang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembelian->penerimaan->pembayaran as $index => $bayar)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($bayar->sisa_hutang ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $bayar->status == 'lunas' ? 'success' : 'warning' }}">
                                {{ ucfirst($bayar->status) }}
                            </span>
                        </td>
                        <td>{{ $bayar->catatan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
    
    <div class="text-right">
        <a href="{{ route('pembayaran-pembelian.edit', $pembayaran->id) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('pembayaran-pembelian.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
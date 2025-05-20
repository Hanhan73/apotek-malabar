@extends('layouts.pdf')

@section('content')
<div class="container">
    <h3 class="text-center mb-4">Laporan Pembelian Tunai</h3>
    <p class="text-center mb-4">Periode: {{ $tanggalMulai->format('d/m/Y') }} - {{ $tanggalAkhir->format('d/m/Y') }}</p>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Pembelian</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Jumlah Item</th>
                <th>Total Harga</th>
                <th>Status</th>
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
                        Dipesan
                    @elseif($pembelian->status == 'dikirim')
                        Dikirim
                    @elseif($pembelian->status == 'diterima')
                        Diterima
                    @elseif($pembelian->status == 'diretur')
                        Diretur
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data pembelian tunai pada periode ini</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-end fw-bold">Total Pembelian Tunai:</td>
                <td colspan="2" class="fw-bold">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    
    <div class="mt-5">
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4 text-center">
                <p>Bandung, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                <p>Mengetahui,</p>
                <br><br><br>
                <p>(_________________)</p>
                <p>Pemilik Apotek</p>
            </div>
        </div>
    </div>
</div>
@endsection
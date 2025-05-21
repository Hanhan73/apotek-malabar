<!-- resources/views/laporan/pembelian_tunai_pdf.blade.php -->
@extends('layouts.laporan')

@section('title', 'Laporan Pembelian Tunai')
@section('subtitle', 'Periode: ' . $tanggalMulai->format('d/m/Y') . ' - ' . $tanggalAkhir->format('d/m/Y'))

@section('content')
<style>
    body { font-family: Arial, sans-serif; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background-color: #f2f2f2; text-align: left; }
    .text-center { text-align: center; }
    .text-end { text-align: right; }
    .fw-bold { font-weight: bold; }
</style>

<h2 style="text-align: center;">Laporan Pembelian Tunai</h2>
<p style="text-align: center;">Periode: {{ $tanggalMulai->format('d/m/Y') }} - {{ $tanggalAkhir->format('d/m/Y') }}</p>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="15%">Kode Pembelian</th>
                <th width="20%">Supplier</th>
                <th width="15%">Tanggal</th>
                <th width="10%">Jumlah Item</th>
                <th width="15%">Total Harga</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pembelianTunai as $index => $pembelian)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $pembelian->kode_pembelian }}</td>
                <td>{{ $pembelian->supplier->nama_supplier }}</td>
                <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $pembelian->detailPembelian->count() }}</td>
                <td class="text-end">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                <td class="text-center">
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
                <td colspan="2" class="text-end fw-bold">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
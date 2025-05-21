<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Pembelian Kredit</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin-bottom: 5px; }
        .footer { margin-top: 15px; font-size: 10px; text-align: center; }
        .badge { padding: 3px 6px; border-radius: 3px; font-weight: normal; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pembelian Kredit</h2>
        <h3>Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</h3>
        @if($status != 'semua')
        <p>Status: {{ $status == 'lunas' ? 'Lunas' : 'Belum Lunas' }}</p>
        @endif
        <p>Periode: {{ $tanggalMulai->format('d/m/Y') }} - {{ $tanggalAkhir->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="12%">Kode Pembelian</th>
                <th width="10%">Tanggal</th>
                <th width="15%">Supplier</th>
                <th width="8%">Jumlah Item</th>
                <th width="12%">Total Harga</th>
                <th width="10%">Status</th>
                <th width="12%">Jumlah Dibayar</th>
                <th width="12%">Sisa Hutang</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pembelianKredit as $index => $pembelian)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $pembelian->kode_pembelian }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</td>
                <td>{{ $pembelian->supplier->nama_supplier }}</td>
                <td class="text-center">{{ $pembelian->detailPembelian->count() }}</td>
                <td class="text-right">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($pembelian->status_pembayaran == 'lunas')
                        <span class="badge badge-success">Lunas</span>
                    @else
                        <span class="badge badge-warning">Belum Lunas</span>
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($pembelian->pembayaran->sum('jumlah')), 0, ',', '.' }}</td>
                <td class="text-right">Rp {{ number_format($pembelian->total - $pembelian->pembayaran->sum('jumlah')), 0, ',', '.' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data pembelian kredit pada periode ini</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right fw-bold">Total:</td>
                <td class="text-right fw-bold">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                <td colspan="2" class="text-right fw-bold">Total Lunas:</td>
                <td class="text-right fw-bold">Rp {{ number_format($totalLunas, 0, ',', '.') }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
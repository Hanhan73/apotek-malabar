<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Pembelian Tunai</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { margin-bottom: 20px; text-align: center; }
        .footer { margin-top: 20px; font-size: 0.8em; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pembelian Tunai</h2>
        <p>Periode: {{ $tanggalMulai->format('d/m/Y') }} - {{ $tanggalAkhir->format('d/m/Y') }}</p>
    </div>

    <table>
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
                <td class="text-right">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
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
    </table>
</body>
</html>
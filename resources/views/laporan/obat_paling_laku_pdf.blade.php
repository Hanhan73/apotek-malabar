<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Obat Paling Laku</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 1px solid #000; }
        .footer { margin-top: 20px; text-align: center; font-size: 8pt; }
        .highlight { background-color: #fff3cd; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN OBAT PALING LAKU</h1>
        <h2>Top {{ $limit }} Obat Terlaris</h2>
        <p>Periode: {{ $tanggalMulai->format('d/m/Y') }} - {{ $tanggalAkhir->format('d/m/Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Obat</th>
                <th width="25%">Nama Obat</th>
                <th width="15%">Jenis</th>
                <th width="10%">Harga Jual</th>
                <th width="15%">Total Terjual</th>
                <th width="15%">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($obatPalingLaku as $index => $obat)
            <tr @if($index < 3) class="highlight" @endif>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $obat->kode_obat }}</td>
                <td>{{ $obat->nama_obat }}</td>
                <td>{{ $obat->jenis_obat }}</td>
                <td class="text-right">Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}</td>
                <td class="text-center">{{ $obat->total_terjual }}</td>
                <td class="text-right">Rp {{ number_format($obat->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Total:</strong></td>
                <td class="text-center"><strong>{{ $totalPenjualan }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
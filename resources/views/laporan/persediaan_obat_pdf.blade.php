<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Persediaan Obat</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 1px solid #000; }
        .summary { margin-bottom: 15px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { border: none; padding: 3px; }
        .danger { background-color: #ffdddd; }
        .warning { background-color: #fff3cd; }
        .success { background-color: #d4edda; }
        .small-text { font-size: 8pt; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PERSEDIAAN OBAT</h1>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
        
        @if($jenisObat || $statusStok != 'semua')
        <p>
            Filter: 
            @if($jenisObat) Jenis Obat: {{ $jenisObat }} | @endif
            @if($statusStok != 'semua') Status Stok: {{ ucfirst(str_replace('_', ' ', $statusStok)) }} @endif
        </p>
        @endif
    </div>

    <div class="summary">
        <table class="summary-table">
            <tr>
                <td><strong>Total Obat:</strong> {{ $totalObat }}</td>
                <td><strong>Total Stok:</strong> {{ $totalStok }}</td>
                <td><strong>Total Nilai Persediaan:</strong> Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Tersedia:</strong> {{ $obatTersedia }}</td>
                <td><strong>Hampir Habis:</strong> {{ $obatHampirHabis }}</td>
                <td><strong>Kosong:</strong> {{ $obatKosong }}</td>
            </tr>
            <tr>
                <td colspan="3"><strong>Obat Kadaluarsa:</strong> {{ $obatKadaluarsa }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Kode Obat</th>
                <th width="20%">Nama Obat</th>
                <th width="10%">Jenis</th>
                <th width="8%">Stok</th>
                <th width="12%">Harga Beli</th>
                <th width="12%">Harga Jual</th>
                <th width="13%">Kadaluarsa</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($obats as $index => $obat)
            @php
                $rowClass = '';
                if ($obat->stok <= 0) {
                    $rowClass = 'danger';
                } elseif ($obat->kadaluarsa < now()) {
                    $rowClass = 'danger';
                } elseif ($obat->stok <= 10) {
                    $rowClass = 'warning';
                }
            @endphp
            <tr class="{{ $rowClass }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $obat->kode_obat }}</td>
                <td>{{ $obat->nama_obat }}</td>
                <td>{{ $obat->jenis_obat }}</td>
                <td class="text-center">{{ $obat->stok }}</td>
                <td class="text-right">Rp {{ number_format($obat->harga_beli, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($obat->kadaluarsa)->format('d/m/Y') }}</td>
                <td class="text-center">
                    @if($obat->kadaluarsa < now())
                        Kadaluarsa
                    @elseif($obat->stok <= 0)
                        Kosong
                    @elseif($obat->stok <= 10)
                        Hampir Habis
                    @else
                        Tersedia
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 8pt;">
        <p><strong>Keterangan:</strong></p>
        <p>- <span style="background-color: #ffdddd;">Warna merah</span>: Obat kosong atau sudah kadaluarsa</p>
        <p>- <span style="background-color: #fff3cd;">Warna kuning</span>: Stok hampir habis (≤10)</p>
    </div>
</body>
</html>
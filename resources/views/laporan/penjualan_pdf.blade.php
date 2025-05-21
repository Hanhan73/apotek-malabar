<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Penjualan</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px;
            line-height: 1.4;
        }
        .container {
            width: 100%;
            padding: 10px;
        }
        .text-center { 
            text-align: center; 
        }
        .text-right { 
            text-align: right; 
        }
        .mb-4 {
            margin-bottom: 1rem;
        }
        .mt-5 {
            margin-top: 3rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .col-md-4 {
            width: 33.33%;
        }
        .col-md-8 {
            width: 66.66%;
        }
        .fw-bold {
            font-weight: bold;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: normal;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-center mb-4">Laporan Penjualan</h3>
        <p class="text-center mb-4">Periode: {{ $tanggalMulai->format('d/m/Y') }} - {{ $tanggalAkhir->format('d/m/Y') }}</p>
        
        @if($jenisPenjualan != 'semua')
        <div class="text-center mb-4">
            <strong>Jenis Penjualan:</strong> 
            @if($jenisPenjualan == 'dengan_resep')
                Dengan Resep
            @else
                Tanpa Resep
            @endif
        </div>
        @endif
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div>
                    <strong>Total Penjualan:</strong> Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                </div>
            </div>
            <div class="col-md-4">
                <div>
                    <strong>Dengan Resep:</strong> Rp {{ number_format($totalDenganResep, 0, ',', '.') }}
                </div>
            </div>
            <div class="col-md-4">
                <div>
                    <strong>Tanpa Resep:</strong> Rp {{ number_format($totalTanpaResep, 0, ',', '.') }}
                </div>
            </div>
        </div>
        
        <table class="table table-bordered">
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
                            <span class="badge badge-info">Dengan Resep</span>
                        @else
                            <span class="badge badge-primary">Tanpa Resep</span>
                        @endif
                    </td>
                    <td>{{ $item->details->count() }}</td>
                    <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    <td>
                        @if($item->status_pembayaran == 'sudah_dibayar')
                            <span class="badge badge-success">Sudah Dibayar</span>
                        @else
                            <span class="badge badge-warning">Belum Dibayar</span>
                        @endif
                    </td>
                    <td>{{ $item->user->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data penjualan pada periode ini</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end fw-bold">Total Penjualan:</td>
                    <td colspan="3" class="fw-bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        
        @if($penjualan->isNotEmpty())
        <h4 class="text-center mt-4">Detail Item Penjualan</h4>
        
        @foreach ($penjualan as $item)
        <div style="margin-bottom: 15px;">
            <p><strong>No. Nota: {{ $item->nomor_nota }}</strong> | 
               <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i') }} | 
               <strong>Total:</strong> Rp {{ number_format($item->total_harga, 0, ',', '.') }}</p>
            
            <table style="width: 100%; margin-top: 5px;">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="15%">Kode Obat</th>
                        <th width="25%">Nama Obat</th>
                        <th width="10%">Jumlah</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="15%">Subtotal</th>
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
        @endforeach
        @endif
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Pembelian Tunai</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
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
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
        }
        .footer {
            margin-top: 20px;
            font-size: 8pt;
            text-align: center;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: normal;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
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
        <div class="header">
            <h2>LAPORAN PEMBELIAN TUNAI</h2>
            <h3>Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</h3>
            @if($supplierId)
            <p>Supplier: {{ $suppliers->firstWhere('id', $supplierId)->nama_supplier }}</p>
            @endif
            <p>Periode: {{ $tanggalMulai->format('d/m/Y') }} - {{ $tanggalAkhir->format('d/m/Y') }}</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div>
                    <strong>Total Pembelian Tunai:</strong> Rp {{ number_format($totalPembelian, 0, ',', '.') }}
                </div>
            </div>
            <div class="col-md-6">
                <div>
                    <strong>Total Transaksi:</strong> {{ $pembelianTunai->count() }}
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="12%">Kode Pembelian</th>
                    <th width="15%">Tanggal</th>
                    <th width="18%">Supplier</th>
                    <th width="8%">Jumlah Item</th>
                    <th width="12%">Total Harga</th>
                    <th width="10%">Status</th>
                    <th width="15%">Dibuat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pembelianTunai as $index => $pembelian)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $pembelian->kode_pembelian }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</td>
                    <td>{{ $pembelian->supplier->nama_supplier }}</td>
                    <td class="text-center">{{ $pembelian->detailPembelian->count() }}</td>
                    <td class="text-right">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($pembelian->status == 'dipesan')
                            <span class="badge badge-secondary">Dipesan</span>
                        @elseif($pembelian->status == 'dikirim')
                            <span class="badge badge-info">Dikirim</span>
                        @elseif($pembelian->status == 'diterima')
                            <span class="badge badge-success">Diterima</span>
                        @elseif($pembelian->status == 'diretur')
                            <span class="badge badge-warning">Diretur</span>
                        @endif
                    </td>
                    <td>{{ $pembelian->user->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pembelian tunai pada periode ini</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right fw-bold">Total Pembelian Tunai:</td>
                    <td colspan="3" class="fw-bold">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        @if($pembelianTunai->isNotEmpty())
        <h4 class="text-center mt-4">Detail Item Pembelian</h4>
        
        @foreach ($pembelianTunai as $pembelian)
        <div style="margin-bottom: 15px;">
            <p><strong>Kode Pembelian: {{ $pembelian->kode_pembelian }}</strong> | 
               <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }} | 
               <strong>Supplier:</strong> {{ $pembelian->supplier->nama_supplier }}</p>
            
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
                    @foreach ($pembelian->detailPembelian as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->obat->kode_obat }}</td>
                        <td>{{ $detail->obat->nama_obat }}</td>
                        <td class="text-center">{{ $detail->jumlah }}</td>
                        <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right fw-bold">Total:</td>
                        <td class="fw-bold">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endforeach
        @endif
    </div>
</body>
</html>
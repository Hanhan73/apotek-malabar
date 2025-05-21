<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Retur Pembelian</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-center mb-4">Laporan Retur Pembelian</h3>
        <p class="text-center mb-4">Periode: {{ $tanggalMulai->format('d/m/Y') }} - {{ $tanggalAkhir->format('d/m/Y') }}</p>
        
        @if($supplierId)
        <div class="text-center mb-4">
            <strong>Supplier:</strong> {{ $suppliers->firstWhere('id', $supplierId)->nama_supplier }}
        </div>
        @endif
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div>
                    <strong>Total Nilai Retur:</strong> Rp {{ number_format($totalNilaiRetur, 0, ',', '.') }}
                </div>
            </div>
            <div class="col-md-4">
                <div>
                    <strong>Total Transaksi Retur:</strong> {{ $returPembelian->count() }}
                </div>
            </div>
            <div class="col-md-4">
                <div>
                    <strong>Total Item Diretur:</strong> {{ $returPembelian->sum(function($retur) { return $retur->items->count(); }) }}
                </div>
            </div>
        </div>
        
        <table class="table table-bordered">
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
                </tr>
            </thead>
            <tbody>
                @forelse ($returPembelian as $index => $retur)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>RET-{{ str_pad($retur->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d/m/Y') }}</td>
                    <td>{{ $retur->penerimaanPembelian->pembelian->kode_pembelian }}</td>
                    <td>{{ $retur->penerimaanPembelian->pembelian->supplier->nama_supplier }}</td>
                    <td>{{ $retur->items->count() }}</td>
                    <td>Rp {{ number_format($retur->total_retur, 0, ',', '.') }}</td>
                    <td>{{ $retur->user->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data retur pembelian pada periode ini</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end fw-bold">Total Nilai Retur:</td>
                    <td colspan="3" class="fw-bold">Rp {{ number_format($totalNilaiRetur, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        
        @if($returPembelian->isNotEmpty())
        <h4 class="text-center mt-4">Detail Item Diretur</h4>
        
        @foreach ($returPembelian as $retur)
        <div style="margin-bottom: 15px;">
            <p><strong>No. Retur: RET-{{ str_pad($retur->id, 5, '0', STR_PAD_LEFT) }}</strong> | 
               <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d/m/Y') }} | 
               <strong>No. Faktur:</strong> {{ $retur->penerimaanPembelian->pembelian->kode_pembelian }}</p>
            
            <table style="width: 100%; margin-top: 5px;">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="15%">Kode Obat</th>
                        <th width="25%">Nama Obat</th>
                        <th width="10%">Jumlah</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="15%">Subtotal</th>
                        <th width="15%">Alasan Retur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($retur->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->obat->kode_obat }}</td>
                        <td>{{ $item->obat->nama_obat }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>Rp {{ number_format($item->obat->harga_beli, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->jumlah * $item->obat->harga_beli, 0, ',', '.') }}</td>
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
        @endforeach
        @endif
</body>
</html>
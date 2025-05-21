@extends('layouts.app')

@section('title', 'Detail Pembelian Obat')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Detail Pembelian Obat</h5>
        <div>
            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="border p-3 rounded bg-light">
                    <h6 class="font-weight-bold text-primary">Informasi Pembelian</h6>
                    <hr>
                    <div class="row">
                        <div class="col-5">Kode Pembelian</div>
                        <div class="col-7 font-weight-bold">: {{ $pembelian->kode_pembelian }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Tanggal Pembelian</div>
                        <div class="col-7">: {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d F Y') }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Supplier</div>
                        <div class="col-7">: {{ $pembelian->supplier->nama_supplier }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Jenis Pembayaran</div>
                        <div class="col-7">: {{ ucfirst($pembelian->jenis_pembayaran) }}</div>
                    </div>
                </div>


            </div>
            <div class="col-md-6">
                <div class="border p-3 rounded bg-light">
                    <h6 class="font-weight-bold text-primary">Status & Pembuat</h6>
                    <hr>
                    <div class="row">
                        <div class="col-5">Status Pembelian</div>
                        <div class="col-7">
                            : <span class="badge bg-{{ $pembelian->status == 'diterima' ? 'success' : ($pembelian->status == 'ditolak' ? 'danger' : 'warning') }}">
                                {{ ucfirst($pembelian->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Dibuat Oleh</div>
                        <div class="col-7">: {{ $pembelian->users->name }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-5">Tanggal Dibuat</div>
                        <div class="col-7">: {{ $pembelian->created_at->translatedFormat('d F Y H:i') }}</div>
                    </div>
                    @if($pembelian->updated_at != $pembelian->created_at)
                    <div class="row mt-2">
                        <div class="col-5">Terakhir Diupdate</div>
                        <div class="col-7">: {{ $pembelian->updated_at->translatedFormat('d F Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Obat</th>
                        <th width="15%">Kode Obat</th>
                        <th width="10%">Jumlah</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="15%">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembelian->detailPembelian as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->obat->nama_obat }}</td>
                        <td class="text-center">{{ $item->obat->kode_obat }}</td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="table-active">
                        <td colspan="5" class="text-end font-weight-bold">Total Pembelian</td>
                        <td class="text-end font-weight-bold">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($pembelian->keterangan)
        <div class="mt-3">
            <h6 class="font-weight-bold">Keterangan:</h6>
            <div class="border p-3 rounded bg-light">
                {{ $pembelian->keterangan }}
            </div>
        </div>
        @endif
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
                                    <div class="col-md-7">: {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</div>
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
</div>

<!-- Modal Penerimaan -->
<div class="modal fade" id="modalPenerimaan" tabindex="-1" role="dialog" aria-hidden="true">
    <!-- Modal content akan diisi via AJAX -->
</div>

<!-- Modal Pembayaran -->
<div class="modal fade" id="modalPembayaran" tabindex="-1" role="dialog" aria-hidden="true">
    <!-- Modal content akan diisi via AJAX -->
</div>

<!-- Modal Retur -->
<div class="modal fade" id="modalRetur" tabindex="-1" role="dialog" aria-hidden="true">
    <!-- Modal content akan diisi via AJAX -->
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Tombol Penerimaan
    $('.btn-penerimaan').click(function() {
        const id = $(this).data('id');
        $('#modalPenerimaan').load(`/pembelian/${id}/penerimaan`, function() {
            $(this).modal('show');
        });
    });

    // Tombol Pembayaran
    $('.btn-pembayaran').click(function() {
        const id = $(this).data('id');
        $('#modalPembayaran').load(`/pembelian/${id}/pembayaran`, function() {
            $(this).modal('show');
        });
    });

    // Tombol Retur
    $('.btn-retur').click(function() {
        const id = $(this).data('id');
        $('#modalRetur').load(`/pembelian/${id}/retur`, function() {
            $(this).modal('show');
        });
    });

    // Tombol Tolak
    $('.btn-tolak').click(function() {
        if(confirm('Apakah Anda yakin ingin menolak pembelian ini?')) {
            const id = $(this).data('id');
            $.ajax({
                url: `/pembelian/${id}/tolak`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT'
                },
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.responseText);
                }
            });
        }
    });
});
</script>
@endsection
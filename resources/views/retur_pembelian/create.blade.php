@extends('layouts.app')

@section('title', 'Tambah Retur Pembelian')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Form Retur Pembelian Obat</h5>
        <a href="{{ route('retur-pembelian.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('retur-pembelian.store') }}" method="POST">
            @csrf

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="penerimaan_pembelian_id" class="form-label">Penerimaan*</label>
                    <select class="form-select @error('penerimaan_pembelian_id') is-invalid @enderror" 
                            id="penerimaan_pembelian_id" name="penerimaan_pembelian_id" required>
                        <option value="">-- Pilih Penerimaan --</option>
                        @foreach($penerimaans as $penerimaan)
                            <option value="{{ $penerimaan->id }}" 
                                {{ old('penerimaan_pembelian_id') == $penerimaan->id ? 'selected' : '' }}>
                                {{ $penerimaan->pembelian->kode_pembelian }} - {{ $penerimaan->pembelian->supplier->nama_supplier }}
                                ({{ $penerimaan->tanggal_penerimaan->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('penerimaan_pembelian_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label for="tanggal_retur" class="form-label">Tanggal Retur*</label>
                    <input type="date" class="form-control @error('tanggal_retur') is-invalid @enderror" 
                           id="tanggal_retur" name="tanggal_retur" 
                           value="{{ old('tanggal_retur', date('Y-m-d')) }}" required>
                    @error('tanggal_retur')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Daftar Obat yang Dikembalikan*</label>
                <div class="table-responsive">
                    <table class="table table-bordered" id="obat-table">
                        <thead class="table-light">
                            <tr>
                                <th width="40%">Nama Obat</th>
                                <th width="15%">Jumlah Diterima</th>
                                <th width="15%">Jumlah Dikembalikan</th>
                                <th width="20%">Harga Satuan</th>
                                <th width="10%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <tr>
                                <td colspan="5" class="text-center">Pilih penerimaan terlebih dahulu</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-3">
                <label for="alasan_retur" class="form-label">Alasan Retur*</label>
                <textarea class="form-control @error('alasan_retur') is-invalid @enderror" 
                          id="alasan_retur" name="alasan_retur" rows="3" required>{{ old('alasan_retur') }}</textarea>
                @error('alasan_retur')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Retur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const penerimaanSelect = document.getElementById('penerimaan_pembelian_id');
    const itemsContainer = document.getElementById('items-container');
    
    // Data penerimaan dari controller
    const penerimaanData = {
        @foreach($penerimaans as $penerimaan)
        "{{ $penerimaan->id }}": {
            items: [
                @foreach($penerimaan->items as $item)
                {
                    id: {{ $item->id }},
                    obat_id: {{ $item->obat->id }},
                    nama_lengkap: "{{ $item->obat->nama_obat }} ({{ $item->obat->kode_obat }})",
                    jumlah_diterima: {{ $item->jumlah_diterima }},
                    harga_satuan: {{ $item->harga_satuan }},
                    subtotal: {{ $item->harga_satuan * $item->jumlah_diterima }}
                },
                @endforeach
            ]
        },
        @endforeach
    };

    penerimaanSelect.addEventListener('change', function() {
        const penerimaanId = this.value;
        
        if (!penerimaanId) {
            itemsContainer.innerHTML = '<tr><td colspan="5" class="text-center">Pilih penerimaan terlebih dahulu</td></tr>';
            return;
        }
        
        const selectedPenerimaan = penerimaanData[penerimaanId];
        
        if (!selectedPenerimaan || !selectedPenerimaan.items) {
            itemsContainer.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Data penerimaan tidak valid</td></tr>';
            return;
        }
        
        let html = '';
        let adaObat = false;
        
        selectedPenerimaan.items.forEach(item => {
            adaObat = true;
            html += `
            <tr>
                <td>
                    ${item.nama_lengkap}
                    <input type="hidden" name="items[${item.obat_id}][obat_id]" value="${item.obat_id}">
                </td>
                <td class="text-center">${item.jumlah_diterima}</td>
                <td>
                    <input type="number" class="form-control" 
                           name="items[${item.obat_id}][jumlah]" 
                           min="1" max="${item.jumlah_diterima}" 
                           value="1" required>
                </td>
                <td class="text-end">Rp ${formatRupiah(item.harga_satuan)}</td>
                <td>
                    <input type="text" class="form-control" 
                           name="items[${item.obat_id}][keterangan]">
                </td>
            </tr>
            `;
        });
        
        itemsContainer.innerHTML = adaObat ? html : `
            <tr>
                <td colspan="5" class="text-center text-danger">
                    Tidak ada data obat yang valid untuk penerimaan ini
                </td>
            </tr>
        `;
    });
    
    function formatRupiah(angka) {
        if (!angka) return '0';
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
});
</script>
@endsection
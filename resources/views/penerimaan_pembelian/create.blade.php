@extends('layouts.app')

@section('title', 'Tambah Penerimaan Pembelian')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Form Penerimaan Pembelian Obat</h5>
        <a href="{{ route('penerimaan-pembelian.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <form action="{{ route('penerimaan-pembelian.store') }}" method="POST" id="penerimaanForm">
            @csrf

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="pembelian_id" class="form-label">Pembelian*</label>
                    <select class="form-select @error('pembelian_id') is-invalid @enderror" 
                            id="pembelian_id" name="pembelian_id" required>
                        <option value="">-- Pilih Pembelian --</option>
                        @foreach($pembelian as $p)
                            <option value="{{ $p->id }}" 
                                {{ old('pembelian_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->kode_pembelian }} - {{ $p->supplier->nama_supplier }}
                                ({{ \Carbon\Carbon::parse($p->p)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('pembelian_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label for="tanggal_penerimaan" class="form-label">Tanggal Penerimaan*</label>
                    <input type="date" class="form-control @error('tanggal_penerimaan') is-invalid @enderror" 
                           id="tanggal_penerimaan" name="tanggal_penerimaan" 
                           value="{{ old('tanggal_penerimaan', date('Y-m-d')) }}" required>
                    @error('tanggal_penerimaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Daftar Obat yang Diterima*</label>
                @error('items')
                    <div class="text-danger mb-2">{{ $message }}</div>
                @enderror
                <div class="table-responsive">
                    <table class="table table-bordered" id="obat-table">
                        <thead class="table-light">
                            <tr>
                                <th width="40%">Nama Obat</th>
                                <th width="15%">Jumlah Dipesan</th>
                                <th width="15%">Jumlah Diterima</th>
                                <th width="20%">Harga Satuan</th>
                                <th width="10%">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <!-- Akan diisi via JavaScript -->
                            <tr>
                                <td colspan="5" class="text-center">Pilih pembelian terlebih dahulu</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="bi bi-save"></i> Simpan Penerimaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const pembelianSelect = document.getElementById('pembelian_id');
    const itemsContainer = document.getElementById('items-container');
    const penerimaanForm = document.getElementById('penerimaanForm');
    
    // Build the data structure directly in JavaScript
    const pembelianData = {
        @foreach($pembelian as $pembelian)
        "{{ $pembelian->id }}": {
            detailPembelian: [
                @foreach($pembelian->detailPembelian as $detail)
                {
                    id: {{ $detail->id }},
                    jumlah: {{ $detail->jumlah }},
                    harga_satuan: {{ $detail->harga_satuan }},
                    subtotal: {{ $detail->subtotal }},
                    obat: @if($detail->obat) {
                        id: {{ $detail->obat->id }},
                        nama_obat: "{{ addslashes($detail->obat->nama_obat) }}",
                        kode_obat: "{{ addslashes($detail->obat->kode_obat) }}",
                        nama_lengkap: "{{ addslashes($detail->obat->nama_obat) }} ({{ addslashes($detail->obat->kode_obat) }})"
                    } @else null @endif
                }@if(!$loop->last),@endif
                @endforeach
            ]
        }@if(!$loop->last),@endif
        @endforeach
    };

    // Check if there's a previously selected value
    if (pembelianSelect.value) {
        loadPembelianItems(pembelianSelect.value);
    }

    pembelianSelect.addEventListener('change', function() {
        const pembelianId = this.value;
        loadPembelianItems(pembelianId);
    });
    
    function loadPembelianItems(pembelianId) {
        if (!pembelianId) {
            itemsContainer.innerHTML = '<tr><td colspan="5" class="text-center">Pilih pembelian terlebih dahulu</td></tr>';
            return;
        }
        
        const selectedPembelian = pembelianData[pembelianId];
        
        if (!selectedPembelian || !selectedPembelian.detailPembelian) {
            itemsContainer.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Data pembelian tidak valid</td></tr>';
            return;
        }
        
        let html = '';
        let adaObat = false;
        
        selectedPembelian.detailPembelian.forEach(item => {
            if (!item.obat) {
                console.warn('Data obat tidak ditemukan untuk item:', item);
                return;
            }
            
            adaObat = true;
            
            // Get old values if they exist
            const oldJumlahDiterima = "{{ old('items." + item.obat.id + ".jumlah_diterima') }}";
            const jumlahDiterima = oldJumlahDiterima ? oldJumlahDiterima : item.jumlah;
            
            html += `
            <tr>
                <td>
                    ${item.obat.nama_lengkap}
                    <input type="hidden" name="items[${item.id}][obat_id]" value="${item.obat.id}">
                </td>
                <td class="text-center">${item.jumlah}</td>
                <td>
                    <input type="number" class="form-control jumlah-diterima" 
                           name="items[${item.id}][jumlah_diterima]" 
                           min="1" max="${item.jumlah}" 
                           value="${jumlahDiterima}" required
                           data-harga="${item.harga_satuan}"
                           oninput="updateSubtotal(this)">
                </td>
                <td class="text-end">
                    Rp ${formatRupiah(item.harga_satuan)}
                    <input type="hidden" name="items[${item.id}][harga_satuan]" 
                           value="${item.harga_satuan}">
                </td>
                <td class="text-end subtotal-cell" data-subtotal="${item.harga_satuan * jumlahDiterima}">
                    Rp ${formatRupiah(item.harga_satuan * jumlahDiterima)}
                </td>
            </tr>
            `;
        });
        
        itemsContainer.innerHTML = adaObat ? html : `
            <tr>
                <td colspan="5" class="text-center text-danger">
                    Tidak ada data obat yang valid untuk pembelian ini
                </td>
            </tr>
        `;
    }
    
    // Add to global scope so it can be called from inline handlers
    window.updateSubtotal = function(input) {
        const harga = parseFloat(input.dataset.harga);
        const jumlah = parseFloat(input.value) || 0;
        const subtotal = harga * jumlah;
        
        const subtotalCell = input.closest('tr').querySelector('.subtotal-cell');
        subtotalCell.textContent = 'Rp ' + formatRupiah(subtotal);
        subtotalCell.dataset.subtotal = subtotal;
    };
    
    penerimaanForm.addEventListener('submit', function(e) {
        // Validate form before submission
        const items = document.querySelectorAll('.jumlah-diterima');
        let hasItems = false;
        
        items.forEach(item => {
            if (parseInt(item.value) > 0) {
                hasItems = true;
            }
        });
        
        if (!hasItems && items.length > 0) {
            e.preventDefault();
            alert('Minimal satu item harus memiliki jumlah diterima lebih dari 0');
        }
    });
    
    function formatRupiah(angka) {
        if (!angka) return '0';
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
});
</script>
@endsection
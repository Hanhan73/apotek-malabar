@extends('layouts.app')

@section('title', 'Tambah Pembelian Obat')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Form Pembelian Obat</h5>
        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('pembelian.store') }}" method="POST">
            @csrf

            <!-- Informasi Utama Pembelian -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="supplier_id" class="form-label">Supplier*</label>
                    <select class="form-select @error('supplier_id') is-invalid @enderror" 
                            id="supplier_id" name="supplier_id" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label for="tanggal_pembelian" class="form-label">Tanggal Pembelian*</label>
                    <input type="date" class="form-control @error('tanggal_pembelian') is-invalid @enderror" 
                           id="tanggal_pembelian" name="tanggal_pembelian" 
                           value="{{ old('tanggal_pembelian', date('Y-m-d')) }}" required>
                    @error('tanggal_pembelian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label for="jenis_pembayaran" class="form-label">Jenis Pembayaran*</label>
                    <select class="form-select @error('jenis_pembayaran') is-invalid @enderror" 
                            id="jenis_pembayaran" name="jenis_pembayaran" required>
                        <option value="tunai" {{ old('jenis_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="kredit" {{ old('jenis_pembayaran') == 'kredit' ? 'selected' : '' }}>Kredit</option>
                    </select>
                    @error('jenis_pembayaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Field Jatuh Tempo (Hanya Tampil untuk Kredit) -->
            <div class="row mb-4" id="jatuh-tempo-row" style="display: none;">
                <div class="col-md-3 offset-md-9">
                    <label for="tanggal_jatuh_tempo" class="form-label">Tanggal Jatuh Tempo*</label>
                    <input type="date" class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" 
                        id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" 
                        value="{{ old('tanggal_jatuh_tempo') }}">
                    @error('tanggal_jatuh_tempo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Daftar Obat -->
            <div class="mb-4">
                <label class="form-label">Daftar Obat*</label>
                <div class="table-responsive">
                    <table class="table table-bordered" id="obat-table">
                        <thead class="table-light">
                            <tr>
                                <th width="40%">Nama Obat</th>
                                <th width="15%">Jumlah</th>
                                <th width="20%">Harga Satuan</th>
                                <th width="20%">Subtotal</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(old('items'))
                                @foreach(old('items') as $index => $item)
                                <tr>
                                    <td>
                                        <select class="form-select obat-select" 
                                                name="items[{{ $index }}][obat_id]" required>
                                            <option value="">-- Pilih Obat --</option>
                                            @foreach($obats as $obat)
                                                <option value="{{ $obat->id }}" 
                                                    data-harga="{{ $obat->harga_beli }}"
                                                    {{ $item['obat_id'] == $obat->id ? 'selected' : '' }}>
                                                    {{ $obat->nama_obat }} ({{ $obat->kode_obat }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control jumlah-input" 
                                               name="items[{{ $index }}][jumlah]" 
                                               min="1" value="{{ $item['jumlah'] }}" required>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control harga-input" 
                                                   name="items[{{ $index }}][harga]" 
                                                   value="{{ number_format($item['harga'], 0, ',', '.') }}" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control subtotal-input" 
                                                   value="{{ number_format($item['harga'] * $item['jumlah'], 0, ',', '.') }}" readonly>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>
                                        <select class="form-select obat-select" name="items[0][obat_id]" required>
                                            <option value="">-- Pilih Obat --</option>
                                            @foreach($obats as $obat)
                                                <option value="{{ $obat->id }}" 
                                                    data-harga="{{ $obat->harga_beli }}">
                                                    {{ $obat->nama_obat }} ({{ $obat->kode_obat }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control jumlah-input" 
                                               name="items[0][jumlah]" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control harga-input" 
                                                   name="items[0][harga]" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control subtotal-input" readonly>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total Pembelian</strong></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" id="total" name="total" readonly>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" id="add-row">
                                        <i class="bi bi-plus"></i> Tambah
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Pembelian
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template untuk Row Obat Baru -->
<script type="text/template" id="obat-row-template">
    <tr>
        <td>
            <select class="form-select obat-select" name="items[__INDEX__][obat_id]" required>
                <option value="">-- Pilih Obat --</option>
                @foreach($obats as $obat)
                    <option value="{{ $obat->id }}" data-harga="{{ $obat->harga_beli }}">
                        {{ $obat->nama_obat }} ({{ $obat->kode_obat }})
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control jumlah-input" 
                   name="items[__INDEX__][jumlah]" min="1" value="1" required>
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control harga-input" 
                       name="items[__INDEX__][harga]" readonly>
            </div>
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control subtotal-input" readonly>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-row">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</script>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // =============================================
    // Fungsi untuk Field Jatuh Tempo
    // =============================================
    const jenisPembayaranSelect = document.getElementById('jenis_pembayaran');
    const jatuhTempoRow = document.getElementById('jatuh-tempo-row');
    const tanggalPembelianInput = document.getElementById('tanggal_pembelian');
    const tanggalJatuhTempoInput = document.getElementById('tanggal_jatuh_tempo');

    // Fungsi untuk menampilkan/menyembunyikan field jatuh tempo
    function toggleJatuhTempo() {
        if (jenisPembayaranSelect.value === 'kredit') {
            jatuhTempoRow.style.display = 'flex';
            tanggalJatuhTempoInput.setAttribute('required', 'required');
            
            // Set default jatuh tempo 30 hari dari tanggal pembelian jika kosong
            if (!tanggalJatuhTempoInput.value && tanggalPembelianInput.value) {
                setDefaultJatuhTempo();
            }
        } else {
            jatuhTempoRow.style.display = 'none';
            tanggalJatuhTempoInput.removeAttribute('required');
        }
    }

    // Fungsi untuk set default tanggal jatuh tempo
    function setDefaultJatuhTempo() {
        const date = new Date(tanggalPembelianInput.value);
        date.setDate(date.getDate() + 30);
        const formattedDate = date.toISOString().split('T')[0];
        tanggalJatuhTempoInput.value = formattedDate;
        tanggalJatuhTempoInput.min = tanggalPembelianInput.value;
    }

    // Event listener untuk jenis pembayaran
    jenisPembayaranSelect.addEventListener('change', toggleJatuhTempo);
    
    // Event listener untuk tanggal pembelian
    tanggalPembelianInput.addEventListener('change', function() {
        if (jenisPembayaranSelect.value === 'kredit') {
            if (this.value) {
                tanggalJatuhTempoInput.min = this.value;
                
                // Update nilai jatuh tempo jika belum diisi
                if (!tanggalJatuhTempoInput.value) {
                    setDefaultJatuhTempo();
                }
            }
        }
    });

    // Jalankan saat pertama kali load
    toggleJatuhTempo();

    // =============================================
    // Fungsi untuk Tabel Obat
    // =============================================
    const obatTable = document.getElementById("obat-table");
    const tbody = obatTable.querySelector("tbody");
    const addButton = document.getElementById("add-row");
    const totalInput = document.getElementById("total");
    
    // Format number to Rupiah
    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Parse Rupiah to number
    function parseRupiah(rupiah) {
        return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
    }
    
    // Update subtotal and total
    function updateSubtotal(row) {
        const hargaInput = row.querySelector(".harga-input");
        const jumlahInput = row.querySelector(".jumlah-input");
        const subtotalInput = row.querySelector(".subtotal-input");
        
        const harga = parseRupiah(hargaInput.value);
        const jumlah = parseInt(jumlahInput.value) || 0;
        const subtotal = harga * jumlah;
        
        subtotalInput.value = formatRupiah(subtotal);
        updateTotal();
    }
    
    function updateTotal() {
        let total = 0;
        document.querySelectorAll(".subtotal-input").forEach(input => {
            total += parseRupiah(input.value);
        });
        totalInput.value = formatRupiah(total);
    }
    
    // Update dropdown options (hide already selected)
    function updateDropdownOptions() {
        const selectedIds = Array.from(document.querySelectorAll('.obat-select'))
            .map(select => select.value)
            .filter(val => val !== "");
        
        document.querySelectorAll('.obat-select').forEach(currentSelect => {
            const currentValue = currentSelect.value;
            Array.from(currentSelect.options).forEach(option => {
                if (option.value === "") return; // Skip placeholder option
                if (option.value === currentValue || !selectedIds.includes(option.value)) {
                    option.hidden = false;
                } else {
                    option.hidden = true;
                }
            });
        });
    }
    
    // Bind events to a row
    function bindRowEvents(row) {
        const obatSelect = row.querySelector(".obat-select");
        const jumlahInput = row.querySelector(".jumlah-input");
        const hargaInput = row.querySelector(".harga-input");
        
        obatSelect.addEventListener("change", function() {
            const selectedOption = this.options[this.selectedIndex];
            const harga = selectedOption.dataset.harga || 0;
            hargaInput.value = formatRupiah(harga);
            updateSubtotal(row);
            updateDropdownOptions();
        });
        
        jumlahInput.addEventListener("input", function() {
            updateSubtotal(row);
        });
    }
    
    // Add new row
    addButton.addEventListener("click", function() {
        const newRow = document.createElement("tr");
        const newIndex = tbody.querySelectorAll("tr").length;
        const template = document.getElementById("obat-row-template").innerHTML;
        
        newRow.innerHTML = template.replace(/__INDEX__/g, newIndex);
        tbody.appendChild(newRow);
        bindRowEvents(newRow);
        updateDropdownOptions();
    });
    
    // Remove row
    tbody.addEventListener("click", function(e) {
        if (e.target.closest(".remove-row")) {
            const row = e.target.closest("tr");
            if (tbody.querySelectorAll("tr").length > 1) {
                row.remove();
                updateTotal();
                updateDropdownOptions();
            } else {
                // Reset the single remaining row instead of removing it
                const select = row.querySelector(".obat-select");
                const inputs = row.querySelectorAll("input");
                select.value = "";
                inputs.forEach(input => {
                    if (input.type === "number") {
                        input.value = "1";
                    } else if (input.classList.contains("harga-input") || 
                             input.classList.contains("subtotal-input")) {
                        input.value = "0";
                    }
                });
                updateTotal();
                updateDropdownOptions();
            }
        }
    });
    
    // Initialize existing rows
    document.querySelectorAll("#obat-table tbody tr").forEach(row => {
        bindRowEvents(row);
        
        // Set initial harga if obat is selected
        const select = row.querySelector(".obat-select");
        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const hargaInput = row.querySelector(".harga-input");
            hargaInput.value = formatRupiah(selectedOption.dataset.harga || 0);
            updateSubtotal(row);
        }
    });
    
    // Initial dropdown options update
    updateDropdownOptions();
    
    // Hitung total awal jika ada data old items
    if (document.querySelectorAll("#obat-table tbody tr").length > 0) {
        updateTotal();
    }
});
</script>
@endsection
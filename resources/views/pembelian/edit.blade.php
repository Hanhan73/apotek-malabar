@extends('layouts.app')

@section('title', 'Edit Pembelian Obat')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Edit Pembelian Obat</h5>
        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="supplier_id" class="form-label">Supplier*</label>
                    <select class="form-select @error('supplier_id') is-invalid @enderror" 
                            id="supplier_id" name="supplier_id" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                {{ $pembelian->supplier_id == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label for="tanggal_pembelian" class="form-label">Tanggal*</label>
                    <input type="date" class="form-control @error('tanggal_pembelian') is-invalid @enderror" 
                           id="tanggal_pembelian" name="tanggal_pembelian" 
                           value="{{ old('tanggal_pembelian', $pembelian->tanggal_pembelian) }}" required>
                    @error('tanggal_pembelian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label for="jenis_pembayaran" class="form-label">Pembayaran*</label>
                    <select class="form-select @error('jenis_pembayaran') is-invalid @enderror" 
                            id="jenis_pembayaran" name="jenis_pembayaran" required>
                        <option value="tunai" {{ $pembelian->jenis_pembayaran == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="kredit" {{ $pembelian->jenis_pembayaran == 'kredit' ? 'selected' : '' }}>Kredit</option>
                    </select>
                    @error('jenis_pembayaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

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
                            @foreach($pembelian->detailPembelian as $index => $item)
                            <tr>
                                <td>
                                    <select class="form-select obat-select" 
                                            name="items[{{ $index }}][obat_id]" required>
                                        <option value="">-- Pilih Obat --</option>
                                        @foreach($obats as $obat)
                                            <option value="{{ $obat->id }}" 
                                                data-harga="{{ $obat->harga_beli }}"
                                                {{ $item->obat_id == $obat->id ? 'selected' : '' }}>
                                                {{ $obat->nama_obat }} ({{ $obat->kode_obat }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control jumlah-input" 
                                           name="items[{{ $index }}][jumlah]" 
                                           min="1" value="{{ $item->jumlah }}" required>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control harga-input" 
                                               name="items[{{ $index }}][harga]" 
                                               value="{{ number_format($item->harga_satuan, 0, ',', '.') }}" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control subtotal-input" 
                                               value="{{ number_format($item->subtotal, 0, ',', '.') }}" readonly>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total Pembelian</strong></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" id="total" 
                                               value="{{ number_format($pembelian->total, 0, ',', '.') }}" readonly>
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
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const obatTable = document.getElementById("obat-table");
        const tbody = obatTable.querySelector("tbody");
        const addButton = document.getElementById("add-row");
        const obatOptions = @json($obats);
        
        // Format number to Rupiah
        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Parse Rupiah to number
        function parseRupiah(rupiah) {
            return parseInt(rupiah.replace(/[^0-9]/g, ''));
        }
        
        // Update subtotal and total
        function updateSubtotal(row) {
            const hargaInput = row.querySelector(".harga-input");
            const jumlahInput = row.querySelector(".jumlah-input");
            const subtotalInput = row.querySelector(".subtotal-input");
            
            const harga = parseRupiah(hargaInput.value) || 0;
            const jumlah = parseInt(jumlahInput.value) || 0;
            const subtotal = harga * jumlah;
            
            subtotalInput.value = formatRupiah(subtotal);
            updateTotal();
        }
        
        function updateTotal() {
            let total = 0;
            document.querySelectorAll(".subtotal-input").forEach(input => {
                total += parseRupiah(input.value) || 0;
            });
            document.getElementById("total").value = formatRupiah(total);
        }
        
        // Update dropdown options (hide already selected)
        function updateDropdownOptions() {
            const selectedIds = Array.from(document.querySelectorAll('.obat-select'))
                .map(select => select.value)
                .filter(val => val !== "");
            
            document.querySelectorAll('.obat-select').forEach(currentSelect => {
                const currentValue = currentSelect.value;
                Array.from(currentSelect.options).forEach(option => {
                    if (option.value === "") return;
                    option.disabled = selectedIds.includes(option.value) && option.value !== currentValue;
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
            
            let optionsHtml = '<option value="">-- Pilih Obat --</option>';
            obatOptions.forEach(obat => {
                optionsHtml += `<option value="${obat.id}" data-harga="${obat.harga_beli}">
                    ${obat.nama_obat} (${obat.kode_obat})
                </option>`;
            });
            
            newRow.innerHTML = `
                <td>
                    <select class="form-select obat-select" name="items[${newIndex}][obat_id]" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control jumlah-input" 
                           name="items[${newIndex}][jumlah]" min="1" value="1" required>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control harga-input" 
                               name="items[${newIndex}][harga]" readonly>
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
            `;
            
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
    });
</script>
@endsection
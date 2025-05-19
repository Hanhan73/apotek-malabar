@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tambah Transaksi Penjualan</h5>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('penjualan.store') }}" id="formPenjualan">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nomor_nota">Nomor Nota:</label>
                                    <input type="text" class="form-control @error('nomor_nota') is-invalid @enderror" id="nomor_nota" name="nomor_nota" value="{{ $nomorNota }}" readonly>
                                    @error('nomor_nota')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="tanggal">Tanggal:</label>
                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="jenis_penjualan">Jenis Penjualan:</label>
                                    <select class="form-control @error('jenis_penjualan') is-invalid @enderror" id="jenis_penjualan" name="jenis_penjualan" required>
                                        <option value="tanpa_resep" {{ old('jenis_penjualan') == 'tanpa_resep' ? 'selected' : '' }}>Tanpa Resep</option>
                                        <option value="dengan_resep" {{ old('jenis_penjualan') == 'dengan_resep' ? 'selected' : '' }}>Dengan Resep</option>
                                    </select>
                                    @error('jenis_penjualan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Detail Obat</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="tabelDetailObat">
                                <thead>
                                    <tr>
                                        <th width="40%">Nama Obat</th>
                                        <th width="15%">Harga</th>
                                        <th width="15%">Stok</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="15%">Subtotal</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="row-0">
                                        <td>
                                            <select class="form-control obat-select" name="obat_id[]" data-row="0" required>
                                                <option value="">Pilih Obat</option>
                                                @foreach ($obats as $obat)
                                                    <option value="{{ $obat->id }}" 
                                                            data-harga="{{ $obat->harga_jual }}" 
                                                            data-stok="{{ $obat->stok }}">
                                                        {{ $obat->nama_obat }} ({{ $obat->kode_obat }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <span class="harga-display" id="harga-display-0">0</span>
                                            <input type="hidden" class="harga-obat" id="harga-0" value="0">
                                        </td>
                                        <td>
                                            <span class="stok-display" id="stok-display-0">0</span>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control jumlah-obat" name="jumlah[]" id="jumlah-0" min="1" value="1" data-row="0" required>
                                        </td>
                                        <td>
                                            <span class="subtotal-display" id="subtotal-display-0">0</span>
                                            <input type="hidden" class="subtotal-obat" id="subtotal-0" value="0">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm hapus-baris" data-row="0" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total:</td>
                                        <td>
                                            <span id="total-penjualan">0</span>
                                            <input type="hidden" name="total_harga" id="total-harga" value="0">
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-secondary" id="tambahBaris">Tambah Obat</button>
                        </div>

                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowCount = 1;

        // Format number to currency
        function formatRupiah(angka) {
            return 'Rp ' + parseFloat(angka).toLocaleString('id-ID');
        }

        // Calculate subtotal for a row
        function hitungSubtotal(rowId) {
            const harga = parseFloat(document.getElementById('harga-' + rowId).value);
            const jumlah = parseInt(document.getElementById('jumlah-' + rowId).value);
            const subtotal = harga * jumlah;
            
            document.getElementById('subtotal-' + rowId).value = subtotal;
            document.getElementById('subtotal-display-' + rowId).textContent = formatRupiah(subtotal);
            
            hitungTotal();
        }

        // Calculate total from all rows
        function hitungTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-obat').forEach(function(el) {
                total += parseFloat(el.value);
            });
            
            document.getElementById('total-penjualan').textContent = formatRupiah(total);
            document.getElementById('total-harga').value = total;
        }

        // Handler for obat select change
        function handleObatChange() {
            document.querySelectorAll('.obat-select').forEach(function(select) {
                select.addEventListener('change', function() {
                    const rowId = this.getAttribute('data-row');
                    const selectedOption = this.options[this.selectedIndex];
                    
                    if (selectedOption.value) {
                        const harga = selectedOption.getAttribute('data-harga');
                        const stok = selectedOption.getAttribute('data-stok');
                        
                        document.getElementById('harga-' + rowId).value = harga;
                        document.getElementById('harga-display-' + rowId).textContent = formatRupiah(harga);
                        document.getElementById('stok-display-' + rowId).textContent = stok;
                        
                        const jumlahInput = document.getElementById('jumlah-' + rowId);
                        jumlahInput.max = stok;
                        
                        hitungSubtotal(rowId);
                    } else {
                        document.getElementById('harga-' + rowId).value = 0;
                        document.getElementById('harga-display-' + rowId).textContent = formatRupiah(0);
                        document.getElementById('stok-display-' + rowId).textContent = 0;
                        hitungSubtotal(rowId);
                    }
                });
            });
        }

        // Handler for jumlah change
        function handleJumlahChange() {
            document.querySelectorAll('.jumlah-obat').forEach(function(input) {
                input.addEventListener('change', function() {
                    const rowId = this.getAttribute('data-row');
                    hitungSubtotal(rowId);
                });
                
                input.addEventListener('keyup', function() {
                    const rowId = this.getAttribute('data-row');
                    hitungSubtotal(rowId);
                });
            });
        }

        // Add new row
        document.getElementById('tambahBaris').addEventListener('click', function() {
            const tbody = document.querySelector('#tabelDetailObat tbody');
            const template = document.getElementById('row-0').cloneNode(true);
            template.id = 'row-' + rowCount;
            
            // Update attributes with new row index
            template.querySelectorAll('[data-row="0"]').forEach(function(el) {
                el.setAttribute('data-row', rowCount);
            });
            
            template.querySelector('.obat-select').name = 'obat_id[]';
            template.querySelector('.obat-select').id = 'obat-' + rowCount;
            template.querySelector('.obat-select').selectedIndex = 0;
            
            template.querySelector('.harga-display').id = 'harga-display-' + rowCount;
            template.querySelector('.harga-display').textContent = '0';
            
            template.querySelector('.harga-obat').id = 'harga-' + rowCount;
            template.querySelector('.harga-obat').value = '0';
            
            template.querySelector('.stok-display').id = 'stok-display-' + rowCount;
            template.querySelector('.stok-display').textContent = '0';
            
            template.querySelector('.jumlah-obat').id = 'jumlah-' + rowCount;
            template.querySelector('.jumlah-obat').name = 'jumlah[]';
            template.querySelector('.jumlah-obat').value = '1';
            
            template.querySelector('.subtotal-display').id = 'subtotal-display-' + rowCount;
            template.querySelector('.subtotal-display').textContent = '0';
            
            template.querySelector('.subtotal-obat').id = 'subtotal-' + rowCount;
            template.querySelector('.subtotal-obat').value = '0';
            
            const deleteButton = template.querySelector('.hapus-baris');
            deleteButton.setAttribute('data-row', rowCount);
            deleteButton.disabled = false;
            
            tbody.appendChild(template);
            rowCount++;
            
            // Rebind handlers for the new row
            handleObatChange();
            handleJumlahChange();
            handleHapusBaris();
        });

        // Delete row
        function handleHapusBaris() {
            document.querySelectorAll('.hapus-baris').forEach(function(button) {
                button.addEventListener('click', function() {
                    if (document.querySelectorAll('#tabelDetailObat tbody tr').length > 1) {
                        const rowId = this.getAttribute('data-row');
                        document.getElementById('row-' + rowId).remove();
                        hitungTotal();
                    }
                });
            });
        }

        // Initialize handlers
        handleObatChange();
        handleJumlahChange();
        handleHapusBaris();

        // Form validation before submit
        document.getElementById('formPenjualan').addEventListener('submit', function(e) {
            let valid = true;
            const obatSelects = document.querySelectorAll('.obat-select');
            
            // Check if at least one obat is selected
            if (obatSelects.length === 0) {
                alert('Harap tambahkan minimal satu obat!');
                e.preventDefault();
                return false;
            }
            
            // Check for duplicate obat
            const selectedObats = [];
            obatSelects.forEach(function(select) {
                if (select.value) {
                    if (selectedObats.includes(select.value)) {
                        valid = false;
                        alert('Terdapat obat yang dipilih lebih dari satu kali. Harap gabungkan ke dalam satu baris!');
                        return;
                    }
                    selectedObats.push(select.value);
                } else {
                    valid = false;
                    alert('Harap pilih obat di setiap baris!');
                    return;
                }
            });
            
            if (!valid) {
                e.preventDefault();
                return false;
            }
            
            // Check quantity
            document.querySelectorAll('.jumlah-obat').forEach(function(input) {
                const jumlah = parseInt(input.value);
                const rowId = input.getAttribute('data-row');
                const stok = parseInt(document.getElementById('stok-display-' + rowId).textContent);
                
                if (jumlah <= 0) {
                    valid = false;
                    alert('Jumlah obat harus lebih dari 0!');
                    return;
                }
                
                if (jumlah > stok) {
                    valid = false;
                    const obatName = document.getElementById('obat-' + rowId).options[document.getElementById('obat-' + rowId).selectedIndex].text;
                    alert('Jumlah ' + obatName + ' melebihi stok yang tersedia (' + stok + ')!');
                    return;
                }
            });
            
            if (!valid) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush
@endsection
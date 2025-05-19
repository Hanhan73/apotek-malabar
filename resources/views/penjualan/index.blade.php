@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Transaksi Penjualan</h5>
                    <a href="{{ route('penjualan.create') }}" class="btn btn-primary">Tambah Penjualan</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nomor Nota</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Penjualan</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penjualans as $index => $penjualan)
                                    <tr>
                                        <td>{{ $penjualans->firstItem() + $index }}</td>
                                        <td>{{ $penjualan->nomor_nota }}</td>
                                        <td>{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($penjualan->jenis_penjualan == 'dengan_resep')
                                                <span class="badge bg-info">Dengan Resep</span>
                                            @else
                                                <span class="badge bg-secondary">Tanpa Resep</span>
                                            @endif
                                        </td>
                                        <td>Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($penjualan->status_pembayaran == 'sudah_dibayar')
                                                <span class="badge bg-success">Sudah Dibayar</span>
                                            @else
                                                <span class="badge bg-warning">Belum Dibayar</span>
                                            @endif
                                        </td>
                                        <td>{{ $penjualan->user->name }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('penjualan.show', $penjualan->id) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                                
                                                @if ($penjualan->status_pembayaran == 'belum_dibayar')
                                                    <a href="{{ route('penjualan.edit', $penjualan->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    
                                                    <form action="{{ route('penjualan.destroy', $penjualan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data penjualan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $penjualans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
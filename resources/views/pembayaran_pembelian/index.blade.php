@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2 class="mb-0">Daftar Pembayaran Pembelian</h2>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between">
                <a href="{{ route('pembayaran-pembelian.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pembayaran
                </a>
                
                <form action="{{ route('pembayaran-pembelian.index') }}" method="GET" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">No. Pembelian</th>
                            <th width="20%">Supplier</th>
                            <th width="12%">Tanggal Bayar</th>
                            <th width="15%">Jumlah Bayar</th>
                            <th width="10%">Metode</th>
                            <th width="10%">Status</th>
                            <th width="13%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayarans as $key => $pembayaran)
                        <tr>
                            <td>{{ ($pembayarans->currentPage()-1) * $pembayarans->perPage() + $key + 1 }}</td>
                            <td>{{ $pembayaran->penerimaanPembelian->pembelian->kode_pembelian }}</td>
                            <td>{{ $pembayaran->penerimaanPembelian->pembelian->supplier->nama_supplier }}</td>
                            <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $pembayaran->metode_pembayaran == 'tunai' ? 'success' : 'info' }}">
                                    {{ ucfirst($pembayaran->metode_pembayaran) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $pembayaran->status == 'lunas' ? 'success' : 'warning' }}">
                                    {{ ucfirst($pembayaran->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('pembayaran-pembelian.show', $pembayaran->id) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pembayaran-pembelian.edit', $pembayaran->id) }}" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pembayaran-pembelian.destroy', $pembayaran->id) }}" 
                                      method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            title="Hapus" onclick="return confirm('Apakah Anda yakin?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data pembayaran pembelian</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($pembayarans->hasPages())
        <div class="card-footer">
            {{ $pembayarans->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
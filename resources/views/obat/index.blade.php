@extends('layouts.app')

@section('title', 'Data Obat')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Data Obat</h1>
        <a href="{{ route('obat.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Obat
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Obat</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Search Form -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="{{ route('obat.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari obat..." value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <a href="{{ route('obat.index') }}" class="btn btn-outline-secondary {{ !request('jenis') ? 'active' : '' }}">Semua</a>
                        <a href="{{ route('obat.index', ['jenis' => 'bebas']) }}" class="btn btn-outline-secondary {{ request('jenis') == 'bebas' ? 'active' : '' }}">Bebas</a>
                        <a href="{{ route('obat.index', ['jenis' => 'bebas_terbatas']) }}" class="btn btn-outline-secondary {{ request('jenis') == 'bebas_terbatas' ? 'active' : '' }}">Bebas Terbatas</a>
                        <a href="{{ route('obat.index', ['jenis' => 'herbal']) }}" class="btn btn-outline-secondary {{ request('jenis') == 'herbal' ? 'active' : '' }}">Herbal</a>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">#</th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'kode_obat', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark d-flex justify-content-between align-items-center">
                                    Kode
                                    @if(request('sort') === 'kode_obat')
                                        <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-2"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_obat', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark d-flex justify-content-between align-items-center">
                                    Nama Obat
                                    @if(request('sort') === 'nama_obat')
                                        <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-2"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Jenis</th>
                            <th>Stok</th>
                            <th>Harga Jual</th>
                            <th>Harga Beli</th>
                            <th>Kadaluarsa</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_obat }}</td>
                            <td>{{ $item->nama_obat }}</td>
                            <td>
                                @switch($item->jenis_obat)
                                    @case('bebas') <span class="badge bg-primary">Bebas</span> @break
                                    @case('herbal') <span class="badge bg-success">Herbal</span> @break
                                    @case('psikotropik') <span class="badge bg-danger">Psikotropik</span> @break
                                    @case('suplemen') <span class="badge bg-info">Suplemen</span> @break
                                    @case('bebas_terbatas') <span class="badge bg-warning">Bebas Terbatas</span> @break
                                @endswitch
                            </td>
                            <td class="text-center">
                                @if($item->stok <= 0)
                                    <span class="badge bg-danger">{{ $item->stok }}</span>
                                @elseif($item->stok <= 10)
                                    <span class="badge bg-warning">{{ $item->stok }}</span>
                                @else
                                    <span class="badge bg-success">{{ $item->stok }}</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->kadaluarsa)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('obat.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('obat.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data?')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $data->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    
    .btn-group {
        gap: 0.25rem;
    }
    
    th a {
        text-decoration: none;
    }
    
    th a:hover {
        text-decoration: underline;
    }
</style>
@endpush
@extends('layouts.app')

@section('title', 'Data Supplier')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Supplier</h5>
        <a href="{{ route('supplier.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Tambah Supplier
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <!-- Search Form -->
        <div class="mb-3">
            <form action="{{ route('supplier.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari supplier..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'kode_supplier', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                                Kode
                                @if(request('sort') === 'kode_supplier')
                                    <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Nama Supplier</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $item)
                    <tr>
                        <td>{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                        <td>{{ $item->kode_supplier }}</td>
                        <td>{{ $item->nama_supplier }}</td>
                        <td>{{ Str::limit($item->alamat, 30) }}</td>
                        <td>{{ $item->telepon }}</td>
                        <td>
                            <a href="{{ route('supplier.show', $item->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('supplier.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('supplier.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-3">
            {{ $suppliers->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
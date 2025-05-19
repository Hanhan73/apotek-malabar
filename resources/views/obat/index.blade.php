@extends('layouts.app')

@section('title', 'Data Obat')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Obat</h5>
        <a href="{{ route('obat.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Tambah Obat
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Nama Obat</th>
                        <th>Jenis</th>
                        <th>Stok</th>
                        <th>Harga Jual</th>
                        <th>Harga Beli</th>
                        <th>Kadaluarsa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
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
                        <td>{{ $item->stok }}</td>
                        <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->kadaluarsa)->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('obat.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('obat.destroy', $item->id) }}" method="POST" class="d-inline">
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
    </div>
</div>
@endsection
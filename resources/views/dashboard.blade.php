@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Dashboard</h1>

    {{-- Summary Cards --}}
    <div class="row g-4">
        @if(in_array(auth()->user()->role, ['admin', 'apoteker', 'asisten_apoteker']))
        <x-dashboard.card 
            title="Total Obat" 
            value="{{ $totalObat ?? 0 }}" 
            icon="bi-capsule" 
            color="primary" />
        
        <x-dashboard.card 
            title="Obat Hampir Habis" 
            value="{{ $obatHampirHabis ?? 0 }}" 
            icon="bi-exclamation-triangle" 
            color="warning" />
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'apoteker']))
        <x-dashboard.card 
            title="Pembelian Bulan Ini" 
            value="{{ $pembelianBulanIni ?? 0 }}" 
            icon="bi-cart-plus" 
            color="success" />
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'apoteker', 'asisten_apoteker']))
        <x-dashboard.card 
            title="Penjualan Bulan Ini" 
            value="{{ $penjualanBulanIni ?? 0 }}" 
            icon="bi-cart-check" 
            color="info" />
        @endif
    </div>

    {{-- Detail Tables --}}
    <div class="row mt-4 g-4">
        @if(in_array(auth()->user()->role, ['admin', 'apoteker']))
        <div class="col-lg-6">
            <x-dashboard.table 
                title="Obat Hampir Habis" 
                :headers="['Kode', 'Nama Obat', 'Stok', 'Status']"
                :rows="$obatHampirHabisList"
                :columns="['kode_obat', 'nama_obat', 'stok']"
                status="Hampir Habis"
                statusColor="warning"
                link="{{ route('obat.index') }}"
            />
        </div>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'apoteker', 'asisten_apoteker']))
        <div class="col-lg-6">
            <x-dashboard.table 
                title="Transaksi Terakhir" 
                :headers="['No Nota', 'Tanggal', 'Total', 'Status']"
                :rows="$transaksiTerakhir"
                :custom="true"
                link="{{ route('penjualan.index') }}"
            />
        </div>
        @endif
    </div>

    @if(auth()->user()->role === 'pemilik')
    <div class="card shadow mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Ringkasan Laporan Bulanan</h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Total Pembelian: <strong>{{ $pembelianBulanIni ?? 0 }}</strong></li>
                <li class="list-group-item">Total Penjualan: <strong>{{ $penjualanBulanIni ?? 0 }}</strong></li>
                <li class="list-group-item">Obat Hampir Habis: <strong>{{ $obatHampirHabis ?? 0 }}</strong></li>
            </ul>
            <a href="{{ route('laporan.index') }}" class="btn btn-outline-primary mt-3">Lihat Laporan Lengkap</a>
        </div>
    </div>
    @endif
</div>
@endsection

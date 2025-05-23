<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apotek Malabar - @yield('title', 'Dashboard')</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @php
        $user = auth()->user(); 
        $role = $user->role ?? '';
    @endphp

    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <h4>APOTEK MALABAR</h4>
            </div>
            
            <hr class="sidebar-divider">
            
            <!-- Navigation -->
            <ul class="nav flex-column">
            <!-- Menu Umum -->
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            {{-- === ADMIN === --}}
            @if($role === 'admin')
                <li class="nav-item mt-3">
                    <span class="nav-link text-muted">DATA MASTER</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('obat.*') ? 'active' : '' }}" href="{{ route('obat.index') }}">
                        <i class="bi bi-capsule me-2"></i> Data Obat
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('supplier.*') ? 'active' : '' }}" href="{{ route('supplier.index') }}">
                        <i class="bi bi-truck me-2"></i> Data Supplier
                    </a>
                </li>

                <li class="nav-item mt-3">
                    <span class="nav-link text-muted">Pembelian</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('retur-pembelian.*') ? 'active' : '' }}" href="{{ route('retur-pembelian.index') }}">
                        <i class="bi bi-arrow-return-left me-2"></i> Retur Pembelian
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('pembayaran-pembelian.*') ? 'active' : '' }}" href="{{ route('pembayaran-pembelian.index') }}">
                        <i class="bi bi-credit-card me-2"></i> Pembayaran
                    </a>
                </li>
            @endif

            {{-- === APOTEKER === --}}
            @if($role === 'apoteker')
                <li class="nav-item mt-3">
                    <span class="nav-link text-muted">PEMBELIAN</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('pembelian.*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
                        <i class="bi bi-cart me-2"></i> Order Pembelian
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('penerimaan-pembelian.*') ? 'active' : '' }}" href="{{ route('penerimaan-pembelian.index') }}">
                        <i class="bi bi-check-circle me-2"></i> Penerimaan Obat
                    </a>
                </li>

                <li class="nav-item mt-3">
                    <span class="nav-link text-muted">PENJUALAN</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('penjualan.*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
                        <i class="bi bi-basket me-2"></i> Penjualan Obat
                    </a>
                </li>
            @endif

            {{-- === ASISTEN APOTEKER === --}}
            @if($role === 'asisten_apoteker')
                <li class="nav-item mt-3">
                    <span class="nav-link text-muted">PENJUALAN</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('penjualan.*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
                        <i class="bi bi-basket me-2"></i> Penjualan Obat
                    </a>
                </li>
            @endif

            {{-- === PEMILIK === --}}
            @if($role === 'pemilik' || $role === 'admin')
                <li class="nav-item mt-3">
                    <span class="nav-link text-muted">LAPORAN</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('laporan.index') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Laporan Utama
                    </a>
                </li>
            @endif

            {{-- === LOGOUT === --}}
            <li class="nav-item mt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link text-white bg-transparent border-0 w-100 text-start">
                        <i class="bi bi-box-arrow-right me-2"></i> Log Out
                    </button>
                </form>
            </li>
        </ul>
        </div>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Top Bar -->
            <div class="topbar">
                <button class="navbar-toggler d-md-none" type="button" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        @auth
                            <span class="fw-bold">{{ auth()->user()->name }}</span>
                        @endauth
                    </div>
                    <div class="topbar-divider"></div>
                    <div>
                        <span class="badge bg-primary">{{ ucfirst(auth()->user()->role ?? 'User') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="container-fluid px-0">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-3">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle sidebar on small screens
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('toggled');
                    document.querySelector('.content-wrapper').classList.toggle('toggled');
                });
            }
        });
    </script>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>
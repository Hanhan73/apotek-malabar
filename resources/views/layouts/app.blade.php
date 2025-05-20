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
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
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
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <div class="sidebar-heading">Data Master</div>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('obat.*') ? 'active' : '' }}" href="{{ route('obat.index') }}">
                        <i class="bi bi-capsule"></i>
                        <span>Data Obat</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}" href="{{ route('supplier.index') }}">
                        <i class="bi bi-truck"></i>
                        <span>Data Supplier</span>
                    </a>
                </li>
                
                <div class="sidebar-heading">Pembelian</div>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pembelian.*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
                        <i class="bi bi-cart-plus"></i>
                        <span>Pembelian Obat</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('penerimaan-pembelian.*') ? 'active' : '' }}" href="{{ route('penerimaan-pembelian.index') }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Penerimaan Obat</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pembayaran-pembelian.*') ? 'active' : '' }}" href="{{ route('pembayaran-pembelian.index') }}">
                        <i class="bi bi-credit-card"></i>
                        <span>Pembayaran Obat</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('retur-pembelian.*') ? 'active' : '' }}" href="{{ route('retur-pembelian.index') }}">
                        <i class="bi bi-arrow-return-left"></i>
                        <span>Retur Obat</span>
                    </a>
                </li>
                
                <div class="sidebar-heading">Penjualan</div>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
                        <i class="bi bi-cart"></i>
                        <span>Penjualan Obat</span>
                    </a>
                </li>
                
                <div class="sidebar-heading">Laporan</div>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                
                <hr class="sidebar-divider">
                
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link text-white bg-transparent border-0 w-100 text-start">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
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
                        @if(auth()->check())
                            <span class="fw-bold">{{ auth()->user()->name }}</span>
                        @endif
                    </div>
                    <div class="topbar-divider"></div>
                    <div>
                        <span class="badge bg-primary">{{ ucfirst(auth()->user()->role ?? 'User') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="container-fluid px-0">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <footer class="sticky-footer">
                <div class="container">
                    <div class="text-center">
                        <span>Copyright &copy; Apotek Malabar 2025</span>
                    </div>
                </div>
            </footer>
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
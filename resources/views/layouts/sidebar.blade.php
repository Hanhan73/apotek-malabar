<nav class="bg-dark text-white vh-100" style="width: 250px;">
    <div class="p-3">
        <h4 class="text-center">Apotek Malabar</h4>
        <hr>
        <ul class="nav flex-column">
            <!-- Dashboard - Untuk semua role -->
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            
            @php
                $userRole = auth()->user()->role;
            @endphp

            <!-- Data Master - Hanya Admin -->
            @if($userRole === 'admin')
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
            @endif
            
            <!-- Pembelian - Apoteker -->
            @if($userRole === 'apoteker')
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
            @endif
            
            <!-- Admin Menu - Retur & Pembayaran -->
            @if($userRole === 'admin')
            <li class="nav-item mt-3">
                <span class="nav-link text-muted">ADMIN</span>
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
            
            <!-- Penjualan - Apoteker & Asisten Apoteker -->
            @if(in_array($userRole, ['apoteker', 'asisten_apoteker']))
            <li class="nav-item mt-3">
                <span class="nav-link text-muted">PENJUALAN</span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('penjualan.*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
                    <i class="bi bi-basket me-2"></i> Penjualan Obat
                </a>
            </li>
            @endif
            
            <!-- Laporan - Admin & Pemilik -->
            @if(in_array($userRole, ['admin', 'pemilik']))
            <li class="nav-item mt-3">
                <span class="nav-link text-muted">LAPORAN</span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('laporan.index') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                    <i class="bi bi-file-earmark-text me-2"></i> Laporan Utama
                </a>
            </li>
            @endif
            
            @if($userRole === 'pemilik')
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('laporan.keuangan') ? 'active' : '' }}" href="{{ route('laporan.keuangan') }}">
                    <i class="bi bi-graph-up me-2"></i> Laporan Keuangan
                </a>
            </li>
            @endif

            <!-- Logout -->
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
</nav>
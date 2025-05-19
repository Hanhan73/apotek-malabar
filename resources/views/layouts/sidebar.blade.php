<nav class="bg-dark text-white vh-100" style="width: 250px;">
    <div class="p-3">
        <h4 class="text-center">Apotek Malabar</h4>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            
            <!-- Data Master -->
            <li class="nav-item mt-3">
                <span class="nav-link text-muted">DATA MASTER</span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('obat.index') }}">
                    <i class="bi bi-capsule me-2"></i> Data Obat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('supplier.index') }}">
                    <i class="bi bi-truck me-2"></i> Data Supplier
                </a>
            </li>
            
            <!-- Pembelian -->
            <li class="nav-item mt-3">
                <span class="nav-link text-muted">PEMBELIAN</span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('pembelian.index') }}">
                    <i class="bi bi-cart me-2"></i> Pembelian Obat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('penerimaan-pembelian.index') }}">
                    <i class="bi bi-cart me-2"></i> Penerimaan Obat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('pembayaran-pembelian.index') }}">
                    <i class="bi bi-cart me-2"></i> Pembayaran Obat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('retur-pembelian.index') }}">
                    <i class="bi bi-cart me-2"></i> Retur Obat
                </a>
            </li>
            
            <!-- Penjualan -->
            <li class="nav-item mt-3">
                <span class="nav-link text-muted">PENJUALAN</span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('penjualan.index') }}">
                    <i class="bi bi-cart me-2"></i> Penjualan Obat
                </a>
            </li>
            <!-- Penjualan -->
            <li class="nav-item mt-3">
                <span class="nav-link text-muted">LAPORAN</span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('laporan.index') }}">
                    <i class="bi bi-cart me-2"></i> Penjualan Obat
                </a>
            </li>

            <!-- Logout -->
            <li class="nav-item">
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
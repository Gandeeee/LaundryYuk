<nav class="navbar navbar-expand-lg navbar-light shadow-sm sticky-top" style="background: rgba(255, 255, 255, 0.98);">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('assets/img/logo.png') }}" alt="LaundryYuk Logo" class="brand-logo-img me-2" style="width: 35px;">
            <span class="fs-5 fw-bold" style="color: var(--admin-dark);">LaundryYuk!</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" id="nav-dashboard" href="{{ route('customer.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.order.create') ? 'active' : '' }}" 
                    id="nav-order" 
                    href="{{ route('customer.order.create') }}">
                    Buat Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="nav-history" href="#">Riwayat</a>
                </li>
            </ul>

            <div class="d-flex flex-column flex-lg-row align-items-lg-center">
                <span class="me-lg-3 mb-2 mb-lg-0" style="color: var(--admin-gray);">Hai, {{ Auth::user()->name ?? 'Pelanggan' }}</span>
                <a href="/logout" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </div>
</nav>
<nav class="sidebar" id="adminSidebar">
    <div>
        <a class="sidebar-brand" href="#">
            <img src="{{ asset('assets/img/logo.png') }}" alt="LAUNDRY YUK!" class="brand-logo">
        </a>

        <ul class="nav flex-column" id="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" data-page="dashboard"> 
                    <i class="bi bi-grid-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}" href="{{ route('admin.orders') }}" data-page="orders">
                    <i class="bi bi-card-checklist"></i> Manajemen Order
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.drivers') ? 'active' : '' }}" href="{{ route('admin.drivers') }}" data-page="drivers">
                    <i class="bi bi-truck"></i> Manajemen Driver
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    <span>Laporan</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-person-circle"></i> {{ Auth::user()->name ?? 'Admin' }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="/logout">
                    <i class="bi bi-box-arrow-left" ></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>
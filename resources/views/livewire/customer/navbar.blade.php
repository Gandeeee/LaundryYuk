<nav class="navbar navbar-expand-lg fixed-top shadow-sm"
     style="background-color: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);">

    <div class="container">

        {{-- LOGO --}}
        <a class="navbar-brand d-flex align-items-center gap-2"
           href="{{ route('customer.dashboard') }}">
            <div class="bg-danger bg-opacity-10 rounded-circle p-1 d-flex align-items-center justify-content-center"
                 style="width:40px;height:40px">
                <img src="{{ asset('assets/img/logo.png') }}" style="max-width:24px">
            </div>
            <span class="fw-bold text-danger">LaundryYuk!</span>
        </a>

        {{-- TOGGLER --}}
        <button class="navbar-toggler border-0"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasNavbar">
            <i class="bi bi-list fs-1"></i>
        </button>

        {{-- OFFCANVAS --}}
        <div class="offcanvas offcanvas-start" id="offcanvasNavbar">
            <div class="offcanvas-header border-bottom">
                <h5 class="fw-bold text-danger">Menu</h5>
                <button class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body">

                {{-- MENU --}}
                <ul class="navbar-nav mx-auto gap-2 mb-4 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'fw-bold text-danger' : '' }}"
                           href="{{ route('customer.dashboard') }}">
                            <i class="bi bi-grid me-2 d-lg-none"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.order.create') ? 'fw-bold text-danger' : '' }}"
                           href="{{ route('customer.order.create') }}">
                            <i class="bi bi-plus-circle me-2 d-lg-none"></i> Buat Pesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.history') ? 'fw-bold text-danger' : '' }}"
                           href="{{ route('customer.history') }}">
                            <i class="bi bi-clock-history me-2 d-lg-none"></i> Riwayat
                        </a>
                    </li>
                </ul>

                {{-- ========================= --}}
                {{-- MOBILE : NOTIFIKASI --}}
                {{-- ========================= --}}
                <div class="d-lg-none border-top pt-3">
                    <a href="{{ route('customer.dashboard') }}"
                       class="d-flex align-items-center justify-content-between py-2 text-decoration-none text-dark">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-bell"></i>
                            <span class="fw-bold">Notifikasi</span>
                        </div>

                        @if($notifCount)
                            <span class="badge bg-danger rounded-pill">{{ $notifCount }}</span>
                        @endif
                    </a>

                    <div class="mt-2 ps-4 small text-muted">
                        @forelse($notifications as $notif)
                            <a href="#"
                            wire:click.prevent="markAsRead({{ $notif->id }})"
                            class="d-block py-1 text-decoration-none text-muted">
                                #LD-{{ $notif->id }} —
                                {{ str_replace('_',' ',$notif->status) }}
                            </a>

                        @empty
                            Tidak ada notifikasi
                        @endforelse
                    </div>
                </div>

                {{-- ========================= --}}
                {{-- MOBILE : PROFILE --}}
                {{-- ========================= --}}
                <div class="d-lg-none border-top mt-3 pt-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                             style="width:35px;height:35px">
                            {{ substr(Auth::user()->name,0,1) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ Auth::user()->name }}</div>
                            <div class="small text-muted">Customer</div>
                        </div>
                    </div>

                    <a href="{{ route('logout') }}"
                       class="d-block text-danger small mt-2">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </a>
                </div>

            </div>
        </div>

        {{-- ========================= --}}
        {{-- DESKTOP : NOTIF + PROFILE --}}
        {{-- ========================= --}}
        <div class="d-none d-lg-flex align-items-center gap-3">

            {{-- NOTIFIKASI --}}
            <div class="dropdown">
                <a class="nav-link position-relative px-2"
                   data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5 {{ $notifCount ? 'text-danger' : '' }}"></i>

                    @if($notifCount)
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                            {{ $notifCount }}
                        </span>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow"
                    style="min-width:300px">
                    @forelse($notifications as $notif)
                        <li>
                            <a class="dropdown-item small"
                            wire:click.prevent="markAsRead({{ $notif->id }})"
                            href="#">
                                #LD-{{ $notif->id }} —
                                {{ str_replace('_',' ',$notif->status) }}
                            </a>

                        </li>
                    @empty
                        <li class="dropdown-item text-muted small">
                            Tidak ada notifikasi
                        </li>
                    @endforelse
                </ul>
            </div>

            {{-- PROFILE --}}
            <div class="dropdown">
                <a class="d-flex align-items-center px-2"
                   data-bs-toggle="dropdown">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width:35px;height:35px">
                        {{ substr(Auth::user()->name,0,1) }}
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <a class="btn btn-danger w-100 mt-3"
                           href="{{ route('logout') }}">
                            <i class="bi bi-box-arrow-right me-2"></i> Keluar
                        </a>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</nav>

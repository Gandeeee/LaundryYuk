<nav class="navbar navbar-expand-lg fixed-top shadow-sm transition-all" 
     style="background-color: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);" 
     wire:poll.10s>
    
    <div class="container">
        {{-- 1. LOGO & BRAND (Merah) --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('customer.dashboard') }}">
            <div class="bg-danger bg-opacity-10 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 24px;">
            </div>
            <span class="fw-bold text-danger tracking-tight" style="font-size: 1.1rem;">LaundryYuk!</span>
        </a>

        {{-- 2. TOMBOL HAMBURGER (MOBILE) --}}
        <button class="navbar-toggler border-0 p-0 focus-ring focus-ring-danger" type="button" 
                data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
            <span class="bi bi-list fs-1 text-dark"></span>
        </button>

        {{-- 3. ISI MENU (OFFCANVAS) --}}
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            
            {{-- Header Sidebar (Mobile Only) --}}
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title fw-bold text-danger" id="offcanvasNavbarLabel">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" width="24" class="me-2"> Menu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            {{-- Body Sidebar --}}
            <div class="offcanvas-body">
                
                {{-- LINKS TENGAH --}}
                <ul class="navbar-nav mx-auto gap-2 gap-lg-4 mb-4 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-pill {{ request()->routeIs('customer.dashboard') ? 'active bg-danger bg-opacity-10 text-danger fw-bold' : 'text-secondary hover-bg-light' }}" 
                           href="{{ route('customer.dashboard') }}">
                           <i class="bi bi-grid-fill me-2 d-lg-none"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-pill {{ request()->routeIs('customer.order.create') ? 'active bg-danger bg-opacity-10 text-danger fw-bold' : 'text-secondary hover-bg-light' }}" 
                           href="{{ route('customer.order.create') }}">
                           <i class="bi bi-plus-circle-fill me-2 d-lg-none"></i> Buat Pesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-pill {{ request()->routeIs('customer.history') ? 'active bg-danger bg-opacity-10 text-danger fw-bold' : 'text-secondary hover-bg-light' }}" 
                           href="{{ route('customer.history') }}">
                           <i class="bi bi-clock-history me-2 d-lg-none"></i> Riwayat
                        </a>
                    </li>
                </ul>

                {{-- USER & NOTIFIKASI (KANAN) --}}
                <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3">
                    
                    {{-- Notifikasi --}}
                    <div class="dropdown">
                        <a class="nav-link position-relative text-secondary d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="position-relative">
                                <i class="bi bi-bell fs-5 {{ $notifCount > 0 ? 'text-danger' : '' }}"></i>
                                @if($notifCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.6rem;">
                                        {{ $notifCount }}
                                    </span>
                                @endif
                            </div>
                            <span class="d-lg-none fw-bold">Notifikasi</span> {{-- Teks muncul hanya di mobile --}}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0 overflow-hidden" style="min-width: 300px; max-height: 400px;">
                            <li class="bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-uppercase text-muted">Notifikasi</span>
                                @if($notifCount > 0) <span class="badge bg-danger rounded-pill">{{ $notifCount }} Baru</span> @endif
                            </li>
                            <div class="overflow-auto custom-scrollbar" style="max-height: 300px;">
                                @forelse($notifications as $notif)
                                    <li>
                                        <a class="dropdown-item p-3 border-bottom d-flex align-items-start gap-3 hover-bg-light" href="{{ route('customer.dashboard') }}">
                                            <div class="shrink-0">
                                                @if($notif->status == 'MENUNGGU_PEMBAYARAN') <i class="bi bi-wallet2 text-warning fs-5"></i>
                                                @elseif(in_array($notif->status, ['SELESAI_DICUCI', 'TIBA'])) <i class="bi bi-check-circle text-success fs-5"></i>
                                                @else <i class="bi bi-info-circle text-primary fs-5"></i> @endif
                                            </div>
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="fw-bold text-dark">#LD-{{ $notif->id }}</small>
                                                    <small class="text-muted" style="font-size: 0.65rem;">{{ $notif->updated_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-0 small text-secondary lh-sm">{{ str_replace('_', ' ', $notif->status) }}</p>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="p-4 text-center text-muted small">Tidak ada notifikasi baru</li>
                                @endforelse
                            </div>
                        </ul>
                    </div>

                    {{-- User Profile --}}
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark gap-2 p-1 rounded hover-bg-light" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 35px; height: 35px;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="text-start" style="line-height: 1.2;">
                                <small class="d-block fw-bold">{{ Auth::user()->name }}</small>
                                <small class="d-block text-muted" style="font-size: 0.65rem;">Customer</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2" style="border-radius: 12px; min-width: 200px;">
                            <li>
                                <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2 text-danger fw-bold" href="{{ route('logout') }}">
                                    <i class="bi bi-box-arrow-right"></i> Keluar
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <audio id="custAudio" src="{{ asset('assets/audio/notif.MP3') }}" preload="auto"></audio>
</nav>

{{-- SCRIPT AUDIO --}}
@script
<script>
    Livewire.on('play-notification-sound', () => {
        const audio = document.getElementById('custAudio');
        if (audio) {
            audio.play().catch(e => console.log("Audio autoplay blocked"));
        }
    });
</script>
@endscript
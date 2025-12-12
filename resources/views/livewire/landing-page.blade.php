<div>
    {{-- 1. Push CSS & Font Awesome khusus Landing Page --}}
    @push('styles')
        <link href="{{ asset('assets/css/landing.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush

    {{-- 2. Navbar (Spesifik Landing Page) --}}
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logo.png') }}" alt="LaundryYuk Logo" class="brand-logo-img">
                <span class="fs-5 fw-bold text-dark">LaundryYuk</span>
            </a>
            <div class="d-flex">
                @auth
                    {{-- Jika sudah login, arahkan ke Dashboard --}}
                    @if(Auth::user()->role === 'admin')
                        <a class="nav-link btn btn-danger text-white btn-sm px-3" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Ke Dashboard
                        </a>
                    @else
                        <a class="nav-link btn btn-danger text-white btn-sm px-3" href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-user-circle me-2"></i>Ke Dashboard
                        </a>
                    @endif
                @else
                    {{-- Jika belum login --}}
                    <a class="nav-link btn btn-danger text-white btn-sm px-3" href="{{ route('login') }}">
                        <i class="fas fa-user-circle me-2"></i>Login / Daftar
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- 3. Hero Section --}}
    <header class="hero-section" style="background-image: linear-gradient(135deg, rgba(31, 41, 55, 0.85) 0%, rgba(31, 41, 55, 0.75) 100%), url('{{ asset('assets/img/landing-bg.png') }}');">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Solusi Laundry Cepat, Bersih, dan Terlacak.</h1>
            <p class="lead my-4">Nikmati layanan antar jemput gratis dan pantau status cucianmu kapan saja, di mana saja.</p>
            <a class="btn btn-danger btn-lg px-4 py-3 fw-bold" href="{{ route('login') }}" role="button">
                <i class="fas fa-shopping-cart me-2"></i>Pesan Layanan Sekarang
            </a>
        </div>
    </header>

    {{-- 4. Layanan Kami --}}
    <section id="layanan">
        <div class="container">
            <h2 class="text-center section-title">Layanan Unggulan Kami</h2>
            <div class="row g-4 justify-content-center">
                {{-- Card 1 --}}
                <div class="col-md-4 d-flex">
                    <div class="card shadow-sm border-0 w-100">
                        <div class="card-body p-4 text-center">
                            <div class="card-icon">
                                <i class="fas fa-tshirt"></i>
                            </div>
                            <h5 class="card-title fw-bold">Cuci Kiloan & Satuan</h5>
                            <p class="card-text">Layanan cuci bersih standar dan premium untuk pakaian harian, jas, dan lainnya dengan hasil higienis.</p>
                        </div>
                    </div>
                </div>
                {{-- Card 2 --}}
                <div class="col-md-4 d-flex">
                    <div class="card shadow-sm border-0 w-100">
                        <div class="card-body p-4 text-center">
                            <div class="card-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h5 class="card-title fw-bold">Layanan Antar Jemput</h5>
                            <p class="card-text">Driver kami akan menjemput dan mengantar pakaian kotormu langsung di depan pintu. Hemat waktu dan tenaga.</p>
                        </div>
                    </div>
                </div>
                {{-- Card 3 --}}
                <div class="col-md-4 d-flex">
                    <div class="card shadow-sm border-0 w-100">
                        <div class="card-body p-4 text-center">
                            <div class="card-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h5 class="card-title fw-bold">Pelacakan Digital</h5>
                            <p class="card-text">Pantau status cucianmu secara real-time mulai dari dijemput, dicuci, hingga diantar kembali melalui aplikasi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. Footer --}}
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 LaundryYuk - Kelompok 3. All rights reserved.</p>
        </div>
    </footer>
</div>
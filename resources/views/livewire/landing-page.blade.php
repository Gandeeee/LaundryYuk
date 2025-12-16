<div>
    {{-- 1. Push CSS & Font Awesome --}}
    @push('styles')
        {{-- Menggunakan FontAwesome 6 (CDN Stabil) --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link href="{{ asset('assets/css/landing.css') }}" rel="stylesheet">
        {{-- Google Fonts: Poppins --}}
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    @endpush

    {{-- 2. Navbar Modern --}}
    <nav class="navbar navbar-expand-lg fixed-top navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logo.png') }}" alt="LaundryYuk Logo" style="height: 40px; width: auto;">
                <span class="fs-4 fw-bold text-danger">LaundryYuk</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item"><a class="nav-link fw-medium" href="#layanan">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="#cara-kerja">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="#harga">Harga</a></li>
                    
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="btn btn-danger btn-sm px-4 rounded-pill fw-bold" href="{{ route('admin.dashboard') }}">
                                    Dashboard Admin <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="btn btn-danger btn-sm px-4 rounded-pill fw-bold" href="{{ route('customer.dashboard') }}">
                                    Dashboard Saya <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="btn btn-outline-danger btn-sm px-4 rounded-pill fw-bold" href="{{ route('login') }}">
                                Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-danger btn-sm px-4 rounded-pill fw-bold" href="{{ route('register') }}">
                                Daftar Gratis
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- 3. Hero Section --}}
    <header class="hero-section d-flex align-items-center position-relative" 
            style="background-image: linear-gradient(135deg, rgba(220, 53, 69, 0.9) 0%, rgba(33, 37, 41, 0.8) 100%), url('{{ asset('assets/img/landing-bg.png') }}');">
        
        <div class="container position-relative z-2 text-white">
            <div class="row align-items-center py-5">
                <div class="col-lg-7 text-center text-lg-start">
                    <span class="badge bg-white text-danger px-3 py-2 rounded-pill mb-3 fw-bold animate-up">
                        <i class="fas fa-star me-1"></i> Laundry #1 Terpercaya
                    </span>
                    <h1 class="display-3 fw-bold mb-3 lh-sm animate-up" style="animation-delay: 0.1s;">
                        Solusi Laundry <br> <span class="text-warning">Cepat & Bersih</span>
                    </h1>
                    <p class="lead mb-4 opacity-75 animate-up" style="animation-delay: 0.2s;">
                        Tidak perlu repot keluar rumah. Kami jemput pakaian kotor Anda, cuci hingga bersih wangi, dan antar kembali ke depan pintu.
                    </p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start animate-up" style="animation-delay: 0.3s;">
                        <a class="btn btn-light text-danger btn-lg px-5 py-3 rounded-pill fw-bold shadow" href="{{ route('register') }}">
                            Pesan Sekarang <i class="fas fa-paper-plane ms-2"></i>
                        </a>
                        <a class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill fw-bold" href="#cara-kerja">
                            Pelajari Cara Kerja
                        </a>
                    </div>
                </div>
                {{-- Ilustrasi Statistik --}}
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="bg-white text-dark p-4 rounded-4 shadow-lg mb-3 text-center floating-card">
                                <h2 class="fw-bold text-danger mb-0">1.5K+</h2>
                                <small class="text-muted fw-bold">Pelanggan Puas</small>
                            </div>
                            <div class="bg-white text-dark p-4 rounded-4 shadow-lg text-center floating-card" style="animation-delay: 1s;">
                                <h2 class="fw-bold text-success mb-0">5.0 Rating</h2>
                                <small class="text-muted fw-bold">Google Maps</small>
                            </div>
                        </div>
                        <div class="col-6 mt-5">
                            <div class="bg-white text-dark p-4 rounded-4 shadow-lg text-center floating-card" style="animation-delay: 0.5s;">
                                <h2 class="fw-bold text-primary mb-0">100%</h2>
                                <small class="text-muted fw-bold">Higienis & Wangi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </header>

    {{-- 4. Cara Kerja --}}
    <section id="cara-kerja" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="text-danger fw-bold text-uppercase ls-2">Proses Kami</span>
                <h2 class="fw-bold mt-2">Mudah, Cuma 4 Langkah</h2>
            </div>
            
            <div class="row g-4 text-center position-relative">
                {{-- Step 1 --}}
                <div class="col-lg-3 col-md-6 position-relative z-1">
                    <div class="bg-white p-3 d-inline-block">
                        <div class="step-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 70px; height: 70px; font-size: 1.5rem;">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h5 class="fw-bold">1. Pesan via Web</h5>
                        <p class="text-muted small">Login dan buat pesanan penjemputan dengan mudah.</p>
                    </div>
                </div>
                {{-- Step 2 --}}
                <div class="col-lg-3 col-md-6 position-relative z-1">
                    <div class="bg-white p-3 d-inline-block">
                        <div class="step-icon bg-white text-danger border border-2 border-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 70px; height: 70px; font-size: 1.5rem;">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h5 class="fw-bold">2. Kami Jemput</h5>
                        <p class="text-muted small">Kurir kami akan datang ke lokasi Anda sesuai jadwal.</p>
                    </div>
                </div>
                {{-- Step 3 --}}
                <div class="col-lg-3 col-md-6 position-relative z-1">
                    <div class="bg-white p-3 d-inline-block">
                        <div class="step-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 70px; height: 70px; font-size: 1.5rem;">
                            <i class="fas fa-soap"></i>
                        </div>
                        <h5 class="fw-bold">3. Proses Cuci</h5>
                        <p class="text-muted small">Dicuci, dikeringkan, dan disetrika dengan hati-hati.</p>
                    </div>
                </div>
                {{-- Step 4 --}}
                <div class="col-lg-3 col-md-6 position-relative z-1">
                    <div class="bg-white p-3 d-inline-block">
                        <div class="step-icon bg-white text-danger border border-2 border-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 70px; height: 70px; font-size: 1.5rem;">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h5 class="fw-bold">4. Antar Kembali</h5>
                        <p class="text-muted small">Pakaian bersih wangi diantar kembali ke rumah Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. Layanan Kami (REVISI IKON) --}}
    {{-- 5. Layanan Kami (FIX WARNA & IKON) --}}
    <section id="layanan" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="text-danger fw-bold text-uppercase ls-2">Service</span>
                <h2 class="fw-bold mt-2">Layanan Unggulan</h2>
            </div>
            
            <div class="row g-4 justify-content-center">
                {{-- Card 1: CUCI KOMPLIT --}}
                <div class="col-md-4">
                    <div class="card service-card h-100 border-0 shadow-sm rounded-4 p-3">
                        <div class="card-body text-center">
                            {{-- SOLUSI: Ganti style background jadi Putih + Border Merah agar Ikon Merah terlihat --}}
                            <div class="icon-circle bg-white text-danger border border-2 border-danger rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                {{-- Gunakan fa-tshirt (versi umum) --}}
                                <i class="fa-solid fa-spray-can-sparkles"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Cuci Komplit</h4>
                            <p class="text-muted mb-4">Layanan cuci, kering, dan setrika rapi. Cocok untuk pakaian sehari-hari yang ingin langsung siap pakai.</p>
                            <span class="text-danger fw-bold small">Mulai Rp 5.000 /kg</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2: EXPRESS (Ini sudah jalan, jangan diubah warnanya) --}}
                <div class="col-md-4">
                    <div class="card service-card h-100 border-0 shadow rounded-4 p-3 position-relative overflow-hidden">
                        <div class="position-absolute top-0 end-0 bg-warning text-dark fw-bold px-3 py-1 rounded-bottom-start small">POPULER</div>
                        <div class="card-body text-center">
                            <div class="icon-circle bg-danger text-white rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Express 3 Jam</h4>
                            <p class="text-muted mb-4">Butuh cepat? Layanan kilat kami menjamin pakaian bersih dan wangi hanya dalam waktu 3 jam.</p>
                            <span class="text-danger fw-bold small">Mulai Rp 10.000 /kg</span>
                        </div>
                    </div>
                </div>

                {{-- Card 3: CUCI SATUAN --}}
                <div class="col-md-4">
                    <div class="card service-card h-100 border-0 shadow-sm rounded-4 p-3">
                        <div class="card-body text-center">
                            {{-- SOLUSI: Samakan style dengan Card 1 (Putih + Border) --}}
                            <div class="icon-circle bg-white text-danger border border-2 border-danger rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                <i class="fa-solid fa-socks"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Cuci Satuan</h4>
                            <p class="text-muted mb-4">Perawatan khusus untuk Bedcover, Boneka, Tas, Sepatu, dan Jas agar tidak rusak.</p>
                            <span class="text-danger fw-bold small">Mulai Rp 15.000 /pcs</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. Pricing / Harga --}}
    <section id="harga" class="py-5 bg-white">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <span class="text-danger fw-bold text-uppercase ls-2">Daftar Harga</span>
                    <h2 class="display-5 fw-bold mb-4">Harga Hemat, <br>Kualitas Hebat</h2>
                    <p class="text-muted lead mb-4">Kami menawarkan harga yang transparan tanpa biaya tersembunyi. Pilih layanan yang sesuai dengan kebutuhan Anda.</p>
                    <a href="{{ asset('assets/pdf/pricelist.pdf') }}" 
                       target="_blank" 
                       class="btn btn-dark rounded-pill px-4 py-3 fw-bold">
                        <i class="fas fa-file-pdf me-2 text-danger"></i> Lihat Katalog Lengkap
                    </a>
                </div>
                <div class="col-lg-7">
                    <div class="table-responsive shadow-sm rounded-4 border">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-danger text-white">
                                <tr>
                                    <th class="p-4 border-0">Layanan</th>
                                    <th class="p-4 border-0">Estimasi</th>
                                    <th class="p-4 border-0 text-end">Dimulai dari</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="p-4 fw-bold"><i class="fas fa-shirt text-danger me-2"></i> Cuci Kering Setrika</td>
                                    <td class="p-4 text-muted">2-4 Hari</td>
                                    <td class="p-4 text-end fw-bold">Rp 6.000 /kg</td>
                                </tr>
                                <tr>
                                    <td class="p-4 fw-bold"><i class="fas fa-wind text-danger me-2"></i> Cuci Kering Lipat</td>
                                    <td class="p-4 text-muted">2 Hari</td>
                                    <td class="p-4 text-end fw-bold">Rp 5.500/kg</td>
                                </tr>
                                <tr>
                                    <td class="p-4 fw-bold"><i class="fas fa-bolt text-warning me-2"></i> Cuci Setrika Express</td>
                                    <td class="p-4 text-muted">6 Jam</td>
                                    <td class="p-4 text-end fw-bold">Rp 10.000 /kg</td>
                                </tr>
                                <tr>
                                    <td class="p-4 fw-bold"><i class="fas fa-layer-group text-primary me-2"></i> Bedcover (Satuan)</td>
                                    <td class="p-4 text-muted">3 Hari</td>
                                    <td class="p-4 text-end fw-bold">Rp 25.000 /pcs</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. Call To Action --}}
    <section class="py-5 bg-danger text-white text-center">
        <div class="container py-4">
            <h2 class="fw-bold mb-3">Cucian Menumpuk? Jangan Pusing!</h2>
            <p class="lead mb-4 opacity-75">Bergabunglah dengan ribuan pelanggan yang telah mempercayakan pakaian mereka kepada LaundryYuk.</p>
            <a href="{{ route('register') }}" class="btn btn-light text-danger btn-lg rounded-pill px-5 py-3 fw-bold shadow">
                Mulai Laundry Sekarang
            </a>
        </div>
    </section>

    {{-- 8. Footer --}}
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row g-4 mb-4">
                {{-- Brand --}}
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="LaundryYuk Logo" style="height: 35px; width: auto;">
                        <span class="fs-4 fw-bold">LaundryYuk</span>
                    </div>
                    <p class="text-secondary small">Solusi laundry modern berbasis aplikasi. Kami mengutamakan kebersihan, kecepatan, dan kenyamanan pelanggan.</p>
                </div>
                
                {{-- Links --}}
                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-bold mb-3">Tautan</h5>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary hover-text-white">Beranda</a></li>
                        <li class="mb-2"><a href="#layanan" class="text-decoration-none text-secondary hover-text-white">Layanan</a></li>
                        <li class="mb-2"><a href="#harga" class="text-decoration-none text-secondary hover-text-white">Harga</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}" class="text-decoration-none text-secondary hover-text-white">Masuk</a></li>
                    </ul>
                </div>
                
                {{-- Kontak --}}
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3">Hubungi Kami</h5>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-danger"></i> Jl. Raya Jetis Kulon I No.66B, Wonokromo, Kec. Wonokromo, Surabaya</li>
                        <li class="mb-2"><i class="fas fa-whatsapp me-2 text-danger"></i> 0899-7990-809</li>
                        <li class="mb-2"><i class="fas fa-regular fa-clock me-2 text-danger"></i> 08.00 WIB - 22.00 WIB</li>
                    </ul>
                </div>

            </div>
            
            <hr class="border-secondary">
            <div class="text-center pt-3">
                <small class="text-secondary">&copy; {{ date('Y') }} LaundryYuk - Kelompok 3. All rights reserved.</small>
            </div>
        </div>
    </footer>
</div>
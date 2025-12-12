<div>
    @push('styles')
        <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
        
        {{-- CSS KHUSUS UNTUK MEMPERCANTIK LAPORAN --}}
        <style>
            /* 1. Border Radius Lebih Lembut */
            .rounded-xl {
                border-radius: 16px !important;
            }

            /* 2. Efek Hover Card (Naik + Bayangan) */
            .stat-card-hover {
                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                border: 1px solid rgba(0,0,0,0.03);
                background: white;
            }
            .stat-card-hover:hover {
                transform: translateY(-5px); /* Efek Naik */
                box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; /* Bayangan Lembut */
                border-color: transparent;
            }

            /* 3. Icon Box Cantik */
            .icon-shape {
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                font-size: 1.5rem;
                margin-bottom: 1rem;
                transition: all 0.3s ease;
            }
            /* Warna Background Icon Soft */
            .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
            .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
            .bg-soft-dark { background-color: rgba(33, 37, 41, 0.1); color: #212529; }
            .bg-soft-warning { background-color: rgba(255, 193, 7, 0.15); color: #856404; }

            /* Efek Hover Icon: Icon ikut membesar sedikit */
            .stat-card-hover:hover .icon-shape {
                transform: scale(1.1);
            }
        </style>
    @endpush

    <x-admin-sidebar />
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <main class="content">
        <livewire:admin.navbar title="Laporan & Statistik" />

        <div class="page-content active">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">Ringkasan Bisnis</h4>
                    <p class="text-muted mb-0">Analisis performa laundry tahun {{ date('Y') }}</p>
                </div>
                {{-- TOMBOL EXPORT --}}
                <button class="btn btn-outline-success fw-bold py-2" wire:click="exportCsv">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export CSV
                </button>
            </div>

            {{-- 1. KARTU STATISTIK RINGKAS (MODERN STYLE) --}}
            <div class="row g-4 mb-5">
                {{-- Total Pendapatan --}}
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 rounded-xl stat-card-hover">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Pendapatan</h6>
                                    <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                                </div>
                                <div class="icon-shape bg-soft-success">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="badge bg-soft-success rounded-pill px-2">LUNAS</span>
                                <small class="text-muted ms-2">Akumulasi tahunan</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Order --}}
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 rounded-xl stat-card-hover">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Order</h6>
                                    <h3 class="fw-bold text-dark mb-0">{{ $totalOrders }}</h3>
                                </div>
                                <div class="icon-shape bg-soft-primary">
                                    <i class="bi bi-basket2-fill"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">Semua transaksi masuk</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Selesai --}}
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 rounded-xl stat-card-hover">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Selesai Dicuci</h6>
                                    <h3 class="fw-bold text-dark mb-0">{{ $completedOrders }}</h3>
                                </div>
                                <div class="icon-shape bg-soft-dark">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">Pesanan rampung</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Aktif --}}
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 rounded-xl stat-card-hover">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Dalam Proses</h6>
                                    <h3 class="fw-bold text-dark mb-0">{{ $activeOrders }}</h3>
                                </div>
                                <div class="icon-shape bg-soft-warning">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted text-warning fw-bold">Perlu tindakan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. GRAFIK / CHARTS --}}
            <div class="row g-4">
                {{-- Grafik Pendapatan --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100 rounded-xl">
                        <div class="card-header bg-white py-3 border-0 rounded-top-xl">
                            <div class="d-flex align-items-center">
                                <div class="icon-shape bg-soft-success me-3 mb-0" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">Grafik Pendapatan Bulanan</h6>
                            </div>
                        </div>
                        
                        {{-- TAMBAHKAN wire:ignore DI SINI --}}
                        {{-- Ini mencegah Livewire mereset canvas saat tombol export diklik --}}
                        <div class="card-body pt-0" wire:ignore>
                            <canvas id="revenueChart" style="height: 350px;"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Grafik Tren Order --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-xl">
                        <div class="card-header bg-white py-3 border-0 rounded-top-xl">
                            <div class="d-flex align-items-center">
                                <div class="icon-shape bg-soft-primary me-3 mb-0" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">Tren Order</h6>
                            </div>
                        </div>

                        {{-- TAMBAHKAN wire:ignore DI SINI JUGA --}}
                        <div class="card-body pt-0" wire:ignore>
                            <canvas id="orderChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.getElementById('sidebarToggleBtn')?.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-toggled');
        });
        document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
            document.body.classList.remove('sidebar-toggled');
        });

        document.addEventListener('livewire:initialized', () => {
            
            const labels = {!! $chartLabels !!};
            const revenueData = {!! $chartRevenue !!};
            const orderData = {!! $chartOrders !!};

            // Chart Pendapatan (Bar dengan Rounded Top)
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctxRevenue, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: revenueData,
                        backgroundColor: '#198754',
                        borderRadius: 8, // Bar Chart melengkung
                        borderSkipped: false, // Melengkung di semua sisi (opsional)
                        barThickness: 25
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 4], color: '#f0f0f0' }, // Grid putus-putus halus
                            ticks: {
                                callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); },
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Chart Order (Line Smooth)
            const ctxOrder = document.getElementById('orderChart').getContext('2d');
            
            // Buat Gradient untuk Line Chart
            let gradient = ctxOrder.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(13, 110, 253, 0.3)');
            gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

            new Chart(ctxOrder, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Order',
                        data: orderData,
                        borderColor: '#0d6efd',
                        backgroundColor: gradient, // Pakai gradient
                        borderWidth: 2,
                        tension: 0.4, // Melengkung halus (smooth curve)
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0d6efd',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 4], color: '#f0f0f0' },
                            ticks: { stepSize: 1 }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
@endpush
<div>
    {{-- Push CSS Admin --}}
    @push('styles')
        <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
    @endpush

    {{-- 1. Panggil Sidebar Component --}}
    <x-admin-sidebar />

    {{-- Overlay untuk Mobile (Sesuai admin.js asli) --}}
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    {{-- 2. Konten Utama --}}
    <main class="content">
        
        {{-- Top Navbar --}}
        <livewire:admin.navbar title="Dashboard" />
        
        <div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>

        <div class="page-content active">
            
            {{-- Kartu Statistik --}}
            <div class="dashboard-stats" id="dashboard-stats-cards">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $totalOrders }}</h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                        <p>Total Pendapatan (Lunas)</p>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $ordersPerluProses }}</h3>
                        <p>Pesanan Perlu Diproses</p>
                    </div>
                </div>
            </div>
            
            {{-- Tabel Ringkasan Order --}}
            <div class="card-modern">
                <div class="card-header-modern">
                    <h5 class="mb-0">Ringkasan Order Terbaru</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Pelanggan</th>
                                <th>Status Terkini</th>
                                <th class="text-end">Total(Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td><strong>#LD-{{ $order->id }}</strong></td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ str_replace('_', ' ', $order->status) }}</span>
                                    </td>
                                    <td class="text-end fw-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center p-4 text-muted">Belum ada pesanan masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- {{-- Grafik (Placeholder untuk UI) --}}
            <div class="row mt-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="card card-modern h-100">
                        <div class="card-header-modern">
                            <h5 class="mb-0"><i class="bi bi-graph-up me-2" style="color: #0d6efd;"></i> Tren Order</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-modern" style="min-height: 350px;">
                                <canvas id="trenOrderChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-modern h-100">
                        <div class="card-header-modern">
                            <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2" style="color: #198754;"></i> Pendapatan</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-modern" style="min-height: 350px;">
                                <canvas id="pendapatanChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            {{-- GRAFIK / CHARTS --}}
            <div class="row g-4">
                {{-- Grafik Tren Order --}}
                <div class="col-lg-6">
                    <div class="card card-modern h-100">
                        <div class="card-header-modern">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up text-primary me-2"></i> Tren Order Bulanan</h6>
                        </div>
                        <div class="card-body">
                            {{-- wire:ignore mencegah refresh chart saat polling --}}
                            <div class="chart-container" style="height: 300px;" wire:ignore>
                                <canvas id="trenOrderChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Grafik Pendapatan --}}
                <div class="col-lg-6">
                    <div class="card card-modern h-100">
                        <div class="card-header-modern">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill text-success me-2"></i> Pendapatan Bulanan</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height: 300px;" wire:ignore>
                                <canvas id="pendapatanChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

{{-- SCRIPT KHUSUS DASHBOARD --}}
@push('scripts')
    {{-- Load Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        // Toggle Sidebar
        document.getElementById('sidebarToggleBtn')?.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-toggled');
        });
        document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
            document.body.classList.remove('sidebar-toggled');
        });

        // INI BAGIAN PENTING: Mengambil Data Real dari PHP
        document.addEventListener('livewire:initialized', () => {
            
            // 1. Ambil Data JSON dari Controller
            const labels = {!! $chartLabels !!}; 
            const revenueData = {!! $chartRevenue !!};
            const orderData = {!! $chartOrders !!};

            // 2. Chart Tren Order (Line Chart dengan Gradient)
            const trenCtx = document.getElementById('trenOrderChart').getContext('2d');
            
            // Buat Gradient Biru
            let gradientBlue = trenCtx.createLinearGradient(0, 0, 0, 400);
            gradientBlue.addColorStop(0, 'rgba(13, 110, 253, 0.4)');
            gradientBlue.addColorStop(1, 'rgba(13, 110, 253, 0)');

            new Chart(trenCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Order',
                        data: orderData, // DATA REAL
                        borderColor: '#0d6efd',
                        backgroundColor: gradientBlue,
                        fill: true,
                        tension: 0.4, // Garis Melengkung
                        pointRadius: 4,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: { stepSize: 1 }, // Angka bulat (order tidak mungkin desimal)
                            grid: { borderDash: [2, 4] }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 3. Chart Pendapatan (Bar Chart Rounded)
            const pendCtx = document.getElementById('pendapatanChart').getContext('2d');
            new Chart(pendCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: revenueData, // DATA REAL
                        backgroundColor: '#198754',
                        borderRadius: 6,
                        barThickness: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 4] },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
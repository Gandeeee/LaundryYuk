<div class="d-flex flex-column min-vh-100" wire:poll.5s>
    @push('styles')
        <link href="{{ asset('assets/css/customer.css') }}" rel="stylesheet">
    @endpush

    {{-- Navbar Customer (Livewire) --}}
    <livewire:customer.navbar />

    <div class="container mt-5 pt-5 mb-5 grow">

        {{-- Alert Sukses --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Dashboard Stats --}}
        <div id="dashboard-page" class="page-section">
            <h2 class="page-title mb-4">Dashboard Saya</h2>
            
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card stat-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="stat-icon bg-warning me-4"><i class="bi bi-arrow-repeat"></i></div>
                            <div>
                                <h5 class="card-title">Pesanan Diproses</h5>
                                <p class="card-value">{{ $prosesCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card stat-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="stat-icon bg-success me-4"><i class="bi bi-patch-check-fill"></i></div>
                            <div>
                                <h5 class="card-title">Total Pesanan Selesai</h5>
                                <p class="card-value">{{ $selesaiCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3 class="page-subtitle mt-4 mb-3">Ringkasan Orderku</h3>
            <div class="card">
                <div class="card-body p-3 p-md-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Tagihan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myOrders as $order)
                                    <tr>
                                        <th scope="row">#LD-{{ $order->id }}</th>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>
                                            {{-- Warna Status Konsisten --}}
                                            @php
                                                $statusColor = 'bg-secondary';
                                                switch($order->status) {
                                                    case 'MENUNGGU_DIJEMPUT': $statusColor = 'bg-primary'; break;
                                                    case 'DRIVER_OTW': $statusColor = 'bg-info text-dark'; break;
                                                    case 'CUCIAN_DIAMBIL': $statusColor = 'bg-info text-dark'; break;
                                                    case 'MENUNGGU_PEMBAYARAN': $statusColor = 'bg-warning text-dark'; break;
                                                    case 'PROSES_PENCUCIAN': $statusColor = 'bg-primary'; break;
                                                    case 'SELESAI_DICUCI': $statusColor = 'bg-dark'; break;
                                                    case 'DIKIRIM': $statusColor = 'bg-info text-dark'; break;
                                                    case 'TIBA': $statusColor = 'bg-success'; break;
                                                    case 'DIBATALKAN': $statusColor = 'bg-danger'; break;
                                                }
                                            @endphp
                                            
                                            <span class="badge {{ $statusColor }}">
                                                {{ str_replace('_', ' ', $order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($order->total_price > 0)
                                                <span class="fw-bold text-success">Rp {{ number_format($order->total_price) }}</span>
                                            @else
                                                <span class="text-muted small fst-italic">Menunggu ditimbang...</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('invoice.print', $order->id) }}" target="_blank" 
                                                class="btn btn-sm btn-outline-dark me-1" 
                                                title="Cetak Nota">
                                                <i class="bi bi-printer"></i>
                                            </a>

                                            <button class="btn btn-sm btn-outline-secondary me-1" 
                                                    wire:click="showDetail({{ $order->id }})"
                                                    data-bs-toggle="modal" data-bs-target="#detailModal">
                                                Detail
                                            </button>

                                            @if($order->status == 'MENUNGGU_PEMBAYARAN' && !$order->payment_proof)
                                                <button class="btn btn-sm btn-danger" 
                                                        wire:click="openPaymentModal({{ $order->id }})"
                                                        data-bs-toggle="modal" data-bs-target="#uploadPaymentModal">
                                                    Bayar
                                                </button>
                                            @elseif($order->status == 'MENUNGGU_PEMBAYARAN' && $order->payment_proof)
                                                <span class="badge text-dark">Sedang diverifikasi mohon ditunggu</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Belum ada riwayat pesanan. Yuk buat pesanan baru!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 LaundryYuk. All rights reserved.</p>
        </div>
    </footer>

    {{-- MODAL 1: PEMBAYARAN & UPLOAD BUKTI (REVISI BARU) --}}
    <div wire:ignore.self class="modal fade" id="uploadPaymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="savePayment">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Pembayaran Tagihan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body text-center">
                        @if($selectedOrder)
                            {{-- 1. NOMINAL TAGIHAN --}}
                            <p class="text-muted mb-1">Total yang harus dibayar:</p>
                            <h2 class="fw-bold text-danger mb-4">Rp {{ number_format($selectedOrder->total_price, 0, ',', '.') }}</h2>

                            {{-- 2. GAMBAR QRIS --}}
                            <div class="border rounded p-3 d-inline-block mb-3 bg-light">
                                <img src="{{ asset('assets/img/qris.jpeg') }}" alt="Scan QRIS" class="img-fluid" style="max-width: 200px;">
                                <small class="d-block text-muted mt-2">Scan QRIS ini</small>
                            </div>

                            {{-- 3. INFO REKENING (Opsional) --}}
                            <div class="alert alert-light border small text-start mb-4">
                                <i class="bi bi-bank me-1"></i> Atau transfer manual ke: <br>
                                <strong>BCA 1234567890 (LaundryYuk)</strong>
                            </div>

                            <hr>

                            {{-- 4. FORM UPLOAD --}}
                            <div class="text-start">
                                <label class="form-label fw-bold">Upload Bukti Transfer</label>
                                <input type="file" class="form-control" wire:model="payment_proof" accept="image/*" required>
                                
                                {{-- Loading State saat pilih file --}}
                                <div wire:loading wire:target="payment_proof" class="text-info small mt-1">
                                    <span class="spinner-border spinner-border-sm" role="status"></span> Sedang memproses gambar...
                                </div>
                                
                                @error('payment_proof') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Preview Gambar Upload --}}
                            @if ($payment_proof)
                                <div class="mt-3 text-start">
                                    <small class="text-muted">Preview:</small>
                                    <div class="mt-1">
                                        <img src="{{ $payment_proof->temporaryUrl() }}" class="img-fluid rounded border" style="max-height: 100px">
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="py-4"><div class="spinner-border text-primary"></div></div>
                        @endif
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger w-100" wire:loading.attr="disabled" wire:target="savePayment">
                            <span wire:loading.remove wire:target="savePayment">Konfirmasi & Kirim Bukti</span>
                            <span wire:loading wire:target="savePayment">Mengupload...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    
    {{-- MODAL 2: DETAIL PESANAN --}}
    <div wire:ignore.self class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
         <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Detail Pesanan</h5>
                    {{-- Tombol Close Pakai ID Khusus untuk Handle JS --}}
                    <button type="button" class="btn-close" id="btnCloseDetailModal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($selectedOrder)
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-primary mb-2">#LD-{{ $selectedOrder->id }}</h4>
                            {{-- LOGIC WARNA BADGE --}}
                            @php
                                $statusColor = 'bg-secondary';
                                switch($selectedOrder->status) {
                                    case 'MENUNGGU_DIJEMPUT': $statusColor = 'bg-primary'; break;
                                    case 'DRIVER_OTW': $statusColor = 'bg-info text-dark'; break;
                                    case 'CUCIAN_DIAMBIL': $statusColor = 'bg-info text-dark'; break;
                                    case 'MENUNGGU_PEMBAYARAN': $statusColor = 'bg-warning text-dark'; break;
                                    case 'PROSES_PENCUCIAN': $statusColor = 'bg-primary'; break;
                                    case 'SELESAI_DICUCI': $statusColor = 'bg-dark'; break;
                                    case 'DIKIRIM': $statusColor = 'bg-info text-dark'; break;
                                    case 'TIBA': $statusColor = 'bg-success'; break;
                                    case 'DIBATALKAN': $statusColor = 'bg-danger'; break;
                                }
                            @endphp
                            
                            <span class="badge {{ $statusColor }} fs-6 px-3 py-2 rounded-pill">
                                {{ str_replace('_', ' ', $selectedOrder->status) }}
                            </span>
                        </div>

                        {{-- INFO UTAMA --}}
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Layanan</small>
                                        <strong>{{ $selectedOrder->service_type }}</strong>
                                    </div>
                                    <div class="col-6 text-end">
                                        <small class="text-muted d-block">Berat / Jumlah</small>
                                        @if($selectedOrder->total_weight > 0)
                                            <strong>{{ $selectedOrder->total_weight }} (Kg/Pcs)</strong>
                                        @else
                                            <span class="fst-italic text-muted">-</span>
                                        @endif
                                    </div>
                                    <div class="col-12 mt-3 border-top pt-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">Total Tagihan</span>
                                            @if($selectedOrder->total_price > 0)
                                                <span class="fw-bold text-success fs-5">Rp {{ number_format($selectedOrder->total_price) }}</span>
                                            @else
                                                <span class="text-muted fst-italic">Menunggu ditimbang</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TIMELINE PROGRES LENGKAP --}}
                        <div class="timeline p-3 border rounded">
                            <small class="text-muted d-block mb-3 fw-bold text-uppercase" style="font-size: 0.7rem;">Status Perjalanan</small>
                            
                            <ul class="list-unstyled mb-0 ps-2">
                                @php
                                    // DAFTAR URUTAN STATUS LENGKAP
                                    $allStatuses = [
                                        'MENUNGGU_DIJEMPUT', 
                                        'DRIVER_OTW', 
                                        'CUCIAN_DIAMBIL',
                                        'MENUNGGU_PEMBAYARAN', 
                                        'PROSES_PENCUCIAN', 
                                        'SELESAI_DICUCI', 
                                        'DIKIRIM', 
                                        'TIBA'
                                    ];

                                    // Cari posisi status saat ini (0 sampai 7)
                                    $currentStatusIndex = array_search($selectedOrder->status, $allStatuses);
                                    
                                    // Handle jika status DIBATALKAN (Index jadi -1 / khusus)
                                    $isCancelled = ($selectedOrder->status == 'DIBATALKAN');
                                @endphp

                                @if($isCancelled)
                                    <li class="mb-3 d-flex align-items-center text-danger">
                                        <i class="bi bi-x-circle-fill me-3 fs-5"></i>
                                        <strong>DIBATALKAN</strong>
                                    </li>
                                @else
                                    {{-- LOOPING STATUS --}}
                                    @foreach($allStatuses as $index => $statusLabel)
                                        @php
                                            // Status sudah lewat atau sedang terjadi
                                            $isActive = $index <= $currentStatusIndex;
                                            
                                            // Status tepat saat ini
                                            $isCurrent = $index === $currentStatusIndex;
                                        @endphp

                                        <li class="mb-3 d-flex align-items-center {{ $isActive ? '' : 'opacity-50' }}">
                                            {{-- ICON --}}
                                            @if($isActive)
                                                <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                            @else
                                                <i class="bi bi-circle text-muted me-3 fs-5"></i>
                                            @endif

                                            {{-- TEXT --}}
                                            <div>
                                                <strong class="d-block {{ $isActive ? 'text-dark' : 'text-muted' }} {{ $isCurrent ? 'text-primary' : '' }}">
                                                    {{ str_replace('_', ' ', $statusLabel) }}
                                                    @if($isCurrent) 
                                                        <span class="badge bg-primary ms-1" style="font-size: 0.6rem">POSISI SAAT INI</span> 
                                                    @endif
                                                </strong>
                                                
                                                {{-- Keterangan Tambahan Per Status --}}
                                                @if($isActive)
                                                    @if($statusLabel == 'MENUNGGU_DIJEMPUT')
                                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Order masuk ke sistem</small>

                                                    <!-- BAGIAN SINI JUGA -->
                                                    @elseif($statusLabel == 'DRIVER_OTW' && $selectedOrder->pickupDriver)
                                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                            Driver: <strong>{{ $selectedOrder->pickupDriver->name }}</strong>
                                                        </small>
                                                        <small class="text-success d-block fw-bold" style="font-size: 0.75rem;">
                                                            <i class="bi bi-whatsapp"></i> 
                                                            <a href="https://wa.me/{{ $selectedOrder->pickupDriver->phone }}" target="_blank" class="text-success text-decoration-none">
                                                                {{ $selectedOrder->pickupDriver->phone }}
                                                            </a>
                                                        </small>
                                                    @elseif($statusLabel == 'MENUNGGU_PEMBAYARAN' && $selectedOrder->is_paid)
                                                        <small class="text-success d-block fw-bold" style="font-size: 0.75rem;">Lunas & Terverifikasi</small>

                                                    <!-- BAGIAN SINI -->
                                                    @elseif($statusLabel == 'DIKIRIM' && $selectedOrder->deliveryDriver)
                                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                                Driver: <strong>{{ $selectedOrder->deliveryDriver->name }}</strong>
                                                        </small>
                                                        <small class="text-success d-block fw-bold" style="font-size: 0.75rem;">
                                                                <i class="bi bi-whatsapp"></i> 
                                                                <a href="https://wa.me/{{ $selectedOrder->deliveryDriver->phone }}" target="_blank" class="text-success text-decoration-none">
                                                                    {{ $selectedOrder->deliveryDriver->phone }}
                                                                </a>
                                                        </small>
                                                    @endif
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted small">Memuat data...</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    {{-- Tombol Tutup Bawah --}}
                    <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        
        // 1. Script Pembuka Modal Detail
        const detailModalEl = document.getElementById('detailModal');
        const detailModal = new bootstrap.Modal(detailModalEl);

        Livewire.on('open-detail-modal', () => {
            detailModal.show();
        });

        // 2. Script Pembuka Modal Upload
        const uploadModalEl = document.getElementById('uploadPaymentModal');
        const uploadModal = new bootstrap.Modal(uploadModalEl);

        Livewire.on('close-modal', () => {
            uploadModal.hide();
            // Jaga-jaga detail juga ditutup
            detailModal.hide();
        });

        // 3. FIX: FORCE CLEANUP MODAL STUCK (PENTING!)
        // Kode ini akan berjalan setiap kali modal ditutup
        detailModalEl.addEventListener('hidden.bs.modal', function () {
            // Hapus backdrop yang tertinggal paksa
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Kembalikan scroll body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
        
        uploadModalEl.addEventListener('hidden.bs.modal', function () {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
        });
    });
</script>
@endpush
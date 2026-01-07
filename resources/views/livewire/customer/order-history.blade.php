<div class="d-flex flex-column min-vh-100">
    @push('styles')
        <link href="{{ asset('assets/css/customer.css') }}" rel="stylesheet">
    @endpush

    <livewire:customer.navbar />

    <div class="container mt-5 pt-5 mb-5 grow">
        
        {{-- Header dengan Filter & Search --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
            
            {{-- 1. Judul Halaman --}}
            <div class="mb-3 mb-md-0">
                <h2 class="page-title mb-1 border-0" style="border-bottom: none !important; padding-bottom: 0 !important;">
                    Riwayat Pesanan
                </h2>
            </div>
            
            {{-- 2. Area Form Search & Filter (Kanan) --}}
            <div class="d-flex flex-column flex-md-row gap-2" style="width: 100%; max-width: 450px;">
                
                {{-- Input Pencarian --}}
                <div class="position-relative grow">
                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control rounded-pill border-0 shadow-sm ps-5" 
                           placeholder="Cari ID..." 
                           wire:model.live="search"
                           style="height: 45px;">
                </div>

                {{-- Dropdown Filter --}}
                <div class="position-relative" style="min-width: 180px;">
                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary">
                        <i class="bi bi-funnel-fill"></i>
                    </span>
                    <select class="form-select rounded-pill border-0 shadow-sm ps-5 cursor-pointer" 
                            wire:model.live="statusFilter"
                            style="height: 45px; cursor: pointer;">
                        <option value="">Semua Status</option>
                        <option value="MENUNGGU_DIJEMPUT">Menunggu Jemput</option>
                        <option value="DRIVER_OTW">Driver OTW</option>
                        <option value="CUCIAN_DIAMBIL">Cucian Diambil</option>
                        <option value="MENUNGGU_PEMBAYARAN">Belum Bayar</option>
                        <option value="PROSES_PENCUCIAN">Sedang Dicuci</option>
                        <option value="SELESAI_DICUCI">Selesai Dicuci</option>
                        <option value="DIKIRIM">Sedang Dikirim</option>
                        <option value="TIBA">Tiba di Lokasi</option>
                        <option value="DIBATALKAN">Dibatalkan</option>
                    </select>
                </div>

            </div>
        </div>

        {{-- Alert Sukses --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Tabel Riwayat --}}
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Order ID</th>
                                <th>Tanggal Order</th>
                                <th>Layanan</th>
                                <th>Status</th>
                                <th>Tagihan</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#LD-{{ $order->id }}</td>
                                    <td>
                                        {{ $order->created_at->format('d M Y') }}
                                        <div class="small text-muted">{{ $order->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <span class="d-block fw-bold text-dark">{{ $order->service_type }}</span>
                                        @if($order->total_weight > 0)
                                            <small class="text-muted">{{ $order->total_weight }} Kg/Pcs</small>
                                        @endif
                                    </td>
                                    <td>
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
                                        <span class="badge {{ $statusColor }} rounded-pill">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->total_price > 0)
                                            <span class="fw-bold text-dark">Rp {{ number_format($order->total_price) }}</span>
                                            @if($order->is_paid)
                                                <i class="bi bi-check-circle-fill text-success ms-1" title="Lunas"></i>
                                            @endif
                                        @else
                                            <span class="fst-italic text-muted small">Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($order->status == 'TIBA')
                                            <button class="btn btn-sm {{ $order->review ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }} me-1"
                                                    wire:click="openRatingModal({{ $order->id }})"
                                                    data-bs-toggle="modal" data-bs-target="#ratingModal"
                                                    title="{{ $order->review ? 'Edit Ulasan' : 'Beri Ulasan' }}">
                                                <i class="bi {{ $order->review ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            </button>
                                        @endif

                                        {{-- Tombol Cetak Nota --}}
                                        <a href="{{ route('invoice.print', $order->id) }}" target="_blank" 
                                           class="btn btn-sm btn-outline-dark me-1" 
                                           title="Cetak Nota">
                                            <i class="bi bi-printer"></i>
                                        </a>

                                        {{-- Tombol Detail --}}
                                        <button class="btn btn-sm btn-outline-secondary me-1" 
                                                wire:click="showDetail({{ $order->id }})"
                                                title="Lihat Detail">
                                            Detail
                                        </button>

                                        {{-- Tombol Bayar --}}
                                        @if($order->status == 'MENUNGGU_PEMBAYARAN' && !$order->payment_proof)
                                            <button class="btn btn-sm btn-danger" 
                                                    wire:click="openPaymentModal({{ $order->id }})"
                                                    data-bs-toggle="modal" data-bs-target="#uploadPaymentModal">
                                                Bayar
                                            </button>
                                        @elseif($order->status == 'MENUNGGU_PEMBAYARAN' && $order->payment_proof)
                                            <span class="badge bg-light text-dark border">Menunggu Verif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted mb-3">
                                            <i class="bi bi-search fs-1"></i>
                                        </div>
                                        <p class="mb-0 text-muted">Order tidak ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Paginasi --}}
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <footer class="mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 LaundryYuk. All rights reserved.</p>
        </div>
    </footer>

    {{-- MODAL 1: PEMBAYARAN --}}
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
                            <p class="text-muted mb-1">Total yang harus dibayar:</p>
                            <h2 class="fw-bold text-danger mb-4">Rp {{ number_format($selectedOrder->total_price, 0, ',', '.') }}</h2>
                            <div class="border rounded p-3 d-inline-block mb-3 bg-light">
                                <img src="{{ asset('assets/img/qris.jpeg') }}" alt="Scan QRIS" class="img-fluid" style="max-width: 200px;">
                                <small class="d-block text-muted mt-2">Scan QRIS ini</small>
                            </div>
                            <div class="alert alert-light border small text-start mb-4">
                                <i class="bi bi-bank me-1"></i> Atau transfer manual ke: <br>
                                <strong>BCA 1234567890 (LaundryYuk)</strong>
                            </div>
                            <hr>
                            <div class="text-start">
                                <label class="form-label fw-bold">Upload Bukti Transfer</label>
                                <input type="file" class="form-control" wire:model="payment_proof" accept="image/*" required>
                                <div wire:loading wire:target="payment_proof" class="text-info small mt-1">
                                    <span class="spinner-border spinner-border-sm" role="status"></span> Sedang memproses gambar...
                                </div>
                                @error('payment_proof') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                            </div>
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
                    <button type="button" class="btn-close" id="btnCloseDetailModal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($selectedOrder)
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-primary mb-2">#LD-{{ $selectedOrder->id }}</h4>
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

                        <div class="timeline p-3 border rounded">
                            <small class="text-muted d-block mb-3 fw-bold text-uppercase" style="font-size: 0.7rem;">Status Perjalanan</small>
                            <ul class="list-unstyled mb-0 ps-2">
                                @php
                                    $allStatuses = ['MENUNGGU_DIJEMPUT', 'DRIVER_OTW', 'CUCIAN_DIAMBIL', 'MENUNGGU_PEMBAYARAN', 'PROSES_PENCUCIAN', 'SELESAI_DICUCI', 'DIKIRIM', 'TIBA'];
                                    $currentStatusIndex = array_search($selectedOrder->status, $allStatuses);
                                    $isCancelled = ($selectedOrder->status == 'DIBATALKAN');
                                @endphp

                                @if($isCancelled)
                                    <li class="mb-3 d-flex align-items-center text-danger">
                                        <i class="bi bi-x-circle-fill me-3 fs-5"></i>
                                        <strong>DIBATALKAN</strong>
                                    </li>
                                @else
                                    @foreach($allStatuses as $index => $statusLabel)
                                        @php
                                            $isActive = $index <= $currentStatusIndex;
                                            $isCurrent = $index === $currentStatusIndex;
                                        @endphp
                                        <li class="mb-3 d-flex align-items-center {{ $isActive ? '' : 'opacity-50' }}">
                                            @if($isActive) <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                            @else <i class="bi bi-circle text-muted me-3 fs-5"></i> @endif
                                            <div>
                                                <strong class="d-block {{ $isActive ? 'text-dark' : 'text-muted' }} {{ $isCurrent ? 'text-primary' : '' }}">
                                                    {{ str_replace('_', ' ', $statusLabel) }}
                                                </strong>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 3: BERI RATING --}}
    <div wire:ignore.self class="modal fade" id="ratingModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="saveRating">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Beri Penilaian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center pt-4">
                        
                        <h6 class="text-muted mb-3">Bagaimana hasil cucian kami?</h6>

                        {{-- STAR RATING INTERFACE --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-center gap-2">
                                @foreach(range(1, 5) as $i)
                                    <button type="button" 
                                            class="btn border-0 p-0" 
                                            wire:click="$set('rating', {{ $i }})">
                                        <i class="bi bi-star-fill fs-1 {{ $rating >= $i ? 'text-warning' : 'text-secondary opacity-25' }} transition-icon"></i>
                                    </button>
                                @endforeach
                            </div>
                            <div class="mt-2 fw-bold text-warning">
                                {{ $rating == 5 ? 'Sangat Puas! 😍' : ($rating == 4 ? 'Puas 😊' : ($rating == 3 ? 'Cukup 🙂' : ($rating == 2 ? 'Kurang 😞' : 'Kecewa 😫'))) }}
                            </div>
                        </div>

                        {{-- INPUT KOMENTAR --}}
                        <div class="mb-3 text-start">
                            <label class="form-label small fw-bold">Ulasan Anda (Opsional)</label>
                            <textarea class="form-control" wire:model="comment" rows="3" placeholder="Contoh: Wangi banget, setrikaan rapi..."></textarea>
                        </div>

                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="submit" class="btn btn-warning w-100 fw-bold">Kirim Penilaian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        
        // 1. Script Khusus untuk Membuka Modal Detail (Karena dipanggil dari PHP)
        const detailModalEl = document.getElementById('detailModal');
        const detailModal = new bootstrap.Modal(detailModalEl);

        Livewire.on('open-detail-modal', () => {
            detailModal.show();
        });

        // 2. Script Penutup Modal Global (Array Approach)
        Livewire.on('close-modal', () => {
            // DAFTAR ID SEMUA MODAL DI HALAMAN INI
            const modals = ['uploadPaymentModal', 'detailModal', 'ratingModal']; 

            modals.forEach(id => {
                const el = document.getElementById(id);
                if(el) {
                    // Ambil instance bootstrap yang sedang aktif
                    const modalInstance = bootstrap.Modal.getInstance(el);
                    if (modalInstance) modalInstance.hide();
                }
            });
        });
        const allModalIds = ['uploadPaymentModal', 'detailModal', 'ratingModal'];
        
        allModalIds.forEach(id => {
            const el = document.getElementById(id);
            if(el) {
                el.addEventListener('hidden.bs.modal', function () {
                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                });
            }
        });
    });
</script>
@endpush
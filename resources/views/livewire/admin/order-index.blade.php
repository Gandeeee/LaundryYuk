<div>
    @push('styles')
        <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
    @endpush

    <x-admin-sidebar />
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <main class="content">
        <livewire:admin.navbar title="Manajemen Order" />

        <div class="page-content active">
            
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">Daftar Pesanan</h4>
                    <p class="text-muted mb-0">Kelola semua pesanan laundry</p>
                </div>
                <button class="btn-add-order" 
                        data-bs-toggle="modal" 
                        data-bs-target="#manualOrderModal" 
                        wire:click="resetManualInput">
                    <i class="bi bi-plus-circle"></i>
                    <span>Entry Laundry Manual</span>
                </button>
            </div>
            
            <div class="card-order-management">
                <div class="card-header-order">
                    <h5 class="mb-0"><i class="bi bi-card-checklist"></i> Daftar Semua Order</h5>
                    <div class="text-muted small">Total: <span class="fw-bold text-primary">{{ $orders->count() }}</span> orders</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-order-management mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Pelanggan</th>
                                <th>Jadwal Jemput</th>
                                <th>Status</th>
                                <th>Pembayaran</th>
                                <th class="text-center">Rating</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td><div class="order-id">#LD-{{ $order->id }}</div></td>
                                    <td>
                                        <div class="customer-info">
                                            <div class="customer-name">{{ $order->customer_name }}</div>
                                            <div class="customer-phone">{{ $order->phone_number }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="schedule-info">
                                            <div class="schedule-date">{{ $order->pickup_schedule->format('d M Y') }}</div>
                                            <div class="schedule-time">{{ $order->pickup_schedule->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        {{-- Badge Status --}}
                                        <span class="badge {{ $order->status == 'SELESAI_DICUCI' ? 'bg-dark' : ($order->status == 'PROSES_PENCUCIAN' ? 'bg-success' : 'bg-primary') }}">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->is_paid)
                                            <span class="payment-badge bg-success text-white">LUNAS</span>
                                        @elseif($order->total_price > 0)
                                            <span class="payment-badge bg-warning text-dark">TAGIHAN: {{ number_format($order->total_price) }}</span>
                                        @else
                                            <span class="payment-badge bg-secondary text-white">BELUM DITIMBANG</span>
                                        @endif
                                    </td>

                                    {{-- ISI KOLOM RATING (BARU) --}}
                                    <td class="text-center">
                                        @if($order->review)
                                            {{-- Tampilkan Bintang --}}
                                            <div class="text-warning small" style="white-space: nowrap;">
                                                @for($i = 0; $i < $order->review->rating; $i++)
                                                    <i class="bi bi-star-fill"></i>
                                                @endfor
                                            </div>
                                            
                                            {{-- Tampilkan Komentar (Tooltip/Small Text) --}}
                                            @if($order->review->comment)
                                                <div class="text-muted fst-italic text-truncate" style="max-width: 150px; font-size: 0.75rem;" title="{{ $order->review->comment }}">
                                                    "{{ $order->review->comment }}"
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    <td class="text-end action-buttons-order">
                                        
                                        <a href="{{ route('invoice.print', $order->id) }}" target="_blank" 
                                            class="btn btn-outline-dark btn-sm me-1" 
                                            title="Cetak Nota">
                                                <i class="bi bi-printer-fill"></i>
                                        </a>

                                        {{-- 1. Tombol Tagihan (HANYA MUNCUL SAAT STATUS 'CUCIAN_DIAMBIL') --}}
                                        @if($order->status == 'CUCIAN_DIAMBIL')
                                            <button class="btn-action btn-tagihan" 
                                                    data-bs-toggle="modal" data-bs-target="#inputTagihanModal" 
                                                    wire:click="openTagihanModal({{ $order->id }})">
                                                <i class="bi bi-receipt"></i><span>Tagihan</span>
                                            </button>
                                        @endif

                                        {{-- 2. Tombol Verifikasi--}}
                                        @if($order->status == 'MENUNGGU_PEMBAYARAN')
                                            {{-- Cek apakah customer sudah upload bukti? --}}
                                            @if($order->payment_proof)
                                                <button class="btn-action btn-verifikasi" 
                                                        data-bs-toggle="modal" data-bs-target="#verifyPaymentModal"
                                                        wire:click="openVerifyModal({{ $order->id }})">
                                                    <i class="bi bi-eye-fill"></i><span>Cek Bukti</span>
                                                </button>
                                            @else
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">Belum Upload</span>
                                            @endif
                                        @endif

                                        {{-- 3. Tombol Status Manual --}}
                                        <button class="btn-action btn-status" 
                                                data-bs-toggle="modal" data-bs-target="#statusModal"
                                                wire:click="openStatusModal({{ $order->id }})">
                                            <i class="bi bi-list-check"></i><span>Status</span>
                                        </button>

                                        {{-- 4. Tombol Assign Driver --}}
                                        @if($order->status == 'MENUNGGU_DIJEMPUT')
                                            <button class="btn-action btn-jemput" 
                                                    data-bs-toggle="modal" data-bs-target="#assignDriverModal"
                                                    wire:click="openDriverModal({{ $order->id }}, 'pickup')">
                                                <i class="bi bi-truck"></i><span>Jemput</span>
                                            </button>
                                        @elseif($order->status == 'SELESAI_DICUCI')
                                            <button class="btn-action btn-antar" 
                                                    data-bs-toggle="modal" data-bs-target="#assignDriverModal"
                                                    wire:click="openDriverModal({{ $order->id }}, 'delivery')">
                                                <i class="bi bi-send"></i><span>Antar</span>
                                            </button>
                                        @endif

                                        {{-- Hapus --}}
                                        <button class="btn btn-outline-danger btn-sm" 
                                                wire:click="deleteOrder({{ $order->id }})"
                                                wire:confirm="Yakin hapus order ini?">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>

                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center p-4">Belum ada order.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
   {{-- MODAL 1: INPUT TAGIHAN --}}
    <div wire:ignore.self class="modal fade" id="inputTagihanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="saveTagihan">
                    <div class="modal-header">
                        <h5 class="modal-title">Input Tagihan (Penimbangan)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        
                        {{-- Info Harga Satuan --}}
                        @if($isAutoCalc)
                            <div class="alert alert-info py-2 small">
                                <i class="bi bi-info-circle me-1"></i>
                                Harga Layanan: <strong>Rp {{ number_format($pricePerUnit) }} / Kg (atau Pcs)</strong>
                            </div>
                        @else
                            <div class="alert alert-warning py-2 small">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Layanan satuan khusus/range harga. Silakan input manual.
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Berat / Jumlah (Kg/Pcs)</label>
                            {{-- Ganti wire:model menjadi wire:model.live agar hitung otomatis --}}
                            <input type="number" step="0.01" class="form-control" 
                                   wire:model.live="weight" 
                                   placeholder="Contoh: 3.5" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Harga (Rp)</label>
                            {{-- Tambahkan logic readonly --}}
                            <input type="number" class="form-control bg-light" 
                                   wire:model="price" 
                                   {{ $isAutoCalc ? 'readonly' : '' }} 
                                   required>
                            @if($isAutoCalc)
                                <div class="form-text text-muted">
                                    Otomatis dihitung: {{ $weight }} x {{ $pricePerUnit }}
                                </div>
                            @endif
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Simpan & Kirim Tagihan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: ASSIGN DRIVER --}}
    <div wire:ignore.self class="modal fade" id="assignDriverModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="saveDriver">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Driver {{ $assignmentType == 'pickup' ? 'Jemput' : 'Antar' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <select class="form-select" wire:model="selectedDriverId" required>
                            <option value="">Pilih Driver...</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Tugaskan Driver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 3: UPDATE STATUS MANUAL --}}
    <div wire:ignore.self class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="saveStatus">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Update Status Bertahap</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        
                        {{-- Logika untuk mencari index status saat ini --}}
                        @php
                            // Cari order yang sedang dipilih dari koleksi
                            $currentOrder = $orders->find($selectedOrderId);
                            $currentStatus = $currentOrder ? $currentOrder->status : 'MENUNGGU_DIJEMPUT';
                            
                            // Cari urutan ke-berapa status saat ini (0, 1, 2, dst)
                            $currentIndex = array_search($currentStatus, $statusFlow);
                            if($currentIndex === false) $currentIndex = -1; // Jika status DIBATALKAN atau custom
                        @endphp

                        <div class="status-timeline-container ps-2">
                            
                            {{-- LOOPING STATUS UTAMA --}}
                            @foreach($statusFlow as $index => $status)
                                @php
                                    // Logic Disable:
                                    // 1. Disable status masa lalu (kurang dari index saat ini)
                                    // 2. Disable status masa depan yang loncat (lebih dari index + 1)
                                    // 3. Enable hanya status saat ini dan SATU langkah ke depan
                                    // Exception: Jika status 'PROSES_PENCUCIAN', cek pembayaran.
                                    // Tapi sesuai instruksi 'tidak boleh melompat', kita kunci berdasarkan urutan saja.
                                    
                                    $isPast = $index < $currentIndex;
                                    $isCurrent = $index === $currentIndex;
                                    $isNext = $index === ($currentIndex + 1);
                                    
                                    // Kunci semua yang bukan Next dan bukan Current
                                    // User juga tidak bisa mundur (uncheck history)
                                    $isDisabled = !($isCurrent || $isNext);
                                @endphp

                                <div class="form-check mb-3 status-step {{ $isPast ? 'step-past' : '' }} {{ $isCurrent ? 'step-current' : '' }}">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="newStatus" 
                                           id="status_{{ $index }}" 
                                           value="{{ $status }}" 
                                           wire:model="newStatus"
                                           {{ $isDisabled ? 'disabled' : '' }}>
                                    
                                    <label class="form-check-label d-block" for="status_{{ $index }}">
                                        <span class="fw-bold {{ $isPast ? 'text-muted text-decoration-line-through' : ($isCurrent ? 'text-primary' : '') }}">
                                            {{ str_replace('_', ' ', $status) }}
                                        </span>
                                        
                                        {{-- Indikator Visual --}}
                                        @if($isCurrent)
                                            <span class="badge bg-primary ms-2">Saat Ini</span>
                                        @elseif($isNext)
                                            <span class="badge bg-success ms-2">Langkah Berikutnya</span>
                                        @elseif($isPast)
                                            <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                        @else
                                            <i class="bi bi-lock-fill text-muted ms-2" style="font-size: 0.8rem;"></i>
                                        @endif
                                    </label>
                                </div>
                            @endforeach

                            <hr class="my-3">

                            {{-- OPSI DIBATALKAN (SELALU MUNCUL DI BAWAH) --}}
                            <div class="form-check">
                                <input class="form-check-input border-danger" 
                                       type="radio" 
                                       name="newStatus" 
                                       id="status_cancel" 
                                       value="DIBATALKAN" 
                                       wire:model="newStatus">
                                <label class="form-check-label text-danger fw-bold" for="status_cancel">
                                    <i class="bi bi-x-circle me-1"></i> DIBATALKAN
                                </label>
                                <div class="form-text text-muted small">
                                    Pilih ini hanya jika order dibatalkan permanen.
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Simpan Perubahan Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 4: ENTRY LAUNDRY MANUAL (WALK-IN) --}}
    <div wire:ignore.self class="modal fade" id="manualOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg"> {{-- Pakai modal-lg biar lega --}}
            <div class="modal-content">
                <form wire:submit.prevent="saveManualOrder">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold"><i class="bi bi-shop me-2"></i>Entry Order (Walk-in)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Order ini akan langsung berstatus <strong>MENUNGGU PEMBAYARAN</strong>. Tanggal order diset <strong>Hari Ini</strong>.
                        </div>

                        <div class="row g-3">
                            {{-- Kolom Kiri: Data Pelanggan --}}
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3 text-secondary">Data Pelanggan</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nama Pelanggan</label>
                                    <input type="text" class="form-control" wire:model="manualName" placeholder="Nama Lengkap" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nomor WhatsApp</label>
                                    <input type="tel" class="form-control" wire:model="manualPhone" placeholder="08xxxxxxxx" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat Domisili</label>
                                    <textarea class="form-control" wire:model="manualAddress" rows="2" placeholder="Alamat pelanggan..." required></textarea>
                                </div>
                            </div>

                            {{-- Kolom Kanan: Detail Cucian --}}
                            <div class="col-md-6 border-start ps-md-4">
                                <h6 class="fw-bold mb-3 text-secondary">Detail Cucian</h6>

                                <div class="mb-3">
                                    <label class="form-label">Pilih Layanan</label>
                                    <select class="form-select" wire:model.live="manualService" required>
                                        <option value="">-- Pilih Layanan --</option>
                                        @foreach($priceList as $serviceName => $price)
                                            <option value="{{ $serviceName }}">{{ $serviceName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Berat/Jml</label>
                                        <input type="number" step="0.1" class="form-control" 
                                               wire:model.live="manualWeight" 
                                               placeholder="0.0" required>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Estimasi (Rp)</label>
                                        <input type="text" class="form-control bg-light" 
                                               value="{{ number_format($manualPricePerUnit) }}" readonly disabled>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-success">Total Tagihan (Rp)</label>
                                    {{-- Input harga tetap editable jaga-jaga ada diskon manual --}}
                                    <input type="number" class="form-control border-success text-success fw-bold" 
                                           wire:model="manualPrice" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-save me-1"></i> Buat Order & Tagihan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 5: VERIFIKASI PEMBAYARAN --}}
    <div wire:ignore.self class="modal fade" id="verifyPaymentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Verifikasi Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    
                    <p class="text-muted mb-2">Bukti Transfer dari Customer:</p>
                    
                    @if($proofUrl)
                        <div class="border rounded p-2 d-inline-block bg-light mb-3">
                            <a href="{{ $proofUrl }}" target="_blank">
                                <img src="{{ $proofUrl }}" class="img-fluid" style="max-height: 300px;" alt="Bukti Transfer">
                            </a>
                        </div>
                        <div class="small text-muted mb-3">
                            <i class="bi bi-zoom-in"></i> Klik gambar untuk memperbesar
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-circle"></i> Customer belum upload bukti atau file tidak ditemukan.
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success fw-bold py-2" wire:click="verifyPayment">
                            <i class="bi bi-check-circle-fill me-2"></i> SAH - Terima Pembayaran
                        </button>
                        
                        {{-- TOMBOL TOLAK (Pakai Outline Danger) --}}
                        <button class="btn btn-outline-danger fw-bold py-2" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i> Tutup / Tolak
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
</div>


@push('scripts')
<script>
    // 1. Toggle Sidebar (Script Lama)
    document.getElementById('sidebarToggleBtn')?.addEventListener('click', () => {
        document.body.classList.toggle('sidebar-toggled');
    });
    document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
        document.body.classList.remove('sidebar-toggled');
    });

    // 2. Helper Tutup Modal (Script Baru yang Anda tanyakan)
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('close-modal', () => {
            // Daftar ID semua modal yang ada di halaman ini
            const modals = [
                'inputTagihanModal', 
                'assignDriverModal', 
                'statusModal', 
                'manualOrderModal', 
                'verifyPaymentModal' // <-- Modal baru
            ];
            
            // Loop untuk menutup modal yang sedang terbuka
            modals.forEach(id => {
                const el = document.getElementById(id);
                if(el) {
                    const modal = bootstrap.Modal.getInstance(el);
                    if (modal) modal.hide();
                }
            });
            
            // FIX TAMBAHAN: Bersihkan backdrop yang nyangkut (Glitch Fix)
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
        });
    });
</script>
@endpush
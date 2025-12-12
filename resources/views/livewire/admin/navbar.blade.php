<nav class="top-navbar" wire:poll.5s>
    
    <button class="btn btn-outline-secondary d-lg-none" type="button" id="sidebarToggleBtn">
        <i class="bi bi-list"></i>
    </button>
    
    <h5 class="mb-0" id="page-title">{{ $title }}</h5>
    
    {{-- Area Notifikasi --}}
    <div class="dropdown notification-ui">
        <button class="btn btn-notification" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-bell-fill {{ $notifCount > 0 ? 'text-danger' : '' }}"></i>
            
            @if($notifCount > 0)
                <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                    {{ $notifCount }}
                </span>
            @endif
        </button>

        <ul class="dropdown-menu dropdown-menu-end notification-dropdown-menu shadow-lg border-0" style="max-height: 400px; overflow-y: auto;">
            <li class="notification-header bg-light">
                <h6 class="dropdown-header fw-bold text-dark">Notifikasi Masuk</h6>
                <span class="badge bg-primary rounded-pill">{{ $notifCount }} Baru</span>
            </li>
            <li><hr class="dropdown-divider my-0"></li>
            
            <div id="notification-list-container">
                @forelse($notifications as $notif)
                    <a class="notification-item dropdown-item p-3 border-bottom" href="{{ route('admin.orders') }}">
                        <div class="d-flex align-items-start">
                            <div class="notif-icon-box me-3 mt-1 
                                {{ $notif->status == 'MENUNGGU_DIJEMPUT' ? 'bg-primary text-white' : 'bg-warning text-dark' }} 
                                rounded p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi {{ $notif->status == 'MENUNGGU_DIJEMPUT' ? 'bi-box-seam' : 'bi-cash-coin' }} fs-5"></i>
                            </div>
                            
                            <div class="notif-content w-100">
                                <div class="d-flex justify-content-between">
                                    <span class="notif-title fw-bold small text-uppercase text-primary">#LD-{{ $notif->id }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="d-block fw-bold text-dark mt-1">{{ $notif->customer_name }}</span>
                                <span class="notif-desc d-block text-muted small mt-1">
                                    @if($notif->status == 'MENUNGGU_DIJEMPUT')
                                        Pesanan Baru! Segera tugaskan driver.
                                    @else
                                        Menunggu verifikasi pembayaran.
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="notif-empty text-center py-5">
                        <i class="bi bi-bell-slash fs-1 text-muted mb-3 d-block"></i>
                        <small class="text-muted">Tidak ada notifikasi baru</small>
                    </div>
                @endforelse
            </div>
        </ul>
    </div>

    {{-- ELEMENT AUDIO: Diarahkan ke notif.MP3 --}}
    <audio id="notifAudio" src="{{ asset('assets/audio/notif.MP3') }}" preload="auto"></audio>

</nav>

{{-- SCRIPT: Tidak berubah, tetap mendengarkan event dari PHP --}}
@script
<script>
    Livewire.on('play-notification-sound', () => {
        const audio = document.getElementById('notifAudio');
        if (audio) {
            audio.play().catch(error => {
                console.log("Autoplay dicegah browser, Admin harus interaksi dulu: ", error);
            });
        }
    });
</script>
@endscript
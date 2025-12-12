<div>
    @push('styles')
        <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
    @endpush

    <x-admin-sidebar />
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <main class="content">
        <livewire:admin.navbar title="Manajemen Driver" />

        <div class="page-content active">
            
            {{-- Alert Sukses --}}
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">Data Driver</h4>
                    <p class="text-muted mb-0">Kelola kurir antar-jemput laundry</p>
                </div>
                {{-- Tombol Tambah dengan Style Baru --}}
                <button class="btn-add-order" 
                        data-bs-toggle="modal" 
                        data-bs-target="#driverModal" 
                        wire:click="resetInput">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Tambah Driver</span>
                </button>
            </div>

            {{-- TABEL DRIVER DENGAN STYLE BARU (Mengadopsi Order Management) --}}
            <div class="card-order-management">
                <div class="card-header-order">
                    <h5 class="mb-0"><i class="bi bi-truck me-2"></i> Daftar Driver</h5>
                    <div class="text-muted small">Total: <span class="fw-bold text-primary">{{ $drivers->count() }}</span> driver</div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-order-management mb-0">
                        <thead>
                            <tr>
                                <th>ID Driver</th>
                                <th>Informasi Driver</th>
                                <th>Status Ketersediaan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($drivers as $driver)
                                <tr>
                                    {{-- ID dengan style box --}}
                                    <td><div class="order-id">#DRV-{{ $driver->id }}</div></td>
                                    
                                    {{-- Info Driver (Nama & No HP) --}}
                                    <td>
                                        <div class="customer-info">
                                            <div class="customer-name">{{ $driver->name }}</div>
                                            <div class="customer-phone">
                                                <i class="bi bi-whatsapp text-success me-1"></i> {{ $driver->phone }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Status Switch (Toggle) --}}
                                    <td>
                                        <div class="form-check form-switch d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                   style="cursor: pointer; width: 3em; height: 1.5em;"
                                                   {{ $driver->is_available ? 'checked' : '' }} 
                                                   wire:click="toggleStatus({{ $driver->id }})">
                                            
                                            @if($driver->is_available)
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Tersedia</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="bi bi-slash-circle me-1"></i> Sibuk</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="text-end action-buttons-order">
                                        <button class="btn-action btn-status" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#driverModal" 
                                                wire:click="edit({{ $driver->id }})">
                                            <i class="bi bi-pencil-square"></i><span>Edit</span>
                                        </button>

                                        <button class="btn btn-outline-danger btn-sm" 
                                                wire:click="delete({{ $driver->id }})"
                                                wire:confirm="Yakin ingin menghapus driver ini?">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center p-5 text-muted">
                                        <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                        Belum ada data driver. Silakan tambah baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL FORM DRIVER --}}
    <div wire:ignore.self class="modal fade" id="driverModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="bi {{ $isEditMode ? 'bi-pencil-square' : 'bi-person-plus' }} me-2"></i>
                            {{ $isEditMode ? 'Edit Driver' : 'Tambah Driver Baru' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" wire:model="name" placeholder="Contoh: Budi Santoso" required>
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor WhatsApp</label>
                            <input type="tel" class="form-control" wire:model="phone" placeholder="08xxxxxxxx" required>
                            @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="alert alert-light border">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="availSwitch" wire:model="is_available">
                                <label class="form-check-label fw-bold" for="availSwitch">Set Status: Tersedia (Available)</label>
                                <div class="text-muted small">Aktifkan jika driver siap menerima orderan sekarang.</div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-save me-1"></i> {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Driver' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Script Toggle Sidebar
    document.getElementById('sidebarToggleBtn')?.addEventListener('click', () => {
        document.body.classList.toggle('sidebar-toggled');
    });
    document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
        document.body.classList.remove('sidebar-toggled');
    });

    // Helper Tutup Modal
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('close-modal', () => {
            var myModalEl = document.getElementById('driverModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) modal.hide();
        });
    });
</script>
@endpush
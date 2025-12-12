<div class="d-flex flex-column min-vh-100">
    @push('styles')
        <link href="{{ asset('assets/css/customer.css') }}" rel="stylesheet">
    @endpush

    <livewire:customer.navbar />

    <div class="container mt-4 mt-md-5 mb-5 grow">
        <h2 class="page-title mb-4">Buat Pesanan Baru</h2>
        
        <div class="card">
            <div class="card-body p-4 p-md-5">
                <form wire:submit="store">
                    
                    {{-- Pilihan Layanan --}}
                    <div class="mb-4">
                        <label for="serviceType" class="form-label fw-bold">Pilih Layanan</label>
                        <select class="form-select form-select-lg @error('service_type') is-invalid @enderror" 
                                id="serviceType" 
                                wire:model="service_type">
                            <option value="" disabled selected>Pilih jenis layanan...</option>
                            <optgroup label="Layanan Cuci (Kiloan)">
                                <option value="Cuci Kering">Cuci Kering (5.000/KG)</option>
                                <option value="Cuci Kering Lipat">Cuci Kering Lipat (5.500/KG)</option>
                                <option value="Cuci Kering Express 6 Jam">Cuci Kering Express 6 Jam (6.000/KG)</option>
                                <option value="Cuci Kering Express 3 Jam">Cuci Kering Express 3 Jam (7.000/KG)</option>
                                <option value="Cuci Setrika 2 Hari">Cuci Kering Setrika 2 Hari (8.000/KG)</option>
                                <option value="Cuci Setrika 3 Hari">Cuci Kering Setrika 3 Hari (7.000/KG)</option>
                                <option value="Cuci Setrika 4 Hari">Cuci Kering Setrika 4 Hari (6.500/KG)</option>
                                <option value="Cuci Setrika Express 6 Jam">Cuci Setrika Express 6 Jam (13.000/KG)</option>
                                <option value="Cuci Setrika Express 1 Jam">Cuci Setrika Express 1 Jam (10.000/KG)</option>
                                <option value="Setrika Saja">Setrika Saja 3 Hari (5.000/KG)</option>
                                <option value="Kering Saja">Kering (4.000/KG)</option>
                            </optgroup>
                            <optgroup label="Layanan Satuan">
                                <option value="Satuan Bedcover">Bedcover (25RB-40RB/PC)</option>
                                <option value="Satuan Sprei">Cuci Sprei/Selimut (10RB-15RB/SET)</option>
                                <option value="Satuan Tas">Tas Ransel (25RB-40RB/PC)</option>
                                <option value="Satuan Boneka">Boneka (5RB-70RB)</option>
                                <option value="Satuan Sepatu">Sepatu (25RB/PSG)</option>
                                <option value="Satuan Karpet">Karpet Tebal (30RB/M²)</option>
                            </optgroup>
                        </select>
                        @error('service_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="mb-4">
                        <label for="address" class="form-label fw-bold">Alamat Penjemputan</label>
                        <textarea class="form-control @error('pickup_address') is-invalid @enderror" 
                                  id="address" rows="4" 
                                  placeholder="Masukkan alamat lengkap (Jalan, No Rumah, Patokan)..." 
                                  wire:model="pickup_address"></textarea>
                        @error('pickup_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- No Telepon --}}
                    <div class="mb-4">
                        <label for="phone" class="form-label fw-bold">Nomor Telepon (WhatsApp)</label>
                        <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                               id="phone" placeholder="08xxxxxxxxxx" 
                               wire:model="phone_number">
                        @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Jadwal Jemput --}}
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="pickupDate" class="form-label fw-bold">Tanggal Jemput</label>
                            
                            {{-- atribut min & max untuk batasan visual kalender --}}
                            {{-- date('Y-m-d') = hari ini --}}
                            {{-- date('Y-m-d', strtotime('+3 days')) = 3 hari dari sekarang --}}
                            <input type="date" 
                                class="form-control @error('pickup_date') is-invalid @enderror" 
                                id="pickupDate" 
                                min="{{ date('Y-m-d') }}"
                                max="{{ date('Y-m-d', strtotime('+3 days')) }}"
                                wire:model="pickup_date">
                                
                            {{-- helper text agar user paham --}}
                            <div class="form-text text-muted small mb-1">
                                Pemesanan maks. H+3 dari hari ini.
                            </div>

                            @error('pickup_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="pickupTime" class="form-label fw-bold">Waktu Jemput</label>
                            {{-- Tambahan atribut min & max untuk batasan visual --}}
                            <input type="time" 
                                class="form-control @error('pickup_time') is-invalid @enderror" 
                                id="pickupTime" 
                                min="09:00" 
                                max="21:00"
                                wire:model="pickup_time">
                            
                            {{-- Helper text kecil untuk memberi tahu user --}}
                            <div class="form-text text-muted small mb-1">
                                Jam Operasional: 09:00 - 21:00
                            </div>
                            @error('pickup_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold">Catatan (Opsional)</label>
                        <input type="text" class="form-control" 
                               id="notes" placeholder="Contoh: Ada noda kopi di baju putih" 
                               wire:model="notes">
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                        {{-- Tombol Batal kembali ke dashboard --}}
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-light btn-lg me-md-2">Batal</a>
                        
                        <button type="submit" class="btn btn-danger btn-lg" wire:loading.attr="disabled">
                            <span wire:loading.remove>Kirim Pesanan</span>
                            <span wire:loading>Mengirim...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy;  2025 LaundryYuk - Kelompok 3. All rights reserved.</p>
        </div>
    </footer>
</div>
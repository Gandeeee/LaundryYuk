<div class="login-wrapper">
    @push('styles')
        <link href="{{ asset('assets/css/auth.css') }}" rel="stylesheet">
    @endpush

    <div class="card shadow-lg border-0">
        <div class="card-header bg-danger text-white text-center">
            <svg class="me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="30">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v16a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM7 8h10M7 12h5m-5 4h8" />
            </svg>
            <h3 class="my-2">LaundryYuk</h3>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <h5 class="card-title text-center mb-4">Buat Akun Customer Baru</h5>
            
            <form wire:submit="register">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" 
                        class="form-control {{ $errors->has('name') ? 'is-invalid' : ($name ? 'is-valid' : '') }}" 
                        id="name" 
                        wire:model.blur="name" 
                        required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                        class="form-control {{ $errors->has('email') ? 'is-invalid' : ($email ? 'is-valid' : '') }}" 
                        id="email" 
                        wire:model.blur="email" 
                        required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                        class="form-control {{ $errors->has('password') ? 'is-invalid' : ($password ? 'is-valid' : '') }}" 
                        id="password" 
                        wire:model.blur="password" 
                        required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    {{-- Validasi visual manual: Cek apakah sama dengan password --}}
                    <input type="password" 
                        class="form-control {{ $password_confirmation && $password === $password_confirmation ? 'is-valid' : '' }}" 
                        id="password_confirmation" 
                        wire:model.blur="password_confirmation" 
                        required>
                </div>

                <div class="d-grid pt-2">
                    <button type="submit" class="btn btn-danger" wire:loading.attr="disabled">
                        <span wire:loading.remove>Daftar</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">Sudah punya akun?
                    <a href="{{ route('login') }}">Login di sini</a>
                </small>
            </div>
        </div>
    </div>
</div>
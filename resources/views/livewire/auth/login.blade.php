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
            <h5 class="card-title text-center mb-4">Silakan Login</h5>
            
            <form wire:submit="login">
                
                {{-- Alert Sukses --}}
                @if (session()->has('success'))
                    <div class="alert alert-success text-center">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Alert Error (Hanya satu kali) --}}
                @if (session()->has('error'))
                    <div class="alert alert-danger text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                        class="form-control {{ $errors->has('email') ? 'is-invalid' : ($email ? 'is-valid' : '') }}" 
                        id="email" 
                        wire:model.blur="email" 
                        required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-4"> 
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                        class="form-control {{ $errors->has('password') ? 'is-invalid' : ($password ? 'is-valid' : '') }}" 
                        id="password" 
                        wire:model.blur="password" 
                        required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="d-grid pt-2">
                    <button type="submit" class="btn btn-danger" wire:loading.attr="disabled">
                        <span wire:loading.remove>Login</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">Belum punya akun?
                    <a href="{{ route('register') }}">Daftar di sini</a>
                </small>
            </div>

        </div>
    </div>
</div>
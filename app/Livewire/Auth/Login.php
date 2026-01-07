<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Login extends Component
{
    public $email = '';
    public $password = '';

    // Menentukan layout yang dipakai (app.blade.php)
    #[Layout('components.layouts.app')] 
    // Judul Halaman
    #[Title('Login - LaundryYuk')]
    public function render()
    {
        return view('livewire.auth.login');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.email' => 'Format email salah.',
        ]);
    }

    public function login()
    {
        // 1. Validasi Input (Final check saat tombol ditekan)
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Cek apakah Email ada di Database?
        // Jika email TIDAK ditemukan, kirim pesan error spesifik.
        if (!User::where('email', $this->email)->exists()) {
            session()->flash('error', 'Email tidak terdaftar. Silakan daftar akun terlebih dahulu.');
            return; // Berhenti di sini, jangan lanjut cek password
        }

        // 3. Coba Login (Laravel Auth)
        // Karena email sudah pasti ada (lolos cek di atas), jika attempt gagal berarti Password Salah.
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            
            // Regenerasi session ID (Security Best Practice)
            session()->regenerate();

            // 4. Cek Role User & Redirect
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('customer.dashboard');
            }
        }

        // 5. Jika Gagal (Berarti Password Salah)
        session()->flash('error', 'Password yang Anda masukkan salah.');
    }
}
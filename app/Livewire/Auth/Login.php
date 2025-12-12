<?php

namespace App\Livewire\Auth;

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

        // 2. Coba Login (Laravel Auth)
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            
            // Regenerasi session ID (Security Best Practice)
            session()->regenerate();

            // 3. Cek Role User & Redirect
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('customer.dashboard');
            }
        }

        // 4. Jika Gagal
        session()->flash('error', 'Email atau password salah.');
    }
}
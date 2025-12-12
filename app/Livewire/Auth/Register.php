<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    #[Layout('components.layouts.app')]
    #[Title('Daftar - LaundryYuk')]
    public function render()
    {
        return view('livewire.auth.register');
    }

    // VALIDASI REAL-TIME (Jalan saat user pindah kolom / selesai ngetik)
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            // Regex: Hanya Huruf (a-z, A-Z) dan Spasi. Tidak boleh angka.
            'name' => 'required|regex:/^[a-zA-Z\s]+$/|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.regex' => 'Nama harus berupa huruf, tidak boleh mengandung angka.',
            'email.email' => 'Format email salah (contoh: nama@domain.com).',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.confirmed' => 'Password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);
    }

    public function register()
    {
        // Validasi Final saat Submit
        $this->validate([
            'name' => 'required|regex:/^[a-zA-Z\s]+$/|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.regex' => 'Nama harus berupa huruf, tidak boleh mengandung angka.',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'customer',
        ]);

        session()->flash('success', 'Registrasi berhasil! Silakan login.');
        return redirect()->route('login');
    }
}
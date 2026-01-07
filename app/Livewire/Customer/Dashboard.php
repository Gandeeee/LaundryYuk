<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads; //buat file upload
use Illuminate\Support\Facades\Storage;

class Dashboard extends Component
{
    use WithFileUploads;

    public $selectedOrderId;
    public $payment_proof; // Properti penampung file gambar
    public $selectedOrder = null; // Untuk detail modal

    #[Layout('components.layouts.app')]
    #[Title('Dashboard Saya - LaundryYuk')]
    public function render()
    {
        $userId = Auth::id();

        return view('livewire.customer.dashboard', [
            'prosesCount' => Order::where('user_id', $userId)
                ->whereNotIn('status', ['SELESAI_DICUCI', 'TIBA', 'DIBATALKAN'])
                ->count(),
            'selesaiCount' => Order::where('user_id', $userId)
                ->whereIn('status', ['SELESAI_DICUCI', 'TIBA'])
                ->count(),
            'myOrders' => Order::where('user_id', $userId)
                ->latest()
                ->get()
        ]);
    }

    // --- FITUR UPLOAD BUKTI BAYAR ---
    // 1. Buka Modal Upload
    public function openPaymentModal($orderId)
    {
        $this->selectedOrderId = $orderId;
        // untuk menampilkan harga di modal pembayaran
        $this->selectedOrder = Order::find($orderId); 
        $this->payment_proof = null; 
    }

    // 2. Simpan Bukti Bayar
    public function savePayment()
    {
        $this->validate([
            'payment_proof' => 'required|image|max:2048', // Max 2MB, harus gambar
        ], [
            'payment_proof.required' => 'Wajib upload foto bukti transfer.',
            'payment_proof.image' => 'File harus berupa gambar (JPG/PNG).',
            'payment_proof.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $order = Order::find($this->selectedOrderId);

        // Upload ke folder 'public/payment_proofs'
        // php artisan storage:link
        $path = $this->payment_proof->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            // Status tidak berubah jadi 'Proses', tetap 'Menunggu Pembayaran'
            // sampai Admin memverifikasi (is_verified = true)
        ]);

        session()->flash('success', 'Bukti pembayaran berhasil diupload! Tunggu verifikasi admin ya.');
        $this->dispatch('close-modal');
    }

    // --- FITUR DETAIL / TIMELINE ---
    public function showDetail($orderId)
    {
        $this->selectedOrder = Order::find($orderId);
        $this->dispatch('open-detail-modal'); // Trigger JS untuk buka modal
    }
}
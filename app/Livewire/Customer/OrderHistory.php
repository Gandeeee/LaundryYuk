<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Review;

class OrderHistory extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Filter & Search
    public $search = '';
    public $statusFilter = '';

    // Modal Properties
    public $selectedOrder;
    public $payment_proof;

    public $rating = 5; // Default bintang 5
    public $comment = '';

    #[Layout('components.layouts.app')]
    #[Title('Riwayat Pesanan - LaundryYuk')]
    public function render()
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
            // 1. Logika Filter Status
            ->when($this->statusFilter, function($query) {
                return $query->where('status', $this->statusFilter);
            })
            // 2. Logika Search ID (Tidak Sensitif)
            ->when($this->search, function($query) {
                // Bersihkan input user: Hapus '#', 'LD', '-', dan Spasi
                // Jadi input "#LD-23", "ld 23", atau "23" akan menjadi "23"
                $cleanId = preg_replace('/[^0-9]/', '', $this->search);
                
                // Cari ID yang cocok
                return $query->where('id', 'like', '%' . $cleanId . '%');
            })
            ->latest()
            ->paginate(10); 

        return view('livewire.customer.order-history', [
            'orders' => $orders
        ]);
    }
    // 1. Method Buka Modal Rating
    public function openRatingModal($id)
    {
        $this->selectedOrder = Order::find($id);
        $this->rating = 5; // Reset ke 5
        $this->comment = ''; // Reset komentar
        
        // Cek jika sudah pernah review, tampilkan datanya (Edit Mode)
        if ($this->selectedOrder->review) {
            $this->rating = $this->selectedOrder->review->rating;
            $this->comment = $this->selectedOrder->review->comment;
        }
    }

    // 2. Method Simpan Rating
    public function saveRating()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Gunakan updateOrCreate agar bisa edit rating
        Review::updateOrCreate(
            ['order_id' => $this->selectedOrder->id],
            [
                'user_id' => Auth::id(),
                'rating' => $this->rating,
                'comment' => $this->comment
            ]
        );

        session()->flash('success', 'Terima kasih atas penilaian Anda!');
        $this->dispatch('close-modal');
    }

    // --- LOGIC DETAIL MODAL ---
    public function showDetail($id)
    {
        $this->selectedOrder = Order::with(['pickupDriver', 'deliveryDriver'])->find($id);
        $this->dispatch('open-detail-modal'); 
    }

    // --- LOGIC PAYMENT MODAL ---
    public function openPaymentModal($id)
    {
        $this->selectedOrder = Order::find($id);
        $this->payment_proof = null; 
    }

    public function savePayment()
    {
        $this->validate([
            'payment_proof' => 'required|image|max:2048', 
        ]);

        if ($this->selectedOrder) {
            $path = $this->payment_proof->store('payment_proofs', 'public');

            $this->selectedOrder->update([
                'payment_proof' => $path,
            ]);

            session()->flash('success', 'Bukti pembayaran berhasil dikirim! Mohon tunggu verifikasi admin.');
            $this->dispatch('close-modal');
        }
    }
}
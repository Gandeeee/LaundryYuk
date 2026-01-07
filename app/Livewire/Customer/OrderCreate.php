<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class OrderCreate extends Component
{
    // Properti untuk menampung input dari form
    public $service_type = '';
    public $pickup_address = '';
    public $phone_number = '';
    public $pickup_date = '';
    public $pickup_time = '';
    public $notes = '';

    #[Layout('components.layouts.app')]
    #[Title('Buat Pesanan Baru - LaundryYuk')]
    public function render()
    {
        return view('livewire.customer.order-create');
    }
    
    public function store()
    {
        // 1. validasi input
        $this->validate([
            'service_type' => 'required',
            'pickup_address' => 'required|min:10',
            'phone_number' => 'required|numeric|min:10',
            
            // --- VALIDASI BARU: TANGGAL ---
            // 'after_or_equal:today' => minimal hari ini
            // 'before_or_equal:+3 days' => maksimal 3 hari ke depan
            'pickup_date' => 'required|date|after_or_equal:today|before_or_equal:+3 days',
            
            // validasi jam tetap sama (09:00 - 21:00)
            'pickup_time' => 'required|date_format:H:i|after_or_equal:09:00|before_or_equal:21:00',
        ], [
            'service_type.required' => 'Mohon pilih jenis layanan.',
            'pickup_address.required' => 'Alamat penjemputan wajib diisi lengkap.',
            'pickup_address.min' => 'Alamat terlalu pendek.',
            
            // pesan error khusus tanggal
            'pickup_date.after_or_equal' => 'Tanggal jemput tidak boleh tanggal lampau.',
            'pickup_date.before_or_equal' => 'Maaf, kami hanya menerima pesanan maksimal untuk 3 hari ke depan.',
            
            // pesan error khusus jam
            'pickup_time.required' => 'Waktu jemput harus diisi.',
            'pickup_time.after_or_equal' => 'Maaf, layanan penjemputan baru buka pukul 09:00 pagi.',
            'pickup_time.before_or_equal' => 'Maaf, layanan penjemputan tutup pukul 21:00 malam.',
        ]);

        // 2. format tanggal & waktu (gabung jadi datetime mysql)
        $pickupSchedule = $this->pickup_date . ' ' . $this->pickup_time . ':00';

        // 3. simpan ke database
        Order::create([
            'user_id' => Auth::id(),
            'customer_name' => Auth::user()->name,
            'phone_number' => $this->phone_number,
            'pickup_address' => $this->pickup_address,
            'service_type' => $this->service_type,
            'pickup_schedule' => $pickupSchedule,
            'notes' => $this->notes,
            'status' => 'MENUNGGU_DIJEMPUT',
            'total_weight' => 0,
            'total_price' => 0,
            'is_paid' => false,
            'is_verified' => false,
        ]);

        // 4. notifikasi sukses
        session()->flash('success', 'Pesanan berhasil dibuat! Driver kami akan segera menjemput.');

        // 5. kembali ke dashboard
        return redirect()->route('customer.dashboard');
    }
}
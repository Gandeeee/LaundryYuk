<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Navbar extends Component
{
    public function render()
    {
        $userId = Auth::id();

        // Ambil notifikasi penting:
        // 1. Menunggu Pembayaran (Tagihan keluar)
        // 2. Driver OTW / Dikirim (Tracking)
        // 3. Selesai (Barang siap)
        $notifications = Order::where('user_id', $userId)
            ->whereIn('status', ['MENUNGGU_PEMBAYARAN', 'DRIVER_OTW', 'DIKIRIM', 'SELESAI_DICUCI', 'TIBA'])
            ->orderBy('updated_at', 'desc')
            ->take(5) // Ambil 5 terbaru saja biar rapi
            ->get();

        $currentCount = $notifications->count();
        
        // Logic Suara: Jika jumlah notifikasi bertambah/berubah
        $lastCount = Session::get('cust_notif_count', 0);
        if ($currentCount > $lastCount) {
            $this->dispatch('play-notification-sound');
        }
        Session::put('cust_notif_count', $currentCount);

        return view('livewire.customer.navbar', [
            'notifications' => $notifications,
            'notifCount' => $currentCount
        ]);
    }
}
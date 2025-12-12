<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Navbar extends Component
{
    public $title = '';

    // Menerima parameter title dari view lain
    public function mount($title = 'Dashboard')
    {
        $this->title = $title;
    }

    public function render()
    {
        // 1. Ambil Notifikasi (Order Baru & Menunggu Bayar)
        $notifications = Order::whereIn('status', ['MENUNGGU_DIJEMPUT', 'MENUNGGU_PEMBAYARAN'])
                              ->where('is_verified', false)
                              ->orderBy('created_at', 'desc')
                              ->get();

        $currentCount = $notifications->count();
        
        // 2. Logic Suara (Cek jika ada notif baru dibanding sesi sebelumnya)
        $lastCount = Session::get('notif_count', 0);

        if ($currentCount > $lastCount) {
            $this->dispatch('play-notification-sound');
        }

        Session::put('notif_count', $currentCount);

        // 3. Render View
        return view('livewire.admin.navbar', [
            'notifications' => $notifications,
            'notifCount' => $currentCount
        ]);
    }
}
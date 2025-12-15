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

        // =========================
        // AMBIL NOTIF BELUM DIBACA
        // =========================
        $notifications = Order::where('user_id', $userId)
            ->where('is_notif_read', false) // 🔥 penting
            ->whereIn('status', [
                'MENUNGGU_PEMBAYARAN',
                'DRIVER_OTW',
                'DIKIRIM',
                'SELESAI_DICUCI',
                'TIBA'
            ])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $currentCount = $notifications->count();

        // =========================
        // LOGIC SUARA NOTIF
        // =========================
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

    // =========================
    // SAAT NOTIF DIKLIK
    // =========================
    public function markAsRead($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // tandai notif sudah dibaca
        $order->update([
            'is_notif_read' => true,
        ]);

        // reset counter session supaya badge update
        Session::put('cust_notif_count', 0);

        // redirect ke detail order
        return redirect()->route('customer.order.detail', $order->id);
    }
}

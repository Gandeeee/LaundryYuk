<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderDetail extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        // keamanan: pastikan order milik user ini
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // ===== TANDAI NOTIF SUDAH DIBACA =====
        // asumsi: ada kolom is_notif_read (boolean)
        if (!$order->is_notif_read) {
            $order->update([
                'is_notif_read' => true,
            ]);
        }

        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.customer.order-detail');
    }
}

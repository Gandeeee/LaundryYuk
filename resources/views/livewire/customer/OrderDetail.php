<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderDetail extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        // 🔐 pastikan order milik user ini
        abort_if($order->user_id !== Auth::id(), 403);

        // ✅ tandai notif sudah dibaca
        if ($order->is_notif_read === false) {
            $order->update([
                'is_notif_read' => true
            ]);
        }

        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.customer.order-detail', [
            'order' => $this->order
        ]);
    }
}

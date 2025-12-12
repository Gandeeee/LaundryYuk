<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function print($id)
    {
        // 1. Ambil data order beserta relasi driver (jika ada)
        $order = Order::with(['user', 'pickupDriver', 'deliveryDriver'])->findOrFail($id);

        // 2. Validasi Keamanan (Security Check)
        $user = Auth::user();

        // Aturan:
        // - Admin BOLEH cetak punya siapa saja.
        // - Customer HANYA BOLEH cetak punya diri sendiri.
        if ($user->role !== 'admin' && $order->user_id !== $user->id) {
            abort(403, 'AKSES DITOLAK: Anda tidak memiliki izin mencetak nota ini.');
        }

        // 3. Tampilkan View Nota
        return view('admin.invoice.print', compact('order'));
    }
}
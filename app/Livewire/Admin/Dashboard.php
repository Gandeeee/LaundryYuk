<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;

class Dashboard extends Component
{
    #[Layout('components.layouts.app')]
    #[Title('Dashboard - LaundryYuk')]
    public function render()
    {
        $currentYear = date('Y');

        // 1. DATA KARTU STATISTIK (Real-time)
        $totalOrders = Order::count();
        
        // Pendapatan hanya dihitung jika status pembayaran LUNAS (is_paid = 1)
        $totalPendapatan = Order::where('is_paid', true)->sum('total_price');
        
        // Pesanan Perlu Proses: Status selain Selesai, Tiba, atau Dibatalkan
        $ordersPerluProses = Order::whereNotIn('status', ['SELESAI_DICUCI', 'TIBA', 'DIBATALKAN'])->count();

        // 2. TABEL ORDER TERBARU (Ambil 5 teratas)
        $recentOrders = Order::latest()->take(5)->get();

        // 3. DATA UNTUK CHART (Sama persis dengan Logic ReportIndex)
        $chartRevenue = $this->getMonthlyRevenue($currentYear);
        $chartOrders = $this->getMonthlyOrders($currentYear);
        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return view('livewire.admin.dashboard', [
            'totalOrders' => $totalOrders,
            'totalPendapatan' => $totalPendapatan,
            'ordersPerluProses' => $ordersPerluProses,
            'recentOrders' => $recentOrders,
            // Kirim data chart ke View sebagai JSON
            'chartRevenue' => json_encode(array_values($chartRevenue)),
            'chartOrders' => json_encode(array_values($chartOrders)),
            'chartLabels' => json_encode($chartLabels),
        ]);
    }

    // Helper: Hitung Pendapatan Bulanan (Sama dengan Report)
    private function getMonthlyRevenue($year)
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = Order::where('is_paid', true)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->sum('total_price');
        }
        return $data;
    }

    // Helper: Hitung Jumlah Order Bulanan
    private function getMonthlyOrders($year)
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();
        }
        return $data;
    }
}
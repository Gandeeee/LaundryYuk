<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportIndex extends Component
{
    public $currentYear;

    public function mount()
    {
        $this->currentYear = date('Y');
    }

    #[Layout('components.layouts.app')]
    #[Title('Laporan Keuangan - LaundryYuk')]
    public function render()
    {
        // 1. DATA RINGKASAN ATAS (CARD)
        $totalRevenue = Order::where('is_paid', true)->sum('total_price');
        $totalOrders = Order::count();
        $completedOrders = Order::whereIn('status', ['SELESAI_DICUCI', 'TIBA'])->count();
        $activeOrders = Order::whereNotIn('status', ['SELESAI_DICUCI', 'TIBA', 'DIBATALKAN'])->count();

        // 2. DATA UNTUK GRAFIK (Chart.js)
        // Kita perlu array berisi 12 angka (Jan-Des)
        $chartRevenue = $this->getMonthlyRevenue();
        $chartOrders = $this->getMonthlyOrders();

        return view('livewire.admin.report-index', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'activeOrders' => $activeOrders,
            'chartRevenue' => json_encode(array_values($chartRevenue)), // Kirim sebagai JSON ke JS
            'chartOrders' => json_encode(array_values($chartOrders)),
            'chartLabels' => json_encode(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'])
        ]);
    }

    // Helper: Hitung Pendapatan per Bulan (Jan-Des)
    private function getMonthlyRevenue()
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = Order::where('is_paid', true)
                ->whereYear('created_at', $this->currentYear)
                ->whereMonth('created_at', $m)
                ->sum('total_price');
        }
        return $data;
    }

    // Helper: Hitung Jumlah Order per Bulan (Jan-Des)
    private function getMonthlyOrders()
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = Order::whereYear('created_at', $this->currentYear)
                ->whereMonth('created_at', $m)
                ->count();
        }
        return $data;
    }

    // 3. FITUR EXPORT CSV
    public function exportCsv()
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($handle, ['Order ID', 'Tanggal', 'Pelanggan', 'Layanan', 'Berat (Kg)', 'Total (Rp)', 'Status', 'Pembayaran']);

            // Query Data (Semua Order diurutkan tanggal)
            Order::orderBy('created_at', 'desc')->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    fputcsv($handle, [
                        '#LD-' . $order->id,
                        $order->created_at->format('Y-m-d H:i'),
                        $order->customer_name,
                        $order->service_type,
                        $order->total_weight,
                        $order->total_price,
                        $order->status,
                        $order->is_paid ? 'LUNAS' : 'BELUM BAYAR'
                    ]);
                }
            });

            fclose($handle);
        }, 'laporan-laundry-' . date('Y-m-d') . '.csv');
    }
}
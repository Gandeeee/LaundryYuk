<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Driver;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OrderIndex extends Component
{
    public $selectedOrderId;
    public $weight; 
    public $price; 
    public $selectedDriverId, $assignmentType;
    public $newStatus;

    // Untuk input manual jika layanan tidak dikenal
    public $manualName, $manualPhone, $manualAddress, $manualService;
    public $manualWeight, $manualPrice;

    public $pricePerUnit = 0; // Harga per Kg atau per Pcs
    public $isAutoCalc = true; // Status apakah bisa hitung otomatis

    public $statusFlow = [
        'MENUNGGU_DIJEMPUT', 
        'DRIVER_OTW', 
        'CUCIAN_DIAMBIL',
        'MENUNGGU_PEMBAYARAN', 
        'PROSES_PENCUCIAN', 
        'SELESAI_DICUCI', 
        'DIKIRIM', 
        'TIBA'
    ];
    // Daftar Harga sesuai OrderCreate.blade.php
    public $priceList = [
        // Layanan Kiloan
        'Cuci Kering' => 5000,
        'Cuci Kering Lipat' => 5500,
        'Cuci Kering Express 6 Jam' => 6000,
        'Cuci Kering Express 3 Jam' => 7000,
        'Cuci Setrika 2 Hari' => 8000, // Value di database
        'Cuci Setrika 3 Hari' => 7000,
        'Cuci Setrika 4 Hari' => 6500,
        'Cuci Setrika Express 6 Jam' => 13000,
        'Cuci Setrika Express 1 Jam' => 10000,
        'Setrika Saja' => 5000,
        'Kering Saja' => 4000,
        
        // Layanan Satuan (Harga fix/estimasi terendah)
        'Satuan Bedcover' => 25000,
        'Satuan Sprei' => 10000,
        'Satuan Tas' => 25000,
        'Satuan Boneka' => 5000,
        'Satuan Sepatu' => 25000,
        'Satuan Karpet' => 30000,
    ];

    // Untuk menyimpan harga manual jika layanan tidak dikenal
    public $manualPricePerUnit = 0;

    #[Layout('components.layouts.app')]
    #[Title('Manajemen Order - LaundryYuk')]
    public function render()
    {
        return view('livewire.admin.order-index', [
            'orders' => Order::with(['user', 'pickupDriver', 'deliveryDriver', 'review'])
                        ->latest()
                        ->get(),
            'drivers' => Driver::where('is_available', true)->get()
        ]);
    }

    // --- LOGIC UTAMA: Saat Modal Tagihan Dibuka ---
    public function openTagihanModal($orderId)
    {
        $order = Order::find($orderId);
        $this->selectedOrderId = $orderId;
        
        // Reset nilai
        $this->weight = $order->total_weight > 0 ? $order->total_weight : '';
        $this->price = $order->total_price > 0 ? $order->total_price : '';
        
        // Cek Harga Layanan
        $service = $order->service_type;
        
        if (array_key_exists($service, $this->priceList)) {
            $this->pricePerUnit = $this->priceList[$service];
            $this->isAutoCalc = true; // Aktifkan kalkulator
        } else {
            $this->pricePerUnit = 0;
            $this->isAutoCalc = false; // Matikan jika layanan tidak dikenal (manual input)
        }
    }

    // --- REAL-TIME CALCULATION ---
    // Jalan otomatis saat Admin mengetik 'weight'
    public function updatedWeight()
    {
        // Hanya hitung jika mode AutoCalc aktif dan input adalah angka
        if ($this->isAutoCalc && is_numeric($this->weight)) {
            $this->price = $this->weight * $this->pricePerUnit;
        }
    }

    public function openDriverModal($orderId, $type)
    {
        $this->selectedOrderId = $orderId;
        $this->assignmentType = $type;
        $this->selectedDriverId = '';
    }

    public function openStatusModal($orderId)
    {
        $this->selectedOrderId = $orderId;
        $order = Order::find($orderId);
        // Set default pilihan ke status saat ini
        $this->newStatus = $order->status;
    }

    public function saveTagihan()
    {
        $this->validate([
            'weight' => 'required|numeric|min:0.1',
            'price' => 'required|numeric|min:1000',
        ]);

        $order = Order::find($this->selectedOrderId);
        $order->update([
            'total_weight' => $this->weight,
            'total_price' => $this->price,
            'status' => ($order->status == 'CUCIAN_DIAMBIL') ? 'MENUNGGU_PEMBAYARAN' : $order->status
        ]);

        session()->flash('success', 'Tagihan berhasil disimpan.');
        $this->dispatch('close-modal');
    }
    
    public function saveDriver()
    {
        $this->validate(['selectedDriverId' => 'required']);
        $order = Order::find($this->selectedOrderId);
        if ($this->assignmentType === 'pickup') {
            $order->pickup_driver_id = $this->selectedDriverId;
            $order->status = 'DRIVER_OTW';
        } else {
            $order->delivery_driver_id = $this->selectedDriverId;
            $order->status = 'DIKIRIM';
        }
        $order->save();
        session()->flash('success', 'Driver berhasil ditugaskan.');
        $this->dispatch('close-modal');
    }

    public function saveStatus()
    {
        $order = Order::find($this->selectedOrderId);
        
        // 1. Dapatkan Index Status Saat Ini & Baru
        $currentIndex = array_search($order->status, $this->statusFlow);
        $newIndex = array_search($this->newStatus, $this->statusFlow);

        // Exception: Status DIBATALKAN boleh kapan saja
        if ($this->newStatus === 'DIBATALKAN') {
            $order->update(['status' => 'DIBATALKAN']);
            session()->flash('success', 'Order telah dibatalkan.');
            $this->dispatch('close-modal');
            return;
        }

        // 2. Validasi Index (Sistem)
        if ($currentIndex === false || $newIndex === false) {
            $this->addError('newStatus', 'Status tidak valid.');
            return;
        }

        // 3. Validasi Alur (Mundur/Loncat)
        if ($newIndex < $currentIndex) {
            $this->dispatch('close-modal');
            session()->flash('error', 'Gagal! Tidak bisa kembali ke status mundur.');
            return; 
        }

        if ($newIndex > $currentIndex + 1) {
            $this->dispatch('close-modal');
            session()->flash('error', 'Gagal! Status harus berurutan, tidak boleh melompat.');
            return;
        }

        // --- VALIDASI DRIVER ---
        if ($this->newStatus === 'DRIVER_OTW' && !$order->pickup_driver_id) {
            $this->dispatch('close-modal');
            session()->flash('error', 'Gagal! Harap tetapkan Driver Jemput terlebih dahulu.');
            return;
        }
        if ($this->newStatus === 'DIKIRIM' && !$order->delivery_driver_id) {
            $this->dispatch('close-modal');
            session()->flash('error', 'Gagal! Harap tetapkan Driver Antar terlebih dahulu.');
            return;
        }

        // --- VALIDASI BARU: CEK TAGIHAN (BERAT & HARGA) ---
        // Jika mau masuk status MENUNGGU_PEMBAYARAN, pastikan sudah ditimbang
        if ($this->newStatus === 'MENUNGGU_PEMBAYARAN') {
            if ($order->total_weight <= 0 || $order->total_price <= 0) {
                $this->dispatch('close-modal');
                // Arahkan admin untuk pakai tombol Tagihan
                session()->flash('error', 'AKSES DITOLAK! Harap input berat & harga terlebih dahulu melalui tombol "Tagihan".');
                return;
            }
        }

        // --- VALIDASI PEMBAYARAN (STRICT PAYMENT) ---
        // Jika posisi sekarang MENUNGGU_PEMBAYARAN dan mau maju, harus sudah diverifikasi
        if ($order->status === 'MENUNGGU_PEMBAYARAN' && $newIndex > $currentIndex) {
            if (!$order->is_paid || !$order->is_verified) {
                $this->dispatch('close-modal');
                session()->flash('error', 'AKSES DITOLAK! Pembayaran belum diverifikasi. Cek bukti transfer & klik "Verif".');
                return;
            }
        }

        // 4. Simpan jika semua aman
        $order->status = $this->newStatus;
        $order->save();

        session()->flash('success', 'Status pesanan berhasil diperbarui.');
        $this->dispatch('close-modal');
    }
    public $proofUrl;

    public function verifyPayment()
    {
        $order = Order::find($this->selectedOrderId);
        
        $order->update([
            'is_paid' => true,
            'is_verified' => true,
            'status' => 'PROSES_PENCUCIAN' // Langsung masuk antrian cuci
        ]);

        session()->flash('success', 'Pembayaran DITERIMA. Order masuk proses pencucian.');
        $this->dispatch('close-modal');
    }

    public function openVerifyModal($orderId)
    {
        $this->selectedOrderId = $orderId;
        $order = Order::find($orderId);
        
        // Ambil path gambar (pastikan storage:link sudah jalan)
        if ($order->payment_proof) {
            $this->proofUrl = asset('storage/' . $order->payment_proof);
        } else {
            $this->proofUrl = null;
        }
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);

        if ($order) {
            // 1. Cek apakah ada file bukti bayar?
            if ($order->payment_proof) {
                // 2. Hapus file fisik dari folder storage (agar tidak jadi sampah)
                if (Storage::disk('public')->exists($order->payment_proof)) {
                    Storage::disk('public')->delete($order->payment_proof);
                }
            }

            // 3. Baru hapus data order dari database
            $order->delete();
            
            session()->flash('success', 'Order dan file bukti bayar berhasil dihapus permanen.');
        }
    }

    // Hitung harga otomatis saat berat diinput (Manual Form)
    public function updatedManualWeight()
    {
        if (is_numeric($this->manualWeight) && $this->manualPricePerUnit > 0) {
            $this->manualPrice = $this->manualWeight * $this->manualPricePerUnit;
        }
    }

    // Set harga satuan saat layanan dipilih
    public function updatedManualService()
    {
        if (array_key_exists($this->manualService, $this->priceList)) {
            $this->manualPricePerUnit = $this->priceList[$this->manualService];
            // Recalculate jika berat sudah diisi
            $this->updatedManualWeight();
        } else {
            $this->manualPricePerUnit = 0;
        }
    }
    public function saveManualOrder()
    {
        $this->validate([
            'manualName' => 'required|min:3',
            'manualPhone' => 'required|numeric|min:10',
            'manualAddress' => 'required|min:5',
            'manualService' => 'required',
            'manualWeight' => 'required|numeric|min:0.1',
            'manualPrice' => 'required|numeric|min:0',
        ], [
            'manualName.required' => 'Nama pelanggan wajib diisi.',
            'manualService.required' => 'Pilih layanan laundry.',
        ]);

        Order::create([
            'user_id' => null, 
            'customer_name' => $this->manualName,
            'phone_number' => $this->manualPhone,
            'pickup_address' => $this->manualAddress,
            'service_type' => $this->manualService,
            
            // 1. Status langsung loncat ke cuci
            'status' => 'PROSES_PENCUCIAN', 
            
            // 2. Waktu set hari ini
            'pickup_schedule' => Carbon::now(), 
            
            // 3. Anggap langsung LUNAS & TERVERIFIKASI (Karena bayar di kasir)
            'is_paid' => true,      
            'is_verified' => true,  
            
            // --------------------------------
            
            'total_weight' => $this->manualWeight,
            'total_price' => $this->manualPrice,
            'notes' => 'Walk-in Order (Manual - Langsung Lunas)',
        ]);

        // 3. Update Pesan Sukses
        session()->flash('success', 'Order Walk-in berhasil! Status langsung PROSES PENCUCIAN (LUNAS).');
        
        $this->dispatch('close-modal');
        $this->resetManualInput();
    }

    public function resetManualInput()
    {
        $this->manualName = '';
        $this->manualPhone = '';
        $this->manualAddress = '';
        $this->manualService = '';
        $this->manualWeight = '';
        $this->manualPrice = '';
        $this->manualPricePerUnit = 0;
    }
    
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            // id unik order, misal nanti jadi #ld-1
            $table->id();
            
            // id user yang memesan (relasi ke tabel users)
            // nullable: karena ada fitur 'walk-in' (pesan manual tanpa akun) di admin
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // snapshot nama & no hp (penting jika user dihapus, data order tetap ada namanya)
            $table->string('customer_name');
            $table->string('phone_number');
            
            // alamat penjemputan & pengantaran
            $table->text('pickup_address')->nullable();
            
            // jenis layanan (dari dropdown select di form order)
            $table->string('service_type')->nullable();
            
            // jadwal jemput yang diinginkan customer
            $table->dateTime('pickup_schedule')->nullable();
            
            // berat cucian (diisi admin saat ditimbang)
            $table->float('total_weight')->default(0);
            
            // total harga (diisi admin atau hitungan sistem)
            $table->decimal('total_price', 12, 2)->default(0);
            
            // status pesanan laundry (enum agar konsisten)
            // urutan status sesuai logika bisnis di frontend lama
            $table->enum('status', [
                'MENUNGGU_DIJEMPUT', 
                'DRIVER_OTW', 
                'CUCIAN_DIAMBIL',
                'MENUNGGU_PEMBAYARAN', 
                'PROSES_PENCUCIAN', 
                'SELESAI_DICUCI', 
                'DIKIRIM', 
                'TIBA',
                'DIBATALKAN'
            ])->default('MENUNGGU_DIJEMPUT');
            
            // status pembayaran & verifikasi
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_verified')->default(false);
            
            // path file bukti transfer (jika upload manual)
            $table->string('payment_proof')->nullable();
            
            // catatan tambahan dari customer
            $table->text('notes')->nullable();
            
            // driver yang jemput & driver yang antar (relasi ke tabel drivers)
            $table->foreignId('pickup_driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->foreignId('delivery_driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

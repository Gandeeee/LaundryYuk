<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    // izinkan semua kolom ini diisi oleh controller/livewire
    protected $fillable = [
        'user_id',
        'customer_name',
        'phone_number',
        'pickup_address',
        'service_type',
        'pickup_schedule',
        'total_weight',
        'total_price',
        'status',
        'is_paid',
        'is_verified',
        'payment_proof',
        'notes',
        'pickup_driver_id',
        'delivery_driver_id',
    ];

    // ubah tipe data secara otomatis saat diambil dari database
    protected $casts = [
        'pickup_schedule' => 'datetime', // biar enak format tanggalnya nanti
        'total_weight' => 'float',
        'total_price' => 'decimal:2',
        'is_paid' => 'boolean',
        'is_verified' => 'boolean',
    ];

    // relasi: order ini milik siapa (customer)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // relasi: siapa driver yang menjemput
    public function pickupDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'pickup_driver_id');
    }

    // relasi: siapa driver yang mengantar
    public function deliveryDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'delivery_driver_id');
    }
    public function review(){
        return $this->hasOne(Review::class);
    }
}
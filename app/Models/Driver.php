<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    // kolom yang boleh diisi manual
    protected $fillable = [
        'name',
        'phone',
        'is_available',
    ];

    // casting tipe data agar otomatis jadi boolean (true/false) bukan 1/0
    protected $casts = [
        'is_available' => 'boolean',
    ];

    // relasi: driver ini menjemput order mana saja
    public function pickupOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'pickup_driver_id');
    }

    // relasi: driver ini mengantar order mana saja
    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_driver_id');
    }
}
<?php

namespace App\Models;

// use illuminate\contracts\auth\mustverifyemail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * the attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // tambah role agar bisa diisi saat register
    ];

    /**
     * the attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // relasi: satu user (customer) bisa punya banyak order laundry
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // helper: cek apakah user ini admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // helper: cek apakah user ini customer
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}
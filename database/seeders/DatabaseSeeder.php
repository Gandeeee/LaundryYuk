<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun ADMIN
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com', // Email untuk login
            'password' => Hash::make('12345678'), // Password untuk login
            'role' => 'admin', 
        ]);
        
        // 2. Buat Akun CUSTOMER
        User::create([
            'name' => 'User Testing',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
        ]);

        // 3. Buat Data DRIVER (Agar tabel driver tidak kosong)
        Driver::create([
            'name' => 'Pak Jarwo',
            'phone' => '081234567890',
            'is_available' => true,
        ]);

        Driver::create([
            'name' => 'Bu Narmi',
            'phone' => '089876543210',
            'is_available' => true,
        ]);
    }
}
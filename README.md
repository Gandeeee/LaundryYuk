# Laundry Yuk! - BACKEND UPDATE

Aplikasi manajemen laundry berbasis web menggunakan Laravel & Livewire.
Project ini dibuat untuk memenuhi tugas kuliah Sistem Informasi Telkom University.

---

## Tech Stack 

Aplikasi ini dibangun menggunakan teknologi modern untuk memastikan performa dan kemudahan pengembangan:

- **Framework:** Laravel 11 + Livewire 3 (Fullstack)
- **Frontend Styling:** Bootstrap 5 + Custom CSS
- **Database:** MySQL
- **Tools:** Git + Composer

---

## Status Project

### Backend: **FULLY FUNCTIONAL (100%)**
Semua fitur utama secara logika backend sudah berjalan mulus, mencakup:
- **Manajemen Order:** Create, Update, Delete Order (CRUD).
- **Tracking:** Pelacakan status cucian real-time.
- **Payment:** Integrasi pembayaran otomatis via Xendit.
- **Admin & Driver:** Dashboard khusus admin dan manajemen tugas kurir.
- **Rating & Review:** Customer dapat memberi ulasan setelah barang diterima. 
- **[BARU] Cetak Nota:** Fitur cetak invoice/struk laundry (Thermal Printer Ready) lengkap dengan status bayar. 



## Cara Install & Menjalankan Project (Untuk Tim)

Ikuti langkah ini jika baru pertama kali clone project:

1.  **Clone Repository**
    ```bash
    git clone [https://github.com/Gandeeee/LaundryYuk.git](https://github.com/Gandeeee/LaundryYuk.git)
    cd LaundryYuk
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    ```

3.  **Setup Environment**
    Salin file `.env` dan generate key:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Konfigurasi Database**
    - Buat database di MySQL bernama `laundry_yuk_db`.
    - Atur koneksi di file `.env`.

5.  **Migrasi Database**
    ```bash
    php artisan migrate:fresh --seed
    ```

6.  **Jalankan Aplikasi**
    ```bash
    php artisan serve
    ```

## Catatan Lain
- **Akun Admin Default:** `admin@gmail.com` / `12345678`

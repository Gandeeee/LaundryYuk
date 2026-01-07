# 🧺 Laundry Yuk! - Modern Laundry Management System

![Project Status](https://img.shields.io/badge/Status-Fully%20Functional-success?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4E56A0?style=for-the-badge&logo=livewire&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

**Aplikasi manajemen laundry berbasis web** yang dirancang untuk efisiensi operasional dan kemudahan pelanggan. Project ini dikembangkan untuk memenuhi Tugas Besar Sistem Informasi **Telkom University**.

---

## 🛠️ Tech Stack & Tools

Aplikasi ini dibangun menggunakan teknologi terkini untuk menjamin performa, keamanan, dan skalabilitas:

| Kategori | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Framework** | ![Laravel 11](https://img.shields.io/badge/Laravel_11-FF2D20?style=flat-square&logo=laravel&logoColor=white) | Core Backend Framework |
| **Fullstack** | ![Livewire 3](https://img.shields.io/badge/Livewire_3-4E56A0?style=flat-square&logo=livewire&logoColor=white) | Reactive Frontend Components |
| **Styling** | ![Bootstrap 5](https://img.shields.io/badge/Bootstrap_5-7952B3?style=flat-square&logo=bootstrap&logoColor=white) | Responsive UI Design |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) | Relational Database Management |
| **Language** | ![PHP 8.2+](https://img.shields.io/badge/PHP_8.2+-777BB4?style=flat-square&logo=php&logoColor=white) | Server-side Language |
| **Asset** | ![Vite](https://img.shields.io/badge/Vite-646CFF?style=flat-square&logo=vite&logoColor=white) | Frontend Tooling |

---

## 🚀 Fitur Unggulan (Project Status)

Backend saat ini berstatus **100% Fully Functional** dengan fitur mencakup:

### 👤 Customer Features
- [x] **Order Booking:** Form pemesanan laundry yang mudah dengan estimasi harga.
- [x] **Real-time Tracking:** Pantau status cucian (Dijemput -> Dicuci -> Diantar).
- [x] **History Transaksi:** Riwayat pesanan lengkap dengan detail.
- [x] **Rating & Review:** Memberikan ulasan layanan setelah selesai.

### 🛡️ Admin & Operasional
- [x] **Dashboard Monitoring:** Grafik ringkasan order harian/bulanan.
- [x] **Management Order (CRUD):** Update status laundry (Proses/Selesai).
- [x] **Driver Assignment:** Penugasan kurir untuk antar-jemput.
- [x] **Laporan Keuangan:** Rekapitulasi pendapatan otomatis.
- [x] **Cetak Nota (Thermal Ready):** [BARU] Invoice format struk kasir.

---

## 💻 Cara Install & Menjalankan (Local Development)

Ikuti langkah-langkah berikut untuk menjalankan project di komputer lokal Anda:

### 1. Clone Repository
```bash
git clone [https://github.com/Gandeeee/LaundryYuk.git](https://github.com/Gandeeee/LaundryYuk.git)
cd LaundryYuk
````

### 2\. Install Dependencies

Pastikan Composer dan NPM sudah terinstall di laptop Anda.

```bash
composer install
npm install
```

### 3\. Konfigurasi Environment

Salin file `.env` dan generate application key.

```bash
cp .env.example .env
php artisan key:generate
```

> **Penting:** Buka file `.env` lalu sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan konfigurasi MySQL lokal Anda.

### 4\. Setup Database

Buat database baru di MySQL (misal: `laundry_yuk_db`), lalu jalankan migrasi & seeder:

```bash
php artisan migrate:fresh --seed
```

*(Perintah ini akan mengisi database dengan data dummy untuk testing)*

### 5\. Jalankan Aplikasi

Buka dua terminal terpisah untuk menjalankan server Backend dan Frontend asset:

**Terminal 1 (Backend):**

```bash
php artisan serve
```


Akses aplikasi di browser: `http://127.0.0.1:8000`

-----

## 🔑 Akun Demo (Default)

Gunakan akun berikut untuk masuk ke sistem setelah melakukan seeding:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin@gmail.com` | `12345678` |
| **Customer** | `user@gmail.com` | `12345678` |

-----

## PEMBAGIAN TUGAS 
# Laundry Yuk! – Daftar Fitur Sistem

Dokumen ini berisi daftar fitur utama yang dikembangkan pada aplikasi **Laundry Yuk!**, beserta deskripsi singkat dan penanggung jawab (PIC) masing-masing fitur.

---

## 1. AUTH
Mengelola seluruh proses akun pengguna, meliputi pendaftaran akun baru, proses login dan logout, autentikasi, serta otorisasi berbasis peran (Customer dan Admin). Fitur ini juga mencakup pengamanan kredensial pengguna untuk menjaga keamanan data akun.

**PIC:** Adis

---

## 2. CUSTOMER ORDER
Menyediakan fitur bagi pengguna untuk melakukan pemesanan layanan laundry. Pengguna dapat memilih jenis layanan, menentukan lokasi penjemputan, memilih tanggal penjemputan, serta menentukan jam penjemputan sesuai kebutuhan.

**PIC:** Adis

---

## 3. ORDER ADMIN
Mengelola proses pemesanan dari sisi admin, termasuk manajemen data order, pembaruan status pesanan, serta memastikan data pengiriman terintegrasi dengan data pesanan yang dibuat oleh pengguna.

**PIC:** Krisna

---

## 4. DASHBOARD ADMIN
Menyediakan dashboard admin yang interaktif dan ringkas, berisi ringkasan data penting yang berasal dari modul order admin dan laporan admin untuk memudahkan pemantauan operasional.

**PIC:** Krisna

---

## 5. PAYMENT INTEGRATION
Mengelola alur pembayaran antara admin dan pengguna, melakukan sinkronisasi data pembayaran, memvalidasi status pembayaran (belum upload bukti, menunggu verifikasi, atau sudah terverifikasi), serta menyimpan riwayat transaksi pembayaran secara terstruktur.

**PIC:** Gandhi

---

## 6. NOTIFICATION INTEGRATION
Menyediakan sistem notifikasi otomatis antara pengguna dan admin terkait pembaruan status pesanan secara real time dengan memanfaatkan Livewire.

**PIC:** Gandhi

---

## 7. RATING & RIWAYAT ORDER
Menyediakan fitur ulasan dan penilaian dari pelanggan terhadap layanan laundry yang diberikan, mengelola data rating, serta menampilkan riwayat pemesanan bagi customer.

**PIC:** Zaki

---

## 8. DRIVER MANAGEMENT
Mengelola data driver yang mencakup proses penambahan, pembaruan, dan penghapusan data driver, serta memastikan data driver terhubung dengan status order yang sedang berjalan.

**PIC:** Zaki

---

## 9. INVOICE EXPORT
Menyediakan fitur ekspor invoice untuk setiap transaksi, memastikan tampilan dan fungsi modal invoice berjalan dengan baik, serta terintegrasi dengan peran admin dan customer.

**PIC:** Gandhi

---

## 10. BUSINESS REPORT
Menyediakan fitur pengelolaan laporan penjualan dan memastikan data laporan dapat diekspor dalam format CSV untuk kebutuhan analisis dan dokumentasi.

**PIC:** Adis

---

## 11. LANDING PAGE
Menyediakan halaman landing page sebagai halaman awal aplikasi untuk menyambut pengguna sebelum melakukan proses login atau registrasi.

**PIC:** Krisna


-----


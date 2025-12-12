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


### Next Step: Scenario Testing
Meskipun backend sudah selesai, **WAJIB** dilakukan *Scenario Testing* lebih lanjut (Coba input data aneh, flow order bolak-balik, dll) untuk memastikan tidak ada error tersembunyi (Bug Hunting).

---

## Known Issues & UI Polish (Perlu Perbaikan)

Berikut adalah komponen tampilan yang perlu diperbaiki oleh tim Frontend/UI:

- [ ] **Navbar Customer (Mobile):** Tampilan sidebar/menu di layar HP masih tertutup atau layoutnya berantakan. Perlu fix CSS responsif.
- [ ] <img width="265" height="595" alt="image" src="https://github.com/user-attachments/assets/6c81e5d2-c4e9-40f3-97a1-afa8eade8467" />

- [ ] **Tombol Logout Customer:** Saat ini ketika tombol logout/keluar ditekan ada warna biru yang tidak sesuai tema yang jadi latar belakang. Tolong ubah warnanya agar *blend-in* dengan desain merah/putih LaundryYuk.
- [ ] <img width="1200" height="1600" alt="image" src="https://github.com/user-attachments/assets/a0169c5b-36da-446c-8ec9-f34e433dab3a" />


---

##  Git Workflow Rules (PENTING!)

Agar kode tidak bentrok dan aman, tolong ikuti aturan ini:

1.  **JANGAN PUSH KE BRANCH `main` LANGSUNG!** 
2.  Setiap mengerjakan fitur atau perbaikan, **BUAT BRANCH BARU**.
    - Contoh: `git checkout -b fix-navbar-mobile`
3.  Jika sudah selesai, lakukan **Push** di branch tersebut.
4.  Buat **Pull Request (PR)** di GitHub untuk di-merge ke `main`.

---

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
- **Konten Presentasi:** Pembagian materi slide akan diinfokan menyusul.
- **Akun Admin Default:** `admin@gmail.com` / `12345678`
- **KONTEN PEMBAGIAN PRESENTASI AKAN DIKIRIMKAN DI GRUP**

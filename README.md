# ♟️ Chess War

[![Laravel Version](https://img.shields.io/badge/Laravel-v12.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-v8.2-777BB4?logo=php&logoColor=white)](https://php.net)
[![Filament](https://img.shields.io/badge/Filament-v3.x-F59E0B?logo=laravel&logoColor=white)](https://filamentphp.com)
[![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?logo=docker&logoColor=white)](https://www.docker.com)

**Chess War** adalah aplikasi web permainan catur interaktif berbasis Laravel yang menghadirkan variasi catur unik dengan sistem **Power Draft**. Pemain dapat memilih kartu/kekuatan khusus (*power*) untuk mengubah mekanik permainan, lalu bertanding melawan komputer yang ditenagai oleh engine catur **WukongJS**.

---

## 👤 Identitas & Dokumentasi

Berikut adalah identitas pengembang beserta daftar laporan yang berada di direktori `docs/`:

* **Nama:** Ramsay Abelson
* **NIM:** 20240801042
* **Daftar Laporan & Dokumen:**
  * 📄 [Laporan Awal Project Akhir](docs/LAPORAN-AWAL-PROJECT-AKHIR.pdf)
  * 📄 [Business Requirements Document (BRD)](docs/BRD_ChessWar_fix.pdf)
  * 📄 [Product Requirements Document (PRD)](docs/PRD_ChessWar.pdf)
  * 📄 [Capstone Project ChessWar](docs/Capstone_ChessWar_fix.pdf)

---

## 📝 Deskripsi Project

Project ini menawarkan pengalaman bermain catur konvensional yang digabungkan dengan elemen permainan kartu taktis. Sebelum pertandingan dimulai, pemain akan melewati fase pemilihan kartu power (*power draft*) yang memberikan kemampuan spesial pada bidak catur tertentu.

Pada implementasi saat ini, aplikasi telah memiliki:
* Halaman Landing Page yang informatif mengenai variasi catur.
* Sistem Autentikasi Pengguna lengkap (Register, Login, Logout).
* Dashboard Pemain untuk memulai pertandingan.
* Papan permainan catur interaktif (*drag & drop*) yang terintegrasi dengan mesin catur.
* Panel Admin modern berbasis Filament 3 untuk manajemen data.

---

## 🚀 Fitur Utama Aplikasi

### 1. Landing Page (`/`)
Halaman interaktif untuk mengenalkan konsep **Random Power Draft Chess**:
* Deskripsi konsep visual dan alur permainan (*Shuffle -> Pick -> Active*).
* Penjelasan detail mengenai 6 variasi kartu power:
  * ⚡ **Blink Knight**
  * 🛡️ **Super Rook**
  * 👑 **Undying King**
  * 🌀 **Confused Pawn**
  * 🔮 **Omni Queen**
  * 🎭 **Grey Bishop**

### 2. Autentikasi Pengguna
* **Register (`/register`):** Pendaftaran akun baru dengan validasi data input lengkap serta penyimpanan password yang aman menggunakan hashing.
* **Login (`/login`):** Validasi credentials pengguna untuk masuk ke sistem.
* **Logout (`/logout`):** Penghapusan session aktif, regenerasi CSRF token untuk keamanan, dan pengalihan kembali ke halaman login.

### 3. Dashboard Pemain (`/dashboard`)
* Halaman khusus pengguna terautentikasi.
* Menampilkan informasi profil dan shortcut menu seperti **Play Now**, **Quick Match**, dan **Continue Playing**.

### 4. Papan Game Catur (`/game`)
* Papan catur berbasis web yang interaktif (mendukung *drag & drop* bidak).
* Terintegrasi dengan rules engine **Chess.js** dan visual board **Chessboard.js**.
* Lawan komputer ditenagai oleh engine **WukongJS** dengan delay pencarian langkah terbaik sekitar 1 detik.
* **Fitur Kontrol Match:**
  * `New Game` – Memulai ulang permainan.
  * `Force Move` – Memaksa engine catur untuk langsung melangkah.
  * `Undo Move` – Membatalkan langkah terakhir.
  * `Flip Board` – Membalik sudut pandang papan catur (Hitam/Putih).

### 5. Admin Panel (`/admin`)
Panel administratif berbasis **Filament 3** yang mencakup:
* Manajemen Pengguna melalui `UserResource`.
* Manajemen Role & Permission menggunakan **Filament Shield** (`super_admin` & `user`).
* Audit & Logging aktivitas menggunakan **Filament Logger**.
* Kustomisasi visual panel admin dengan plugin Filament tambahan.

### 6. Struktur Database Match
Aplikasi memiliki tabel `matches` untuk menyimpan riwayat pertandingan dengan kolom:
* `user_id` – Relasi ke pemain.
* `is_win` – Hasil akhir pertandingan.
* `total_time` – Durasi permainan.
* `power_type` – Kartu power yang aktif selama pertandingan.

---

## 🛠️ Teknologi & Stack

### Backend Stack
* **Language:** PHP 8.2+
* **Framework:** Laravel 12.x (dengan Laravel Blade)
* **Authentication:** Custom manual authentication via `AuthController`
* **Admin Panel:** Filament v3
* **Authorization:** Spatie Laravel Permission (via Filament Shield)
* **Database:** MariaDB 10.11

### Frontend Stack
* **Asset Bundler:** Vite
* **Styling:** CSS & Tailwind CSS (Bootstrap 4 khusus untuk halaman game)
* **Library:** jQuery, Chessboard.js, Chess.js
* **Chess Engine:** WukongJS (JavaScript Chess Engine)

### Dev & Ops Tools
* **Containerization:** Docker & Docker Compose
* **Web Server:** Nginx (PHP-FPM)
* **Package Manager:** Composer & NPM
* **Testing:** Pest Testing Framework
* **Code Style:** Laravel Pint

---

## 📂 Struktur Folder Utama

```text
chess_war/
├── docker-compose.yml     # Konfigurasi Docker Services
├── nginx/                 # Konfigurasi Web Server Nginx
├── php/                   # Dockerfile & Konfigurasi PHP
├── db/                    # Data Database Persistent (Local)
└── src/                   # Source Code Laravel
    ├── app/
    │   ├── Filament/      # Resource & Page Admin Panel
    │   ├── Http/
    │   │   └── Controllers/
    │   └── Models/        # Model Eloquent
    ├── database/
    │   ├── migrations/    # Migrasi Skema Database
    │   └── seeders/       # Seed Data Awal (User, Role)
    ├── public/
    │   ├── css/
    │   ├── js/
    │   └── vendor/        # Library Papan Catur & Engine
    ├── resources/
    │   └── views/         # Template Blade
    └── routes/            # Route HTTP Aplikasi
```

---

## 🛣️ Daftar Route Utama

| Method | Route | Fungsi | Keterangan |
| :--- | :--- | :--- | :--- |
| **GET** | `/` | Landing Page | Informasi produk & konsep power |
| **GET** | `/login` | Halaman Login | Form masuk akun |
| **POST**| `/login` | Proses Login | Validasi & pembuatan session |
| **GET** | `/register`| Halaman Daftar | Form pembuatan akun baru |
| **POST**| `/register`| Proses Daftar | Penyimpanan user baru |
| **POST**| `/logout` | Proses Keluar | Hancurkan session |
| **GET** | `/dashboard`| Dashboard | Menu & shortcut game (Auth) |
| **GET** | `/game` | Halaman Game | Area bertanding vs engine (Auth) |
| **GET** | `/admin` | Panel Admin | Manajemen Filament (Admin Only) |

---

## ⚙️ Panduan Menjalankan Project

Ikuti langkah-langkah di bawah ini untuk menjalankan project di lingkungan lokal menggunakan Docker:

### 1. Jalankan Container Docker
Pastikan service Docker sudah aktif di komputer Anda, lalu jalankan perintah berikut di root folder project:
```bash
docker compose up -d --build
```

### 2. Masuk ke Container PHP
Semua perintah Laravel Artisan dan package manager PHP harus dieksekusi di dalam container:
```bash
docker compose exec php bash
```

### 3. Pasang Dependensi Backend (PHP)
Di dalam container PHP, jalankan:
```bash
composer install
```

### 4. Pasang Dependensi Frontend (Node.js)
Di dalam container PHP, jalankan:
```bash
npm install
```

### 5. Konfigurasi Environment File
Jika file `.env` belum ada di dalam folder `src/`, salin template dari `.env.example` dan generate application key:
```bash
cp .env.example .env
php artisan key:generate
```

### 6. Inisialisasi Database & Seeder
Jalankan command khusus berikut untuk membersihkan, memigrasi, serta mengisi database dengan data awal secara otomatis:
```bash
php artisan project:init
```
*Perintah ini akan menjalankan migrasi database baru (`migrate:fresh`), meng-generate permissions Filament Shield, mengisi data seeder awal, dan membersihkan cache aplikasi.*

### 7. Build Asset Frontend
* **Development Mode (Hot Reload):**
  ```bash
  npm run dev
  ```
* **Production Mode (Compiled Assets):**
  ```bash
  npm run build
  ```

### 8. Akses Aplikasi Web
Buka peramban (browser) dan akses alamat berikut:
```text
https://chess_war.test
```
> 💡 **Tips:** Pastikan Anda telah menambahkan pemetaan domain `127.0.0.1 chess_war.test` pada file `hosts` sistem operasi lokal Anda.

---

## 🧪 Panduan Pengujian (Testing)

Project ini dilengkapi dengan pengujian otomatis menggunakan **Pest**. Untuk menjalankan seluruh suite pengujian, jalankan perintah berikut di dalam container PHP:
```bash
php artisan test
```

---

## 📌 Akun Default Pengujian

Untuk mempermudah pengujian fitur, gunakan akun bawaan berikut setelah menjalankan database seeder:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `admin@admin.com` | `password` |
| **Pemain (User)**| `user@admin.com` | `password` |

---

## 📝 Catatan Implementasi & Pengembangan

* Konsep utama variasi power draft saat ini baru ditampilkan sebagai informasi di halaman landing page.
* Mekanik game pada route `/game` saat ini berjalan sebagai permainan catur standar melawan engine WukongJS. Pengembangan kartu power interaktif dapat ditambahkan sebagai fitur berkelanjutan.
* Tabel `matches` telah disiapkan untuk merekam data pertandingan, integrasi database ke frontend game dapat disesuaikan pada pengembangan tahap selanjutnya.
* Struktur `UserChessResource` pada panel Filament telah siap digunakan, kolom/tabel form dapat disesuaikan dengan kebutuhan administrasi data ke depan.

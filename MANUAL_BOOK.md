# 📖 Buku Panduan (Manual Book) - Sistem Antrean KIR Kendaraan

Selamat datang di Buku Panduan Sistem Antrean KIR (Uji Kendaraan Bermotor). Dokumen ini bertujuan untuk memudahkan pengguna, pengelola, maupun tim IT dalam memahami, mengakses, dan menggunakan sistem ini.

---

## 1. Pendahuluan
Sistem Antrean KIR Kendaraan adalah platform berbasis web yang dibangun dengan Laravel untuk mengelola antrean uji kendaraan bermotor secara efisien. Sistem ini memfasilitasi pendaftaran antrean, pemanggilan antrean secara real-time, pencatatan hasil uji, dan manajemen data pengguna dengan fitur notifikasi WhatsApp yang terintegrasi (menggunakan Fonnte).

---

## 2. Hak Akses (Roles)
Sistem ini membagi pengguna ke dalam tiga hak akses utama, di mana masing-masing memiliki fungsi dan tampilan antarmuka yang berbeda:

1. **Admin (Administrator)**
   Memiliki kontrol penuh terhadap sistem. Bertugas mengelola data inti seperti jadwal uji, akun pengguna, konfigurasi sistem, dan melihat laporan secara keseluruhan.
   
2. **Penguji (Examiner)**
   Petugas di lapangan yang melakukan inspeksi kendaraan. Bertugas memanggil nomor antrean, memperbarui status antrean, dan memasukkan data hasil uji (Lulus / Tidak Lulus) ke dalam sistem.
   
3. **Pendaftar (Participant)**
   Masyarakat umum atau pemilik kendaraan yang ingin melakukan uji KIR. Dapat mendaftarkan kendaraan, mengambil nomor antrean pada jadwal yang tersedia, dan memantau status serta hasil uji secara mandiri.

---

## 3. Fitur Utama Sistem
- **Manajemen Antrean Real-Time**: Antrean dapat dipantau dan dipanggil secara langsung tanpa perlu menyegarkan halaman secara manual.
- **Pendaftaran Uji Kendaraan Online**: Pendaftar bisa memilih jadwal dan mendaftarkan kendaraan dari mana saja.
- **Input Hasil Uji Terintegrasi**: Penguji dapat dengan mudah memasukkan dan menyimpan hasil uji yang langsung terhubung dengan data kendaraan dan pendaftar.
- **Notifikasi WhatsApp Otomatis**: Pendaftar akan menerima pesan via WhatsApp terkait konfirmasi pendaftaran, pengingat antrean, dan hasil ujian.
- **Laporan & Statistik**: Dashboard interaktif untuk admin guna memantau beban antrean dan tingkat kelulusan.

---

## 4. Panduan Penggunaan Berdasarkan Peran

### 👨‍💻 A. Panduan Admin
1. **Login**: Akses halaman login dan masuk menggunakan kredensial Admin.
2. **Dashboard**: Pada halaman utama, Anda akan melihat ringkasan statistik (jumlah pendaftar, kendaraan diuji, antrean aktif).
3. **Manajemen Pengguna (Users)**: Tambahkan, ubah, atau nonaktifkan akun Penguji maupun Pendaftar.
4. **Jadwal Uji (Test Schedule)**: Buka menu jadwal untuk membuka tanggal ujian baru, menetapkan kuota maksimal, dan mengatur status operasional hari tersebut.
5. **Konfigurasi WhatsApp**: Akses menu **Settings > WhatsApp** untuk memasukkan API Key dan nomor perangkat yang digunakan untuk mengirim pesan otomatis.
6. **Laporan & Audit**: Akses menu pelaporan untuk mengunduh laporan hasil uji kendaraan atau memantau riwayat perubahan (Audit Log).

### 🕵️‍♂️ B. Panduan Penguji (Examiner)
1. **Login**: Masuk menggunakan kredensial akun Penguji.
2. **Menu Antrean (Queue)**: 
   - Anda akan melihat daftar kendaraan yang sedang menunggu.
   - Klik tombol **"Panggil"** untuk memanggil nomor antrean berikutnya.
   - Ubah status menjadi **"Proses"** saat kendaraan mulai diperiksa, atau **"Lewati"** jika pendaftar tidak hadir saat dipanggil.
3. **Input Hasil Uji**: 
   - Setelah selesai melakukan inspeksi fisik kendaraan, pergi ke menu **Test Results**.
   - Cari kendaraan berdasarkan nomor antrean atau nomor polisi.
   - Masukkan status pengujian (**Lulus / Tidak Lulus**) beserta catatan perbaikan jika kendaraan tidak lulus.
   - Simpan data agar hasil dapat dilihat oleh pendaftar.

### 👤 C. Panduan Pendaftar (Participant)
1. **Pendaftaran / Login**: Jika Anda belum memiliki akun, lakukan registrasi terlebih dahulu. Masuk ke dalam sistem jika sudah memiliki akun.
2. **Manajemen Kendaraan**: Pada menu **Vehicles**, tambahkan data kendaraan Anda (Plat nomor, tipe kendaraan, tahun, dll).
3. **Daftar Antrean**: 
   - Masuk ke menu pendaftaran ujian.
   - Pilih kendaraan yang ingin diuji.
   - Pilih tanggal dan jadwal yang masih memiliki kuota.
   - Sistem akan menerbitkan **Nomor Antrean** dan Anda (mungkin) akan menerima notifikasi WhatsApp.
4. **Pantau Status & Hasil**: Pada hari H, Anda dapat melihat status antrean di halaman dashboard Anda. Setelah pengujian selesai, Anda dapat melihat surat/keterangan hasil ujian di menu **Test Results** atau **Riwayat**.

---

## 5. Panduan Instalasi (Untuk Tim IT / Developer)

Jika Anda ingin menjalankan atau memodifikasi project ini di perangkat lokal, ikuti langkah singkat berikut. *(Pastikan PHP 8.1+, Composer, Node.js, dan MySQL telah terpasang).*

```bash
# 1. Clone Repositori (Jika belum)
git clone <url-repo-project> kir-antrean
cd kir-antrean

# 2. Instal Dependensi
composer install
npm install

# 3. Persiapkan Environment
cp .env.example .env
php artisan key:generate

# 4. Atur Konfigurasi Database
# Buka file .env dan sesuaikan kredensial DB (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 5. Migrasi dan Seeding Data (Membuat tabel & data awal)
php artisan migrate --seed

# 6. Jalankan Server Development
php artisan serve

# 7. Compile Assets Front-end (Jalankan di terminal terpisah)
npm run dev
```

> **Catatan**: Untuk panduan instalasi yang lebih mendalam, silakan merujuk pada file [SETUP_CHECKLIST.md](./SETUP_CHECKLIST.md).

---

## 5.A Panduan Instalasi Berdasarkan Versi Laravel

Sistem ini dirancang untuk kompatibel dengan **Laravel 10, 11, 12, dan 13**. Berikut adalah panduan instalasi lengkap untuk setiap versi.

### 📋 Tabel Kompatibilitas Versi

| Versi Laravel | Persyaratan PHP | Status | Support Hingga |
|---|---|---|---|
| **Laravel 10** | 8.1+ | ✅ Didukung | Januari 2025 |
| **Laravel 11** | 8.2+ | ✅ Didukung (LTS) | Januari 2027 |
| **Laravel 12** | 8.3+ | ✅ Didukung | Q3 2025 |
| **Laravel 13** | 8.3+ | ✅ Didukung | Q3 2026 |

---

### ⚙️ A. Instalasi untuk Laravel 10

#### Persyaratan:
- PHP 8.1 atau lebih tinggi
- Composer
- Node.js dan npm
- MySQL 5.7+

#### Langkah-langkah Instalasi:

```bash
# 1. Clone Repository
git clone <url-repo-project> kir-antrean
cd kir-antrean

# 2. Pastikan composer.json sudah menggunakan Laravel 10
# File composer.json akan otomatis dikonfigurasi untuk Laravel 10+

# 3. Install Dependencies PHP
composer install

# 4. Install Dependencies JavaScript
npm install

# 5. Setup Environment File
cp .env.example .env

# 6. Generate Application Key
php artisan key:generate

# 7. Konfigurasi Database di .env
# Buka file .env dengan text editor dan isi:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=kir_antrean
# DB_USERNAME=root
# DB_PASSWORD=

# 8. Jalankan Database Migrations
php artisan migrate

# 9. Seeding Data Awal (Opsional)
php artisan db:seed

# 10. Verifikasi Versi Laravel
php artisan --version
# Output: Laravel Framework 10.x.x

# 11. Jalankan Development Server
php artisan serve
# Akses: http://127.0.0.1:8000

# 12. Compile Assets Frontend (Terminal Terpisah)
npm run dev
```

#### Perintah Utilities untuk Laravel 10:

```bash
# Cek versi Laravel
php artisan tinker
App\Helpers\LaravelVersionHelper::getVersion()

# Jalankan tests
php artisan test

# Cache clear
php artisan cache:clear
php artisan config:clear
php artisan optimize:clear
```

---

### ⚙️ B. Instalasi untuk Laravel 11 (Direkomendasikan)

Laravel 11 adalah rilis LTS (Long Term Support) dengan dukungan hingga Januari 2027.

#### Persyaratan:
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js dan npm
- MySQL 5.7+

#### Langkah-langkah Instalasi:

```bash
# 1. Clone Repository
git clone <url-repo-project> kir-antrean
cd kir-antrean

# 2. Install Dependencies PHP
composer install

# 3. Install Dependencies JavaScript
npm install

# 4. Setup Environment File
cp .env.example .env

# 5. Generate Application Key
php artisan key:generate

# 6. Konfigurasi Database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=kir_antrean
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan Database Migrations
php artisan migrate

# 8. Seeding Data (Opsional)
php artisan db:seed

# 9. Verifikasi Versi Laravel
php artisan --version
# Output: Laravel Framework 11.x.x

# 10. Jalankan Development Server
php artisan serve

# 11. Compile Assets Frontend (Terminal Terpisah)
npm run dev
```

#### Keunggulan Laravel 11:
- ✅ Performa lebih cepat (~5-10% improvement)
- ✅ Error reporting yang lebih baik
- ✅ LTS - Dukungan jangka panjang hingga 2027
- ✅ Kompatibel penuh dengan codebase yang sudah ada

#### Perintah Utilities untuk Laravel 11:

```bash
# Cek kompatibilitas versi
php artisan version:check

# Cek info detail versi
php artisan version:check --verbose

# Test kompatibilitas
php artisan test tests/Feature/LaravelCompatibilityTest.php

# Optimasi production
php artisan optimize
```

---

### ⚙️ C. Instalasi untuk Laravel 12

Laravel 12 adalah rilis terbaru dengan peningkatan performa dan fitur-fitur baru.

#### Persyaratan:
- PHP 8.3 atau lebih tinggi
- Composer
- Node.js dan npm
- MySQL 5.7+

#### Langkah-langkah Instalasi:

```bash
# 1. Verifikasi Versi PHP
php --version
# Pastikan PHP 8.3 atau lebih tinggi

# 2. Clone Repository
git clone <url-repo-project> kir-antrean
cd kir-antrean

# 3. Update composer.json untuk Laravel 12
# Edit composer.json dan ubah:
# "laravel/framework": "^12.0"

# 4. Install Dependencies PHP
composer install

# 5. Install Dependencies JavaScript
npm install

# 6. Setup Environment File
cp .env.example .env

# 7. Generate Application Key
php artisan key:generate

# 8. Konfigurasi Database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=kir_antrean
# DB_USERNAME=root
# DB_PASSWORD=

# 9. Jalankan Database Migrations
php artisan migrate

# 10. Seeding Data (Opsional)
php artisan db:seed

# 11. Verifikasi Versi Laravel
php artisan --version
# Output: Laravel Framework 12.x.x

# 12. Jalankan Development Server
php artisan serve

# 13. Compile Assets Frontend (Terminal Terpisah)
npm run dev
```

#### Fitur Baru Laravel 12:
- ✅ Performance improvements
- ✅ Improved database layer
- ✅ Enhanced validation
- ✅ Better error handling

#### Perintah Utilities untuk Laravel 12:

```bash
# Cek versi dan kompatibilitas
php artisan version:check

# Jalankan migration test
php artisan migrate:status

# Test performa
php artisan test --profile

# Optimize untuk production
php artisan optimize
php artisan vendor:publish --tag=config
```

---

### ⚙️ D. Instalasi untuk Laravel 13

Laravel 13 adalah versi terbaru dengan fitur-fitur paling mutakhir.

#### Persyaratan:
- PHP 8.3 atau lebih tinggi
- Composer
- Node.js dan npm
- MySQL 5.7+

#### Langkah-langkah Instalasi:

```bash
# 1. Verifikasi Versi PHP
php --version
# Pastikan PHP 8.3 atau lebih tinggi

# 2. Clone Repository
git clone <url-repo-project> kir-antrean
cd kir-antrean

# 3. Update composer.json untuk Laravel 13
# Edit composer.json dan ubah:
# "laravel/framework": "^13.0"

# 4. Clear Composer Cache (untuk update terbaru)
composer clear-cache

# 5. Install Dependencies PHP
composer install

# 6. Install Dependencies JavaScript
npm install

# 7. Setup Environment File
cp .env.example .env

# 8. Generate Application Key
php artisan key:generate

# 9. Konfigurasi Database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=kir_antrean
# DB_USERNAME=root
# DB_PASSWORD=

# 10. Jalankan Database Migrations
php artisan migrate

# 11. Seeding Data (Opsional)
php artisan db:seed

# 12. Verifikasi Versi Laravel
php artisan --version
# Output: Laravel Framework 13.x.x

# 13. Jalankan Development Server
php artisan serve

# 14. Compile Assets Frontend (Terminal Terpisah)
npm run dev
```

#### Fitur Terbaru Laravel 13:
- ✅ Routing yang lebih powerful
- ✅ Database query optimization
- ✅ Enhanced API responses
- ✅ Improved middleware handling

#### Perintah Utilities untuk Laravel 13:

```bash
# Cek versi
php artisan version:check --verbose

# Test kompatibilitas penuh
php artisan test

# Cek health aplikasi
php artisan tinker
> health_check()

# Optimize untuk production
php artisan optimize:clear
php artisan optimize
```

---

### 🔄 Upgrade dari Versi Lama ke Versi Baru

Jika Anda sudah memiliki project di Laravel 10 dan ingin upgrade ke versi yang lebih baru, ikuti langkah berikut:

#### Upgrade dari Laravel 10 → Laravel 11:

```bash
# 1. Backup Database
mysqldump -u root -p kir_antrean > backup_$(date +%s).sql

# 2. Commit Changes
git commit -am "Pre-upgrade Laravel 11 backup"

# 3. Update composer.json
# Edit dan ubah: "laravel/framework": "^11.0"

# 4. Install Dependencies Baru
composer update

# 5. Clear Caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan optimize:clear

# 6. Jalankan Migrations
php artisan migrate

# 7. Test Aplikasi
php artisan test

# 8. Verifikasi
php artisan version:check --verbose
```

#### Upgrade dari Laravel 11 → Laravel 12:

```bash
# 1. Backup Database
mysqldump -u root -p kir_antrean > backup_$(date +%s).sql

# 2. Commit Changes
git commit -am "Pre-upgrade Laravel 12 backup"

# 3. Update composer.json
# Edit dan ubah: "laravel/framework": "^12.0"

# 4. Install Dependencies Baru
composer update

# 5. Clear Caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan optimize:clear

# 6. Jalankan Migrations
php artisan migrate

# 7. Jalankan Tests
php artisan test

# 8. Verifikasi
php artisan version:check --verbose
```

#### Upgrade dari Laravel 12 → Laravel 13:

```bash
# 1. Backup Database
mysqldump -u root -p kir_antrean > backup_$(date +%s).sql

# 2. Commit Changes
git commit -am "Pre-upgrade Laravel 13 backup"

# 3. Update composer.json
# Edit dan ubah: "laravel/framework": "^13.0"

# 4. Clear Composer Cache
composer clear-cache

# 5. Install Dependencies Baru
composer update

# 6. Clear Caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan optimize:clear

# 7. Jalankan Migrations
php artisan migrate

# 8. Jalankan Tests
php artisan test

# 9. Verifikasi
php artisan version:check --verbose
```

---

### ✅ Checklist Instalasi untuk Semua Versi

Setelah menyelesaikan instalasi, pastikan Anda telah:

- [ ] PHP versi sesuai terinstall dan verified
- [ ] Composer dan dependencies terinstall dengan sukses
- [ ] Node.js dan npm terinstall
- [ ] Database MySQL/MariaDB berjalan
- [ ] Environment file (.env) sudah dikonfigurasi
- [ ] Application key sudah di-generate
- [ ] Database migrations sudah dijalankan
- [ ] Development server dapat diakses (http://127.0.0.1:8000)
- [ ] Frontend assets sudah di-compile
- [ ] Tests berjalan tanpa error
- [ ] Dapat login dengan credential default (jika ada)
- [ ] Semua API endpoints dapat diakses

### 🆘 Troubleshooting Instalasi

#### Problem: "Class not found" Error
```bash
# Solusi:
composer dump-autoload
php artisan optimize:clear
```

#### Problem: Database Connection Error
```bash
# Verifikasi kredensial di .env
# Test connection:
php artisan tinker
> DB::connection()->getPdo()
```

#### Problem: Migration Fails
```bash
# Cek migration status:
php artisan migrate:status

# Reset dan jalankan ulang:
php artisan migrate:reset
php artisan migrate --seed
```

#### Problem: Sanctum Authentication Not Working
```bash
# Republish Sanctum configuration:
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force
php artisan migrate
```

#### Problem: Port 8000 Sudah Terpakai
```bash
# Gunakan port lain:
php artisan serve --port=8001
# Akses: http://127.0.0.1:8001
```

---

## 6. Referensi Dokumen Lainnya
Untuk keperluan teknis yang lebih spesifik, silakan merujuk pada dokumen-dokumen berikut di direktori utama (root) proyek:
- 📑 **`API_DOCUMENTATION.md`**: Referensi rute (endpoint) dan respon REST API.
- ⚙️ **`CONFIG_SETUP.md`**: Panduan konfigurasi tingkat lanjut terkait variabel environment.
- 🚀 **`DEPLOYMENT_GUIDE.md`**: Langkah-langkah untuk mendeploy sistem ke server production (VPS/Shared Hosting).
- 💾 **`BACKUP_RECOVERY.md`**: Strategi untuk mencadangkan database dan pemulihan data jika terjadi kendala.

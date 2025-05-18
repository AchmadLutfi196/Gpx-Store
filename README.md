# GPX Store

GPX Store adalah platform e-commerce berbasis Laravel yang memungkinkan pengguna untuk menjelajahi, membeli, dan mengelola produk secara online. Website ini dibangun menggunakan Laravel 12 dengan Filament sebagai admin panel, serta integrasi pembayaran dengan Midtrans.

## Fitur Utama
- Manajemen pengguna dan autentikasi
- Manajemen produk, kategori, dan merek
- Keranjang belanja dan checkout
- Sistem pembayaran dengan Midtrans
- Admin panel menggunakan Filament
- Pengelolaan transaksi dan pesanan
- Sistem newsletter dan email marketing
- Wishlist dan manajemen ulasan produk
- Manajemen biaya pengiriman
- Manajemen promo dan kode diskon
- Dashboard analitik untuk melihat performa toko

## Instalasi

### 1. Clone Repository
```sh
git clone https://github.com/AchmadLutfi196/Gpx-Store.git
cd Gpx-Store
```

### 2. Install Dependensi
```sh
composer install
npm install && npm run dev
```

### 3. Konfigurasi Environment
Buat file `.env` dari template:
```sh
cp .env.example .env
```

Edit file `.env` untuk mengatur:
- Koneksi database
- Konfigurasi email (untuk newsletter dan notifikasi)
- Kredensial Midtrans

### 4. Generate Key dan Migrate Database
```sh
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### 5. Menjalankan Aplikasi
```sh
php artisan serve
```
Akses website di `http://127.0.0.1:8000`

## Login ke Filament
Filament digunakan sebagai admin panel. Untuk mengaksesnya:
1. Buka: `http://127.0.0.1:8000/admin`
2. Gunakan akun admin default:
   - **Email:** `admin@example.com`
   - **Password:** `password`

Jika tidak ada akun admin, buat dengan:
```sh
php artisan make:filament-user
```

## Konfigurasi Filament Shield
```sh
php artisan shield:super-admin
php artisan shield:generate --all
```

## Sistem Newsletter
GPX Store memiliki sistem newsletter terintegrasi yang memungkinkan:
- Pendaftaran pelanggan untuk newsletter
- Pengiriman email campaign massal
- Pengelolaan subscriber
- Analitik performa email

Untuk mengatur jadwal pengiriman email:
```sh
php artisan schedule:work
```

## Sistem Pembayaran
Integrasi dengan Midtrans memungkinkan berbagai metode pembayaran:
- Kartu kredit/debit
- Transfer bank
- E-wallet
- QRIS

Konfigurasi kredensial Midtrans di `.env`:
```
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

## Kontribusi
Jika ingin berkontribusi, fork repo ini dan buat pull request dengan perubahan yang ingin diusulkan.

---
Dikembangkan oleh **Lutfi Madhani** 🚀


# GPX Store

GPX Store adalah platform e-commerce berbasis Laravel yang memungkinkan pengguna untuk menjelajahi, membeli, dan mengelola produk secara online. Website ini dibangun menggunakan Laravel 12 dengan Filament sebagai admin panel, serta integrasi pembayaran dengan Midtrans.

## Fitur Utama
- Manajemen pengguna dan autentikasi
- Manajemen produk, kategori, dan merek
- Keranjang belanja dan checkout
- Sistem pembayaran dengan Midtrans
- Admin panel menggunakan Filament
- Pengelolaan transaksi dan pesanan

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
Edit `.env` dan atur konfigurasi database serta Midtrans:
```sh
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gpx_store
DB_USERNAME=root
DB_PASSWORD=

MIDTRANS_SERVER_KEY=your_midtrans_server_key
MIDTRANS_CLIENT_KEY=your_midtrans_client_key
```

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

## Kontribusi
Jika ingin berkontribusi, fork repo ini dan buat pull request dengan perubahan yang ingin diusulkan.

---
Dikembangkan oleh **Lutfi Madhani** 🚀


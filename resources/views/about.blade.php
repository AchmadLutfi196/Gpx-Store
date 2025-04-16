@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4 text-center text-primary">Tentang Kami</h1>
            
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h2 class="h4 text-secondary">Tentang Gpx-Store</h2>
                    <p class="text-muted">
                        Gpx-Store adalah toko online terpercaya yang menyediakan berbagai produk berkualitas tinggi. 
                        Didirikan pada tahun 2023, kami berkomitmen untuk memberikan pengalaman belanja online 
                        terbaik kepada pelanggan kami.
                    </p>
                    
                    <p class="text-muted">
                        Dengan fokus pada layanan pelanggan yang istimewa dan produk berkualitas premium, 
                        kami terus berusaha menjadi pilihan utama bagi konsumen Indonesia.
                    </p>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h2 class="h4 text-secondary">Visi Kami</h2>
                    <p class="text-muted">
                        Menjadi platform e-commerce terkemuka yang menyediakan produk berkualitas tinggi 
                        dengan harga terjangkau untuk semua konsumen Indonesia.
                    </p>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h2 class="h4 text-secondary">Misi Kami</h2>
                    <ul class="list-unstyled text-muted">
                        <li><i class="bi bi-check-circle-fill text-success"></i> Menyediakan produk dengan kualitas terbaik</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Memberikan layanan pelanggan yang luar biasa</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Memastikan keamanan dalam bertransaksi online</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Mengembangkan hubungan jangka panjang dengan pelanggan dan pemasok kami</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h4 text-secondary">Hubungi Kami</h2>
                    <p class="text-muted">
                        Jika Anda memiliki pertanyaan atau masukan, jangan ragu untuk menghubungi kami:
                    </p>
                    <ul class="list-unstyled text-muted">
                        <li><strong>Email:</strong> <a href="mailto:info@gpx-store.com" class="text-decoration-none text-primary">info@gpx-store.com</a></li>
                        <li><strong>Telepon:</strong> <a href="tel:+6281234567890" class="text-decoration-none text-primary">+62 812 3456 7890</a></li>
                        <li><strong>Alamat:</strong> Jl. Contoh No. 123, Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
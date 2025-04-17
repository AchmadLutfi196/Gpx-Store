@extends('layouts.app')

@section('content')
<div class="about-us-wrapper">
    <!-- Hero Header Section -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white py-16 mb-10">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-center">{{ $aboutUs->title ?? 'Tentang Kami' }}</h1>
            <div class="w-24 h-1 bg-white mx-auto mt-4"></div>
        </div>
    </div>
    
    <div class="container mx-auto px-4 pb-16">
        <!-- About Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
            <div>
                <div class="about-content">
                    <h2 class="text-2xl font-bold text-blue-600 mb-4 relative pb-3">Tentang Gpx-Store</h2>
                    <div class="text-gray-600">
                        {!! nl2br(e($aboutUs->introduction ?? 'Gpx-Store adalah toko online terpercaya yang menyediakan berbagai produk berkualitas tinggi. Didirikan pada tahun 2023, kami berkomitmen untuk memberikan pengalaman belanja online terbaik kepada pelanggan kami.')) !!}
                    </div>
                </div>
            </div>
            <div>
                <div class="rounded-lg overflow-hidden shadow-lg">
                    <img src="{{ asset('images/logo.png') }}" alt="Gpx Store" class="w-full h-auto object-cover">
                </div>
            </div>
        </div>
        
        <!-- Vision & Mission Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
            <div>
                <div class="bg-white rounded-lg shadow-lg p-8 h-full border-t-4 border-blue-500 transition-transform hover:-translate-y-2 duration-300">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-eye text-blue-500 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-center mb-4">Visi Kami</h2>
                    <div class="text-gray-600 text-center">
                        {!! nl2br(e($aboutUs->vision ?? 'Menjadi platform e-commerce terkemuka yang menyediakan produk berkualitas tinggi dengan harga terjangkau untuk semua konsumen Indonesia.')) !!}
                    </div>
                </div>
            </div>
            <div>
                <div class="bg-white rounded-lg shadow-lg p-8 h-full border-t-4 border-purple-500 transition-transform hover:-translate-y-2 duration-300">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-bullseye text-purple-500 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-center mb-4">Misi Kami</h2>
                    <div class="text-gray-600 text-center">
                        {!! nl2br(e($aboutUs->mission ?? '
                        • Menyediakan produk dengan kualitas terbaik
                        • Memberikan layanan pelanggan yang luar biasa
                        • Memastikan keamanan dalam bertransaksi online
                        • Mengembangkan hubungan jangka panjang dengan pelanggan dan pemasok kami')) !!}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Team Section -->
        <div class="bg-gray-50 rounded-xl p-8 mb-16">
            <h2 class="text-3xl font-bold text-center mb-2">Tim Kami</h2>
            <div class="w-24 h-1 bg-blue-500 mx-auto mb-10"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div>
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg transition-transform hover:-translate-y-2 duration-300">
                        <div class="h-64 overflow-hidden">
                            <img src="{{ asset('images/team/ceo.jpg') }}" alt="CEO" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-gray-800">Ahmad Ramadhan</h3>
                            <p class="text-blue-500 font-medium mb-3">CEO & Founder</p>
                            <p class="text-gray-600 text-sm mb-4">Berpengalaman lebih dari 10 tahun di bidang e-commerce dan retail.</p>
                            <div class="flex justify-center space-x-3">
                                <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-colors">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                                <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-colors">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg transition-transform hover:-translate-y-2 duration-300">
                        <div class="h-64 overflow-hidden">
                            <img src="{{ asset('images/team/cto.jpg') }}" alt="CTO" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-gray-800">Budi Setiawan</h3>
                            <p class="text-blue-500 font-medium mb-3">CTO</p>
                            <p class="text-gray-600 text-sm mb-4">Ahli teknologi dengan keahlian dalam pengembangan platform e-commerce.</p>
                            <div class="flex justify-center space-x-3">
                                <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-colors">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                                <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-colors">
                                    <i class="fab fa-github"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg transition-transform hover:-translate-y-2 duration-300">
                        <div class="h-64 overflow-hidden">
                            <img src="{{ asset('images/team/marketing.jpg') }}" alt="Marketing Director" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-gray-800">Siti Aisyah</h3>
                            <p class="text-blue-500 font-medium mb-3">Marketing Director</p>
                            <p class="text-gray-600 text-sm mb-4">Spesialis pemasaran digital dengan fokus pada pertumbuhan bisnis.</p>
                            <div class="flex justify-center space-x-3">
                                <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-colors">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                                <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-colors">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <div class="bg-white rounded-lg shadow-lg p-8 h-full">
                    <h2 class="text-2xl font-bold text-blue-600 mb-6 relative pb-3">Hubungi Kami</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-map-marker-alt text-blue-500"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Alamat</h4>
                                <p class="text-gray-600">Jl. Contoh No. 123, Jakarta, Indonesia</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-phone text-blue-500"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Telepon</h4>
                                <p class="text-gray-600">+62 812 3456 7890</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-blue-500"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Email</h4>
                                <p class="text-gray-600">info@gpx-store.com</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3 mt-8">
                        <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="rounded-lg overflow-hidden shadow-lg h-full">
                    <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.382123456789!2d112.723083!3d-7.12775!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd803f3c3c3c3c3%3A0x3d2ad6e1e0e9bcc8!2sUniversitas%20Trunojoyo%20Madura!5e0!3m2!1sen!2sid!4v1681653719146!5m2!1sen!2sid"
                        width="100%" 
                        height="100%" 
                        style="border:0; min-height: 400px" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Tambahan style untuk elemen khusus halaman about */
    .about-content h2::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background-color: #3b82f6;
    }
    
    @media (max-width: 640px) {
        .about-us-wrapper .container {
            padding-left: 16px;
            padding-right: 16px;
        }
    }
</style>
@endsection
@extends('layouts.app')

@section('styles')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    /* Custom Animation Styles */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    
    @keyframes pulse-ring {
        0% { transform: scale(0.85); opacity: 0.6; }
        50% { transform: scale(1); opacity: 1; }
        100% { transform: scale(0.85); opacity: 0.6; }
    }
    
    /* Animation Classes */
    .animate-fade-in-up { animation: fadeInUp 1s ease-out forwards; }
    .animate-blob { animation: blob 7s infinite; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-pulse-ring { animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    
    /* Animation Delays */
    .animation-delay-2000 { animation-delay: 2s; }
    .animation-delay-4000 { animation-delay: 4s; }
    
    /* Parallax Effect */
    .parallax {
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        position: relative;
    }
    
    /* Custom Utility Classes */
    .bg-gradient-primary {
        background: linear-gradient(to right, #4f46e5, #1d4ed8);
    }
    
    .text-gradient {
        background: linear-gradient(to right, #60a5fa, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .transition-all-300 {
        transition: all 0.3s ease;
    }
    
    .ripple-bg {
        position: relative;
        overflow: hidden;
    }
    
    .ripple-bg::after {
        content: '';
        position: absolute;
        width: 200%;
        height: 200%;
        top: -50%;
        left: -50%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 60%);
        animation: ripple 15s linear infinite;
        z-index: 0;
    }
    
    @keyframes ripple {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .scroll-indicator {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
    }
    
    .scroll-indicator::before {
        content: '';
        width: 40px;
        height: 40px;
        position: absolute;
        top: -20px;
        left: -8px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Product Card Effects */
    .product-card {
        transition: all 0.4s ease;
        will-change: transform;
    }
    
    .product-card:hover {
        transform: translateY(-12px);
    }
    
    .product-card .quick-view {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }
    
    .product-card:hover .quick-view {
        opacity: 1;
        transform: translateY(0);
    }
    
    .product-card .product-img {
        transform: scale(1);
        transition: transform 0.6s cubic-bezier(0.215, 0.61, 0.355, 1);
    }
    
    .product-card:hover .product-img {
        transform: scale(1.08);
    }
    
    .badge-new {
        position: absolute;
        top: 12px;
        right: 12px;
        background: linear-gradient(to right, #ff1a6c, #ff5e62);
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        box-shadow: 0 4px 6px rgba(255, 26, 108, 0.3);
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Category card effects */
    .category-card {
        position: relative;
        overflow: hidden;
    }
    
    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, transparent 60%);
        z-index: 1;
        transition: opacity 0.4s ease;
    }
    
    .category-card:hover::before {
        opacity: 0.9;
    }
    
    /* Hero elements */
    .hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(40px);
        opacity: 0.2;
        mix-blend-mode: multiply;
    }
    
    /* Scroll appearance animation */
    .scroll-appear {
        opacity: 0;
        transform: translateY(30px);
    }
    
    /* Wave animation */
    .wave-container {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
    }
    
    .wave {
        position: relative;
        width: 100%;
        height: 60px;
    }
    
    .wave-path {
        animation: wave 20s linear infinite;
    }
    
    @keyframes wave {
        0% { transform: translateX(0); }
        50% { transform: translateX(-50%); }
        100% { transform: translateX(0); }
    }
    
    /* Benefit cards */
    .benefit-card {
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .benefit-card:hover {
        transform: translateY(-10px) scale(1.03);
    }
    
    .benefit-icon {
        transition: all 0.3s ease;
    }
    
    .benefit-card:hover .benefit-icon {
        transform: rotateY(180deg);
    }
    
    /* Testimonial card effects */
    .testimonial-card {
        position: relative;
        transition: all 0.3s ease;
    }
    
    .testimonial-card::after {
        content: '\201C';
        position: absolute;
        top: 20px;
        left: 20px;
        font-size: 5rem;
        color: #f3f4f6;
        font-family: 'Georgia', serif;
        line-height: 1;
        opacity: 0.3;
        z-index: 0;
    }
    
    .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .float-button {
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.5);
    }
</style>
@endsection

@section('content')
    <!-- Hero Section with Parallax Effect -->
    <div class="relative overflow-hidden h-screen">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-indigo-900 overflow-hidden ripple-bg">
            <!-- Animated Blobs -->
            <div class="hero-blob bg-blue-400 w-96 h-96 top-1/4 left-1/4 animate-blob animation-delay-2000"></div>
            <div class="hero-blob bg-purple-500 w-96 h-96 top-1/3 right-1/4 animate-blob animation-delay-4000"></div>
            <div class="hero-blob bg-pink-500 w-96 h-96 bottom-1/4 left-1/2 animate-blob"></div>
            
            <!-- Abstract Lines -->
            <svg class="absolute w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0,50 Q25,30 50,50 T100,50" stroke="white" stroke-width="0.5" fill="none"/>
                <path d="M0,30 Q35,70 70,30 T100,30" stroke="white" stroke-width="0.5" fill="none"/>
                <path d="M0,70 Q35,30 70,70 T100,70" stroke="white" stroke-width="0.5" fill="none"/>
            </svg>
        </div>

        <div class="relative h-full flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                <div class="inline-block mb-6 animate-float">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-white bg-opacity-20 backdrop-blur-sm text-white text-sm font-medium">
                        <span class="mr-2 relative">
                            <span class="absolute inset-0 animate-ping rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        Koleksi Terbaru 2025
                    </span>
                </div>
                
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-white mb-6">
                    <span class="block mb-2">Koleksi Tas</span>
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-purple-300">Eksklusif GPX</span>
                </h1>
                
                <p class="mt-6 max-w-lg mx-auto text-xl text-blue-100">
                    Temukan tas berkualitas premium dengan desain terkini untuk gaya penampilanmu
                </p>
                
                <div class="mt-10 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('shop') }}" 
                       class="relative inline-flex items-center justify-center px-8 py-3 text-base font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 md:py-4 md:text-lg md:px-10 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl group overflow-hidden">
                        <span class="relative z-10">Belanja Sekarang</span>
                        <span class="absolute inset-0 bg-white z-0"></span>
                        <span class="absolute inset-0 bg-gradient-to-r from-blue-100 to-blue-200 transform scale-x-0 origin-left transition-transform duration-300 group-hover:scale-x-100 z-0"></span>
                    </a>
                    
                    <a href="#featured" 
                       class="relative inline-flex items-center justify-center px-8 py-3 text-base font-medium rounded-md text-white border-2 border-white border-opacity-30 hover:border-opacity-70 md:py-4 md:text-lg md:px-10 transition-all duration-300 transform hover:scale-105 backdrop-blur-sm">
                        <span>Lihat Koleksi</span>
                        <svg class="ml-2 w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Animated Scroll Indicator -->
        <div class="scroll-indicator">
            <a href="#featured" class="text-white flex flex-col items-center">
                <span class="text-sm mb-2 animate-pulse">Scroll</span>
                <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Benefits Bar with Floating Animation -->
    <div class="relative bg-white py-16 -mt-12 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-8">
                @foreach([
                    ['icon' => 'check', 'title' => 'Kualitas Premium', 'desc' => 'Material terbaik', 'color' => 'blue', 'delay' => 0],
                    ['icon' => 'currency-dollar', 'title' => 'Harga Terbaik', 'desc' => 'Garansi harga termurah', 'color' => 'green', 'delay' => 100],
                    ['icon' => 'truck', 'title' => 'Gratis Ongkir', 'desc' => 'Min. pembelian 500k', 'color' => 'purple', 'delay' => 200],
                    ['icon' => 'shield-check', 'title' => 'Garansi 1 Tahun', 'desc' => 'Tanpa ribet', 'color' => 'pink', 'delay' => 300]
                ] as $benefit)
                <div class="benefit-card bg-white p-6 rounded-xl shadow-lg" data-aos="fade-up" data-aos-delay="{{ $benefit['delay'] }}">
                    <div class="flex flex-col items-center text-center">
                        <div class="benefit-icon bg-{{ $benefit['color'] }}-100 p-4 rounded-full mb-4">
                            <svg class="h-8 w-8 text-{{ $benefit['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $benefit['icon'] === 'check' ? '5 13l4 4L19 7' : 
                                    ($benefit['icon'] === 'currency-dollar' ? '12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' : 
                                    ($benefit['icon'] === 'truck' ? '20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' : 'shield-check')) }}"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $benefit['title'] }}</h3>
                        <p class="text-gray-600">{{ $benefit['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Featured Categories with Staggered Animation -->
    <section id="featured" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-3">KATEGORI UNGGULAN</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">
                    <span class="block relative">
                        Koleksi Kategori
                        <span class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-blue-600 rounded"></span>
                    </span>
                </h2>
                <p class="mt-6 max-w-2xl text-xl text-gray-500 mx-auto">
                    Temukan tas sesuai kebutuhan Anda
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($categories->take(6) as $index => $category)
                <div class="category-card rounded-xl overflow-hidden shadow-xl" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <div class="relative aspect-w-16 aspect-h-10">
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
                             class="w-full h-80 object-cover transition-transform duration-700 transform hover:scale-110">
                    </div>
                    <div class="absolute inset-0 flex flex-col justify-end p-8 z-10">
                        <div data-aos="fade-right" data-aos-delay="{{ $index * 100 + 200 }}">
                            <h3 class="text-2xl font-bold text-white mb-2">{{ $category->name }}</h3>
                            <p class="text-gray-200 mb-4 max-w-xs">{{ $category->description ?? 'Koleksi tas ' . $category->name . ' dengan berbagai pilihan model dan warna.' }}</p>
                        </div>
                        <a href="{{ route('shop', ['category' => $category->id]) }}" 
                           class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-medium rounded-md hover:bg-blue-50 transition-all duration-300 transform translate-y-0 hover:translate-y-0 hover:scale-105 shadow-lg max-w-max">
                            <span>Jelajahi Koleksi</span>
                            <svg class="ml-2 w-4 h-4 transition-all duration-300 transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Trending Collection Banner -->
    <section class="relative py-16 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl overflow-hidden">
                <div class="absolute right-0 top-0 -mt-20 -mr-20">
                    <svg width="400" height="400" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="text-white opacity-10">
                        <path fill="currentColor" d="M42,-72.2C54.9,-65.2,66,-55.3,76.4,-42.8C86.9,-30.2,96.6,-15.1,95.9,-0.4C95.2,14.3,84,28.6,73.3,42.3C62.6,56,52.4,69.1,39,76.3C25.7,83.6,9.1,85,-5.9,80.9C-20.9,76.9,-34.4,67.5,-46.6,56.8C-58.8,46.1,-69.7,34.1,-76.7,19.4C-83.8,4.7,-87.1,-12.6,-82.1,-27.5C-77.1,-42.5,-63.8,-55,-48.7,-61.4C-33.5,-67.9,-16.7,-68.2,-0.3,-67.7C16.2,-67.1,32.4,-65.7,42,-72.2Z" transform="translate(100 100)" />
                    </svg>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 items-center">
                    <div class="p-12 md:p-16" data-aos="fade-right">
                        <span class="inline-block px-3 py-1 bg-white bg-opacity-20 text-white rounded-full text-sm font-semibold mb-4 backdrop-blur-sm">KOLEKSI TERBARU</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Koleksi Trending 2025</h2>
                        <p class="text-blue-100 mb-8 text-lg">Tas eksklusif dengan desain unggulan yang menjadi tren fashion tahun ini.</p>
                        <a href="{{ route('shop', ['collection' => 'trending']) }}" 
                           class="inline-flex items-center px-6 py-3 bg-white text-blue-700 font-medium rounded-md transition-all duration-300 transform hover:scale-105 shadow-lg">
                            Lihat Koleksi
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                    
                    <div class="relative h-full" data-aos="fade-left">
                        <img src="{{ asset('images/trending-collection.jpg') }}" alt="Trending Collection" class="w-full h-full object-cover object-center md:rounded-l-3xl" style="min-height: 400px;">
                        <div class="absolute bottom-0 right-0 m-8 bg-white bg-opacity-90 backdrop-blur-sm p-4 rounded-lg shadow-lg">
                            <div class="text-sm font-semibold text-blue-800">Mulai dari</div>
                            <div class="text-3xl font-bold text-blue-600">Rp 299.000</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- New Arrivals with Floating Cards -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-16">
                <div class="mb-6 md:mb-0" data-aos="fade-right">
                    <span class="inline-block px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-sm font-semibold mb-3">BARU DATANG</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 relative">
                        Produk Terbaru
                        <span class="absolute -bottom-2 left-0 w-24 h-1 bg-pink-500 rounded"></span>
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-2xl">
                        Koleksi tas terbaru kami dengan desain eksklusif dan kualitas premium
                    </p>
                </div>
                <a href="{{ route('shop') }}" data-aos="fade-left" 
                   class="px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-pink-500 to-purple-500 hover:from-pink-600 hover:to-purple-600 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg hover:shadow-xl">
                    <span>Lihat Semua Produk</span>
                    <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($latestProducts->take(8) as $index => $product)
                <div class="product-card bg-white rounded-xl shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <div class="relative overflow-hidden">
                        <img class="product-img h-64 w-full object-cover" 
                             src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}">
                        <div class="badge-new">BARU</div>
                        
                        <!-- Quick View Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-30 opacity-0 transition-opacity duration-300 flex items-center justify-center quick-view">
                            <a href="{{ route('product', $product->id) }}" class="bg-white rounded-full p-3 transform transition-all duration-300 hover:scale-110 hover:bg-blue-50 shadow-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors duration-300">{{ $product->name }}</h3>
                            <div class="flex">
                                @php
                                    // Calculate average rating from product reviews
                                    $rating = 0;
                                    if ($product->reviews->count() > 0) {
                                        $rating = $product->reviews->avg('rating');
                                    } else {
                                        $rating = 0; // Default rating if no reviews
                                    }
                                    $reviewCount = $product->reviews->count();
                                    $reviewCount = $product->reviews_count ?? 0;
                                @endphp
                                <div class="flex items-center mt-2">
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= floor($rating))
                                                <i class="fas fa-star"></i>
                                            @elseif ($i - 0.5 <= $rating)
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-3 truncate overflow-hidden whitespace-normal line-clamp-2 h-10">{{ $product->description ?? 'Tas premium dengan desain stylish dan bahan berkualitas tinggi.' }}</p>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-blue-600 font-bold text-lg">{{ 'Rp ' . number_format($product->price, 0, ',', '.') }}</span>
                            <div class="flex space-x-2">
                                @auth
                                @php
                                    $inWishlist = App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->exists();
                                @endphp
                                <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                           class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full {{ $inWishlist ? 'text-red-500' : 'text-gray-600 hover:text-red-500' }} transition-colors duration-300">
                                        <svg class="w-5 h-5" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>
                                </form>
                                
                                <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="p-2 bg-blue-600 hover:bg-blue-700 rounded-full text-white transition-colors duration-300 add-to-cart-button">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('login') }}" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full text-gray-600 hover:text-red-500 transition-colors duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Stats Counter -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-indigo-700 relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full">
                <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-white rounded-full mix-blend-overlay filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
                <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-white rounded-full mix-blend-overlay filter blur-3xl opacity-10 animate-blob animation-delay-4000"></div>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-white text-center">
                @foreach([
                    ['number' => '15,000+', 'label' => 'Pelanggan Puas', 'icon' => 'users'],
                    ['number' => '200+', 'label' => 'Model Tas', 'icon' => 'collection'],
                    ['number' => '25+', 'label' => 'Tahun Pengalaman', 'icon' => 'clock'],
                    ['number' => '5+', 'label' => 'Penghargaan', 'icon' => 'star']
                ] as $index => $stat)
                <div class="flex flex-col items-center" data-aos="zoom-in" data-aos-delay="{{ $index * 100 }}">
                    <div class="bg-white bg-opacity-20 rounded-full p-4 mb-4 backdrop-blur-sm animate-pulse-ring">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ 
                                $stat['icon'] === 'users' ? 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' : 
                                ($stat['icon'] === 'collection' ? 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' : 
                                ($stat['icon'] === 'clock' ? 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' : 
                                'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z')) }}">
                            </path>
                        </svg>
                    </div>
                    <div class="text-3xl md:text-4xl font-bold mb-2" data-counter="{{ str_replace('+', '', $stat['number']) }}">{{ $stat['number'] }}</div>
                    <p class="text-blue-100">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Bagian Review/Testimonial Customer -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Apa Kata Customer Kami</h2>
                <p class="mt-4 text-lg text-gray-600">Testimoni dari pelanggan yang puas dengan produk kami</p>
            </div>

            @if($bestReviews->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($bestReviews as $review)
                        <div class="bg-white rounded-lg shadow-md p-6 transition-transform hover:-translate-y-1">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0">
                                    @if($review->product->image)
                                        <img src="{{ asset('storage/' . $review->product->image) }}" alt="{{ $review->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded-lg">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate" title="{{ $review->product->name }}">
                                        {{ \Illuminate\Support\Str::limit($review->product->name, 25) }}
                                    </h3>
                                    <a href="{{ route('product', $review->product->id) }}" class="text-sm text-blue-600 hover:underline">Lihat Produk</a>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex items-center mb-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                
                                <blockquote class="italic text-gray-700 mt-2">
                                    "{{ \Illuminate\Support\Str::limit($review->review, 150) }}"
                                </blockquote>
                            </div>

                            <div class="flex justify-between items-center mt-4 text-sm">
                                <div class="font-semibold text-gray-900">{{ $review->user->name }}</div>
                                <div class="text-gray-500">{{ $review->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- <div class="text-center mt-12">
                    <a href="{{ route('reviews.all') }}" class="inline-block bg-white border border-gray-300 text-gray-700 rounded-md px-6 py-3 font-medium hover:bg-gray-50 transition">
                        Lihat Semua Ulasan
                    </a>
                </div> --}}
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">Belum ada ulasan produk</p>
                </div>
            @endif
        </div>
    </section>


    <!-- CTA Section with Wave Background -->
    <section class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-800 py-24">
        <!-- Wave Background -->
        <div class="absolute top-0 left-0 right-0 h-20 overflow-hidden">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" class="absolute bottom-0 w-full h-20 transform rotate-180">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="fill-current text-gray-50"></path>
            </svg>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center" data-aos="fade-up">
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white mb-6">
                    Siap Memperbarui Koleksi Tas Anda?
                </h2>
                <p class="mt-4 max-w-2xl text-xl text-blue-100 mx-auto">
                    Dapatkan diskon 15% untuk pembelian pertama dengan mendaftar newsletter kami
                </p>
                <div class="mt-12 flex justify-center">
                    <div class="w-full max-w-md">
                        <form class="sm:flex space-y-3 sm:space-y-0 sm:space-x-3">
                            <div class="flex-grow">
                                <label for="email-address" class="sr-only">Alamat Email</label>
                                <input id="email-address" name="email" type="email" autocomplete="email" required 
                                       class="w-full px-5 py-3 border border-transparent placeholder-gray-500 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md shadow-lg transform transition-all duration-300 focus:scale-105"
                                       placeholder="Masukkan email Anda">
                            </div>
                            <button type="submit" 
                                    class="w-full sm:w-auto px-6 py-3 bg-white text-blue-700 font-medium rounded-md hover:bg-blue-50 focus:outline-none shadow-lg transform transition-all duration-300 hover:scale-105">
                                Daftar Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Wave -->
        <div class="absolute bottom-0 left-0 right-0 overflow-hidden">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" class="absolute bottom-0 w-full h-20">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="fill-current text-white"></path>
            </svg>
        </div>
    </section>

    <!-- Floating Action Button -->
    <div class="fixed bottom-8 right-8 z-50">
        <a href="https://wa.me/62895337436089" 
           class="float-button bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-xl hover:bg-green-600 transition-all duration-300 transform hover:scale-110">
            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
        </a>
    </div>
@endsection

@section('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS
        AOS.init({
            duration: 1000, // Animation duration
            once: true, // Whether animation should happen only once
            offset: 200, // Offset (in px) from the original trigger point
        });

        // Initialize Swiper for testimonials
        const swiper = new Swiper('#testimonial-swiper', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '#testimonial-next',
                prevEl: '#testimonial-prev',
            },
            pagination: {
                el: '#testimonial-pagination',
                clickable: true,
            },
        });
        
        // Handle wishlist toggle with AJAX
        document.querySelectorAll('.wishlist-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const inWishlist = this.dataset.status === 'true';
                
                // Send AJAX request
                fetch(`/wishlist/toggle/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Toggle button appearance
                        this.classList.toggle('text-red-500');
                        this.classList.toggle('text-gray-600');
                        
                        const svg = this.querySelector('svg');
                        if (inWishlist) {
                            svg.setAttribute('fill', 'none');
                            this.dataset.status = 'false';
                        } else {
                            svg.setAttribute('fill', 'currentColor');
                            this.dataset.status = 'true';
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw {status: response.status, data: errorData};
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Produk berhasil ditambahkan ke keranjang',
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                })
                .catch(error => {
                    let errorMessage = 'Gagal menambahkan produk ke keranjang';
                    
                    if (error.status === 422) {
                        errorMessage = 'Stok produk tidak tersedia';
                        if (error.data && error.data.message) {
                            errorMessage = error.data.message;
                        }
                    }
                    
                    Swal.fire({
                        title: 'Oops!',
                        text: errorMessage,
                        icon: 'error',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                });
            });
        });
    });
</script>
@endsection

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'GPX Store' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    @livewireStyles
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .nav-link {
            position: relative;
            padding: 0.5rem 1rem;
            color: #4b5563;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover {
            color: #3b82f6;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #3b82f6;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 80%;
            left: 10%;
        }
        
        .nav-link.active {
            color: #3b82f6;
            font-weight: 500;
        }
        
        .nav-link.active::after {
            width: 80%;
            left: 10%;
        }
        
        .cart-icon {
            position: relative;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            font-size: 10px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .dropdown {
            position: relative;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 1000;
            min-width: 200px;
            padding: 0.5rem 0;
            margin: 0.5rem 0 0;
            background-color: #fff;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: opacity 0.3s, transform 0.3s, visibility 0.3s;
        }

        /* Show dropdown on both hover and when .show class is present */
        .dropdown:hover .dropdown-menu,
        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        /* Add arrow to dropdown */
        .dropdown-menu::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 16px; /* Adjust based on your layout */
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid white;
        }
        
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            height: 100vh;
            width: 80%;
            max-width: 300px;
            background-color: #fff;
            z-index: 50;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
            transition: right 0.3s ease;
            padding: 1.5rem;
        }
        
        .mobile-menu.active {
            right: 0;
        }
        
        .overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }
        
        .overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .footer-link {
            transition: color 0.3s, transform 0.3s;
        }
        
        .footer-link:hover {
            color: #3b82f6;
            transform: translateX(5px);
        }
        
        .scroll-top-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background-color: #3b82f6;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.5);
            z-index: 30;
        }
        
        .scroll-top-btn.active {
            opacity: 1;
            visibility: visible;
        }
        
        .scroll-top-btn:hover {
            transform: translateY(-5px);
        }
        
        /* Mobile accordion animation */
        .mobile-accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
    </style>
    
    @yield('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Top Bar -->
    <div class="bg-blue-600 text-white py-2 text-sm">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center px-4">
            <div>
                <a href="tel:+62895337436089" class="mr-4 hover:text-blue-200">
                    <i class="fas fa-phone-alt mr-2"></i>0895-33743-6089
                </a>
                <a href="mailto:info@gpx-store.com" class="hover:text-blue-200">
                    <i class="fas fa-envelope mr-2"></i>info@gpx-store.com
                </a>
            </div>
            <div class="mt-2 md:mt-0">
                <a href="#" class="ml-3 hover:text-blue-200">
                    <i class="fas fa-map-marker-alt mr-1"></i>Cek Lokasi Toko
                </a>
                <a href="#" class="ml-3 hover:text-blue-200">
                    <i class="fas fa-shipping-fast mr-1"></i>Cek Pengiriman
                </a>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="bg-white shadow sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="GPX Store" class="h-8 mr-2">
                </a>
                
                <!-- Search Form - Desktop -->
                <div class="hidden md:flex-1 md:flex md:max-w-lg md:mx-8">
                    <form action="{{ route('shop') }}" method="GET" class="w-full">
                        <div class="relative">
                            <input type="text" name="search" placeholder="Cari produk..." 
                                   class="w-full py-2 pl-4 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" class="absolute right-0 top-0 bottom-0 flex items-center px-3 text-gray-500 hover:text-blue-600">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('shop') }}" class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}">
                        Shop
                    </a>
                    <!-- Brands Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="nav-link flex items-center brand-dropdown-toggle">
                            Brand <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <div class="dropdown-menu" id="brand-dropdown-menu">
                            @foreach(App\Models\Brand::all() as $brand)
                                <a href="{{ route('shop', ['brand' => $brand->id]) }}" class="block px-4 py-2 hover:bg-gray-100">
                                    {{ $brand->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <!-- Categories Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="nav-link flex items-center category-dropdown-toggle">
                            Kategori <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <div class="dropdown-menu" id="category-dropdown-menu">
                            @foreach(App\Models\Category::all() as $category)
                                <a href="{{ route('shop', ['category' => $category->id]) }}" class="block px-4 py-2 hover:bg-gray-100">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <a href="{{ route('about') }}" class="nav-link">Tentang Kami</a>
                    <a href="{{ route('contact.index') }}" class="nav-link">CS</a>


                </div>
                
                <!-- User Actions -->
                <div class="flex items-center">
                    <!-- Di bagian navbar, tambahkan link ke wishlist -->
                    <a href="{{ route('wishlist') }}" class="text-gray-600 hover:text-gray-900 relative">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        @auth
                            @php
                                $wishlistCount = App\Models\Wishlist::where('user_id', Auth::id())->count();
                            @endphp
                            @if($wishlistCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full text-xs w-4 h-4 flex items-center justify-center">
                                    {{ $wishlistCount }}
                                </span>
                            @endif
                        @endauth
                    </a>
                    
                    <!-- Cart -->
                    <a href="{{ route('cart') }}" class="relative inline-flex items-center p-2">
                        <svg class="w-6 h-6 text-gray-600 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full cart-count">
                            {{ App\Models\CartItem::where(function($query) {
                                if (Auth::check()) {
                                    $query->where('user_id', Auth::id());
                                } else {
                                    $query->where('session_id', Session::getId());
                                }
                            })->sum('quantity') }}
                        </span>
                    </a>
                    
                    <!-- User Account -->
                    <div class="dropdown ml-1" id="user-dropdown">
                        <button class="p-2 text-gray-600 hover:text-blue-600" id="user-dropdown-toggle">
                            <i class="far fa-user-circle text-lg"></i>
                        </button>
                        <div class="dropdown-menu" id="user-dropdown-menu">
                            @auth
                                {{-- <a href="{{ route('account.dashboard') }}" class="block px-4 py-2 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a> --}}
                                <a href="{{ route('profile.index') }}" class="block px-4 py-2 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <button type="button" class="w-full text-left px-4 py-2 hover:bg-gray-100" id="logout-button">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="block px-4 py-2 hover:bg-gray-100">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                                </a>
                                <a href="{{ route('register') }}" class="block px-4 py-2 hover:bg-gray-100">
                                    <i class="fas fa-user-plus mr-2"></i>Register
                                </a>
                            @endauth
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        document.getElementById('logout-button').addEventListener('click', function () {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "You will be logged out!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, logout!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('logout-form').submit();
                                }
                            });
                        });
                    </script>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="ml-2 p-2 text-gray-600 hover:text-blue-600 md:hidden">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Search (shows below navbar) -->
            <div class="md:hidden py-3">
                <form action="{{ route('shop') }}" method="GET">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Cari produk..." 
                               class="w-full py-2 pl-4 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" class="absolute right-0 top-0 bottom-0 flex items-center px-3 text-gray-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu">
        <div class="flex justify-between items-center mb-6">
            <span class="text-lg font-bold text-blue-600">Menu</span>
            <button id="close-menu-button" class="text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="block py-2 text-gray-800 hover:text-blue-600 {{ request()->routeIs('home') ? 'text-blue-600 font-medium' : '' }}">
                <i class="fas fa-home mr-2"></i>Beranda
            </a>
            <a href="{{ route('shop') }}" class="block py-2 text-gray-800 hover:text-blue-600 {{ request()->routeIs('shop') ? 'text-blue-600 font-medium' : '' }}">
                <i class="fas fa-shopping-bag mr-2"></i>Shop
            </a>
            
            <!-- Mobile Accordion Menu -->
            <div class="border-b border-gray-200 pb-4">
                <button class="flex items-center justify-between w-full py-2 text-gray-800 hover:text-blue-600 focus:outline-none" id="category-accordion-toggle">
                    <span><i class="fas fa-th-large mr-2"></i>Kategori</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>
                <div class="mobile-accordion-content pl-8 space-y-2">
                    @foreach(App\Models\Category::all() as $category)
                        <a href="{{ route('shop', ['category' => $category->id]) }}" class="block py-1 text-gray-600 hover:text-blue-600">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            
            <a href="{{ route('about') }}" class="block py-2 text-gray-800 hover:text-blue-600">
                <i class="fas fa-info-circle mr-2"></i>Tentang Kami
            </a>
            <a href="{{ route('contact.index') }}" class="block py-2 text-gray-800 hover:text-blue-600">
                <i class="fas fa-phone mr-2"></i>CS
            </a>
            <div class="border-t border-gray-200 pt-4 mt-4">
                @auth
                    <a href="{{ route('profile.index') }}" class="block py-2 text-gray-800 hover:text-blue-600">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" id="mobile-logout-form">
                        @csrf
                        <button type="button" class="w-full text-left flex items-center py-2 text-gray-800 hover:text-blue-600" id="mobile-logout-button">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block py-2 text-gray-800 hover:text-blue-600">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}" class="block py-2 text-gray-800 hover:text-blue-600">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                @endauth
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const mobileLogoutButton = document.getElementById('mobile-logout-button');
                    const mobileLogoutForm = document.getElementById('mobile-logout-form');

                    if (mobileLogoutButton && mobileLogoutForm) {
                        mobileLogoutButton.addEventListener('click', function () {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "You will be logged out!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, logout!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    mobileLogoutForm.submit();
                                }
                            });
                        });
                    }
                });
            </script>
        </div>
    </div>
    
    <div id="overlay" class="overlay"></div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-12 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-xl font-bold mb-4">GPX Store</h3>
                    <p class="text-gray-300 mb-4">Toko tas premium dengan kualitas terbaik dan harga terjangkau. Menyediakan berbagai jenis tas untuk kebutuhan sehari-hari.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-white hover:text-blue-400 transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white hover:text-blue-400 transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white hover:text-blue-400 transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white hover:text-blue-400 transition-colors"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <!-- Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Tautan Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white footer-link inline-block">Beranda</a></li>
                        <li><a href="{{ route('shop') }}" class="text-gray-300 hover:text-white footer-link inline-block">Shop</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-white footer-link inline-block">Tentang Kami</a></li>
                        <li><a href="{{ route('contact.index') }}" class="text-gray-300 hover:text-white footer-link inline-block">Customor Service</a></li>
                        <li>
                            <a href="{{ route('message.check-status') }}" class="text-gray-300 hover:text-white transition">
                                Cek Status Pesan
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Categories -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kategori</h3>
                    <ul class="space-y-2">
                        @foreach(App\Models\Category::take(5)->get() as $category)
                            <li><a href="{{ route('shop', ['category' => $category->id]) }}" class="text-gray-300 hover:text-white footer-link inline-block">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-3 text-gray-300">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-blue-400"></i>
                            <span>Jl. Raya Utama No.123, Jakarta Selatan, Indonesia</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-blue-400"></i>
                            <span>0895-33743-6089</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-blue-400"></i>
                            <span>info@gpx-store.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-blue-400"></i>
                            <span>Senin - Sabtu: 09:00 - 20:00</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="border-gray-700 my-8">
            
            <div class="flex flex-col md:flex-row justify-between items-center py-4">
                <div class="mb-4 md:mb-0">
                    <p>&copy; {{ date('Y') }} GPX Store. All rights reserved.</p>
                </div>
                {{-- <div class="flex space-x-4">
                    <img src="{{ asset('images/payment/visa.png') }}" alt="Visa" class="h-8">
                    <img src="{{ asset('images/payment/mastercard.png') }}" alt="Mastercard" class="h-8">
                    <img src="{{ asset('images/payment/paypal.png') }}" alt="PayPal" class="h-8">
                </div> --}}
            </div>
        </div>
    </footer>
    
    <!-- Scroll to top button -->
    <button id="scroll-top" class="scroll-top-btn">
        <i class="fas fa-chevron-up"></i>
    </button>

    @livewireScripts
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('overlay');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeMenuButton = document.getElementById('close-menu-button');
            
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
            
            function closeMenu() {
                mobileMenu.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            closeMenuButton.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);
            
            // User account dropdown toggle - click functionality
            const userDropdownToggle = document.getElementById('user-dropdown-toggle');
            const userDropdownMenu = document.getElementById('user-dropdown-menu');
            
            if (userDropdownToggle && userDropdownMenu) {
                userDropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    userDropdownMenu.classList.toggle('show');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userDropdownToggle.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.classList.remove('show');
                    }
                });
            }
            
            // Category dropdown toggle
            const categoryDropdownToggle = document.querySelector('.category-dropdown-toggle');
            const categoryDropdownMenu = document.getElementById('category-dropdown-menu');
            
            if (categoryDropdownToggle && categoryDropdownMenu) {
                categoryDropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    categoryDropdownMenu.classList.toggle('show');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!categoryDropdownToggle.contains(e.target) && !categoryDropdownMenu.contains(e.target)) {
                        categoryDropdownMenu.classList.remove('show');
                    }
                });
            }
            
            // Mobile category accordion
            const categoryAccordionToggle = document.getElementById('category-accordion-toggle');
            const mobileAccordionContent = document.querySelector('.mobile-accordion-content');
            const accordionIcon = categoryAccordionToggle.querySelector('.fas');
            
            if (categoryAccordionToggle && mobileAccordionContent) {
                categoryAccordionToggle.addEventListener('click', function() {
                    if (mobileAccordionContent.style.maxHeight) {
                        mobileAccordionContent.style.maxHeight = null;
                        accordionIcon.classList.remove('fa-chevron-up', 'transform', 'rotate-180');
                        accordionIcon.classList.add('fa-chevron-down');
                    } else {
                        mobileAccordionContent.style.maxHeight = mobileAccordionContent.scrollHeight + 'px';
                        accordionIcon.classList.remove('fa-chevron-down');
                        accordionIcon.classList.add('fa-chevron-up', 'transform', 'rotate-180');
                    }
                });
            }
            
            // Scroll to top button
            const scrollButton = document.getElementById('scroll-top');
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    scrollButton.classList.add('active');
                } else {
                    scrollButton.classList.remove('active');
                }
            });
            
            scrollButton.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>
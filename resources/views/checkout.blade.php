{{-- {{ dd($addresses) }} --}}
@extends('layouts.app')

@section('styles')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    .form-control {
        @apply w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500;
    }
    
    .form-label {
        @apply block text-sm font-medium text-gray-700 mb-1;
    }
    
    .section-title {
        @apply text-lg font-semibold text-gray-900 mb-4;
    }
    
    .address-card {
        @apply border rounded-lg p-4 cursor-pointer transition-all duration-200;
    }
    
    .address-card.selected {
        @apply border-blue-500 bg-blue-50;
    }
    
    .address-card:hover:not(.selected) {
        @apply border-gray-400 bg-gray-50;
    }
    
    .address-badge {
        @apply inline-block px-2 py-1 text-xs rounded-full font-medium;
    }
    
    .address-badge-primary {
        @apply bg-blue-100 text-blue-800;
    }
    
    .address-badge-secondary {
        @apply bg-gray-100 text-gray-800;
    }
    
    .shipping-method {
        @apply flex items-center p-3 border rounded-md cursor-pointer transition-all duration-200;
    }
    
    .shipping-method:hover {
        @apply bg-gray-50;
    }
    
    .shipping-method.selected {
        @apply border-blue-500 bg-blue-50;
    }
    
    .btn-primary {
        @apply bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition duration-200;
    }
    
    .btn-outline {
        @apply border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200;
    }
    
    .payment-card {
        height: 32px;
        margin-right: 8px;
    }
    
    .animated-bg {
        @apply absolute inset-0 bg-gradient-to-b from-blue-50 to-white -z-10;
    }
    
    /* Custom animation for address selection */
    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5); }
        70% { box-shadow: 0 0 0 5px rgba(59, 130, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    
    .pulse-animation {
        animation: pulse-border 2s infinite;
    }
    
    /* Loading spinner */
    .spinner {
        @apply inline-block w-4 h-4 border-2 border-t-2 border-white rounded-full animate-spin;
        border-top-color: transparent;
    }
    
    /* Alert styles */
    .alert {
        @apply p-3 rounded-md text-sm mb-4;
    }
    
    .alert-success {
        @apply bg-green-100 text-green-700 border border-green-200;
    }
    
    .alert-danger {
        @apply bg-red-100 text-red-700 border border-red-200;
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb with nice background -->
<div class="bg-gray-100 py-4">
    <div class="container mx-auto px-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-blue-600">Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('cart') }}" class="ml-1 text-gray-500 md:ml-2 hover:text-blue-600">Cart</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-800 font-medium md:ml-2">Checkout</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Checkout Steps Progress Bar -->
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between max-w-3xl mx-auto">
        <div class="flex flex-col items-center">
            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold">1</div>
            <span class="mt-2 text-sm font-medium text-blue-600">Shipping</span>
        </div>
        <div class="flex-auto h-1 mx-2 bg-blue-200"></div>
        <div class="flex flex-col items-center">
            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold">2</div>
            <span class="mt-2 text-sm font-medium text-gray-600">Payment</span>
        </div>
        <div class="flex-auto h-1 mx-2 bg-gray-200"></div>
        <div class="flex flex-col items-center">
            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold">3</div>
            <span class="mt-2 text-sm font-medium text-gray-600">Confirmation</span>
        </div>
    </div>
</div>

<!-- Checkout Content -->
<div class="container mx-auto px-4 py-6 mb-16 relative">
    <!-- Background effect -->
    <div class="animated-bg"></div>

    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST" class="relative">
        @csrf
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Customer Information and Shipping Details -->
            <div class="w-full lg:w-2/3">
                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="section-title flex items-center">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        Customer Information
                    </h2>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 mt-2 rounded-r-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">Informasi ini akan digunakan untuk keperluan pengiriman dan konfirmasi pesanan.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. 08123456789" required>
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alt_phone" class="form-label">Alternative Phone (Optional)</label>
                            <input type="text" id="alt_phone" name="alt_phone" value="{{ old('alt_phone') }}" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. 08123456789">
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Addresses -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="section-title flex items-center">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </span>
                        Shipping Address
                    </h2>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Alamat pengiriman yang dipilih:</p>
                    </div>

                    <!-- Address Selection -->
                    <div class="space-y-3 mb-6">
                        <!-- Saved Addresses -->
                        @if(isset($addresses) && count($addresses) > 0)
                            <div class="border rounded-lg p-4 bg-blue-50 border-blue-200">
                                @foreach($addresses as $address)
                                    @if($address->is_default)
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="text-sm font-semibold text-gray-900">{{ $address->name }}</h4>
                                                <p class="text-sm text-gray-600 mt-1">{{ $address->phone }}</p>
                                            </div>
                                            <div>
                                                <span class="address-badge address-badge-primary">Default</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 text-sm text-gray-700">
                                            <p>{{ $address->address_line1 }}</p>
                                            @if($address->address_line2)
                                                <p>{{ $address->address_line2 }}</p>
                                            @endif
                                            <p>{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                                            <p>{{ $address->country }}</p>
                                        </div>
                                        
                                        <input type="hidden" name="address_id" value="{{ $address->id }}">
                                    @endif
                                @endforeach

                                <div class="mt-4 text-center">
                                    <a href="{{ route('profile.addresses') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Ubah Alamat
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="p-6 text-center border rounded-lg bg-yellow-50 border-yellow-200">
                                <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Anda belum memiliki alamat tersimpan.</h3>
                                <p class="mt-1 text-gray-500 mb-4">Silahkan tambahkan alamat terlebih dahulu untuk melanjutkan.</p>
                                <a href="{{ route('addresses.create') }}" class="btn-primary inline-block">
                                    Tambahkan Alamat Baru
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Shipping Method -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="section-title flex items-center">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </span>
                        Shipping Method
                    </h2>
                    
                    <div class="space-y-3 mt-4">
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Pilih Ekspedisi</h3>
                            
                            <!-- Courier Selection -->
                            <label for="courier" class="block text-sm font-medium text-gray-700 mb-2"></label>
                            <div class="relative">
                                <select id="courier" name="courier" class="form-control appearance-none pr-10 pl-4 py-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full transition-colors duration-200">
                                    <option value="" disabled selected>Pilih Jasa Pengiriman</option>
                                    <option value="jne" class="py-2">JNE</option>
                                    <option value="tiki" class="py-2">TIKI</option>
                                    <option value="pos" class="py-2">POS Indonesia</option>
                                    <option value="anteraja" class="py-2">AnterAja</option>
                                    <option value="sicepat" class="py-2">SiCepat</option>
                                    <option value="jnt" class="py-2">J&T Express</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <div class="flex courier-logos mt-3 mb-4 justify-between">
                                <img src="{{ asset('images/couriers/jne.png') }}" alt="JNE" class="h-8 object-contain opacity-60 hover:opacity-100 transition-opacity cursor-pointer" onclick="selectCourierByLogo('jne')">
                                <img src="{{ asset('images/couriers/tiki.png') }}" alt="TIKI" class="h-8 object-contain opacity-60 hover:opacity-100 transition-opacity cursor-pointer" onclick="selectCourierByLogo('tiki')">
                                <img src="{{ asset('images/couriers/pos.png') }}" alt="POS" class="h-8 object-contain opacity-60 hover:opacity-100 transition-opacity cursor-pointer" onclick="selectCourierByLogo('pos')">
                                <img src="{{ asset('images/couriers/anteraja.png') }}" alt="AnterAja" class="h-8 object-contain opacity-60 hover:opacity-100 transition-opacity cursor-pointer" onclick="selectCourierByLogo('anteraja')">
                                <img src="{{ asset('images/couriers/sicepat.png') }}" alt="SiCepat" class="h-8 object-contain opacity-60 hover:opacity-100 transition-opacity cursor-pointer" onclick="selectCourierByLogo('sicepat')">
                                <img src="{{ asset('images/couriers/jnt.png') }}" alt="J&T" class="h-8 object-contain opacity-60 hover:opacity-100 transition-opacity cursor-pointer" onclick="selectCourierByLogo('jnt')">
                            </div>
                            
                            <button type="button" id="check-shipping" class="w-full flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Cek Opsi Pengiriman
                            </button>
                        </div>
                        
                        <!-- Shipping results will be displayed here -->
                        <div id="shipping-options" class="space-y-3">
                            <div class="p-4 text-sm text-gray-500 border border-gray-200 rounded-md bg-gray-50">
                                Silakan pilih kurir untuk melihat opsi pengiriman yang tersedia.
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md text-sm text-yellow-700" id="shipping-notice" style="display:none;">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </div>
                                <div class="ml-3">
                                    <p>Jika Anda ingin menggunakan alamat pengiriman yang berbeda, silakan ubah alamat pengiriman pada halaman profil Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Notes -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="section-title flex items-center">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </span>
                        Order Notes
                    </h2>
                    
                    <div class="mt-6 flex">
                        <label for="notes" class="form-label w-[40%]">Order Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="3" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Special instructions for delivery or order">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden sticky top-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>
                    </div>
                    
                    <div class="px-6 py-4">
                        <!-- Order Items -->
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Items in Your Order ({{ isset($cartItems) ? count($cartItems) : 0 }})</h3>
                            
                            <div class="max-h-60 overflow-y-auto pr-2" style="scrollbar-width: thin;">
                                @if(isset($cartItems))
                                    @foreach($cartItems as $item)
                                        <div class="flex items-center py-3 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                            <div class="flex-shrink-0 w-16 h-16 border border-gray-200 rounded overflow-hidden">
                                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 line-clamp-1">{{ $item->product->name }}</h4>
                                                <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                            </div>
                                            <div class="ml-4 text-right">
                                                @if($item->product->discount_price && $item->product->discount_price < $item->product->price)
                                                    <p class="text-sm font-medium text-blue-600">Rp {{ number_format($item->product->discount_price * $item->quantity, 0, ',', '.') }}</p>
                                                    <p class="text-xs text-gray-400 line-through">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</p>
                                                @else
                                                    <p class="text-sm font-medium text-blue-600">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="py-3 text-center text-gray-500">
                                        No items in cart
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Order Totals -->
                        <div class="border-t border-gray-200 pt-4 pb-2 space-y-2">
                            <div class="flex justify-between py-1">
                                <span class="text-sm text-gray-600">Subtotal</span>
                                <span class="text-sm font-medium" id="subtotal-amount">Rp {{ isset($subtotal) ? number_format($subtotal, 0, ',', '.') : '0' }}</span>
                            </div>
                            
                            <div class="flex justify-between py-1">
                                <span class="text-sm text-gray-600">Tax (11%)</span>
                                <span class="text-sm font-medium" id="tax-amount">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                            </div>
                            
                            <!-- Shipping row will be added here dynamically -->

                            <div id="discount-row" class="flex justify-between py-1 {{ $discount > 0 ? 'block' : 'hidden' }}">
                                <span class="text-sm text-gray-600">Discount</span>
                                <span class="text-sm font-medium text-red-600" id="discount-amount">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <!-- Only one Total row -->
                        <div class="flex justify-between py-3 border-t border-gray-200 mt-2">
                            <span class="text-base font-semibold text-gray-900">Total</span>
                            <span class="text-lg font-bold text-blue-600" id="order-total">Rp {{ number_format($subtotal + $tax - $discount, 0, ',', '.') }}</span>
                        </div>
                        
                        <!-- Coupon Code -->
                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <label for="coupon_code" class="form-label">Coupon Code</label>
                            <div class="flex">
                                <input type="text" id="coupon_code" name="coupon_code" 
                                    placeholder="Enter coupon code" 
                                    class="flex-1 form-control rounded-r-none {{ isset($appliedPromo) ? 'bg-green-50 border-green-500' : '' }} w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ $appliedPromo['code'] ?? '' }}"
                                    {{ isset($appliedPromo) ? 'readonly' : '' }}>
                                
                                @if(isset($appliedPromo))
                                    <button type="button" id="remove-coupon" 
                                        class="btn-outline rounded-l-none text-red-600 hover:bg-red-50 border-l-0 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Remove
                                    </button>
                                @else
                                    <button type="button" id="apply-coupon" 
                                        class="btn-outline rounded-l-none text-blue-600 hover:bg-blue-50 border-l-0">
                                        Apply
                                    </button>
                                @endif
                            </div>
                            
                            <div id="promo-message" class="mt-2 text-sm hidden"></div>
                        </div>
                        
                        <!-- Payment Information -->
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Payment Information</h3>
                            <p class="text-xs text-gray-500 mb-4">After clicking "Place Order", you will be redirected to our secure payment gateway to complete your payment.</p>
                        </div>
                        
                        <button type="submit" id="place-order-button" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition mt-4">
                            <span>Place Order</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                        <p class="text-xs text-gray-500 mt-4">
                            By placing your order, you agree to our <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Make selectCourierByLogo globally accessible
function selectCourierByLogo(courierCode) {
    const courierSelect = document.getElementById('courier');
    if (courierSelect) {
        courierSelect.value = courierCode;
        
        // Trigger change event to ensure proper behavior
        const changeEvent = new Event('change', { bubbles: true });
        courierSelect.dispatchEvent(changeEvent);
        
        // Highlight the selected logo
        const logos = document.querySelectorAll('.courier-logos img');
        logos.forEach(logo => {
            if (logo.alt.toLowerCase().includes(courierCode.toLowerCase())) {
                logo.classList.remove('opacity-60');
                logo.classList.add('opacity-100');
            } else {
                logo.classList.add('opacity-60');
                logo.classList.remove('opacity-100');
            }
        });
    }
}

// Make shipping method functions globally accessible
function selectShippingMethod(element, method) {
    const shippingMethods = document.querySelectorAll('.shipping-method');
    shippingMethods.forEach(method => {
        method.classList.remove('selected');
    });
    
    element.classList.add('selected');
    
    const radio = element.querySelector('input[type="radio"]');
    if (radio) {
        radio.checked = true;
        
        // Get shipping cost and courier from data attribute
        const shippingCost = parseInt(radio.getAttribute('data-cost') || 0);
        const courier = radio.getAttribute('data-courier');
        
        // Update shipping cost in order summary
        updateOrderSummary(shippingCost);
        
        // Add hidden input fields for the form submission
        let shippingInput = document.querySelector('input[name="selected_shipping_cost"]');
        let courierInput = document.querySelector('input[name="selected_courier"]');
        let serviceInput = document.querySelector('input[name="selected_service"]');
        
        if (!shippingInput) {
            shippingInput = document.createElement('input');
            shippingInput.type = 'hidden';
            shippingInput.name = 'selected_shipping_cost';
            document.getElementById('checkout-form').appendChild(shippingInput);
        }
        
        if (!courierInput) {
            courierInput = document.createElement('input');
            courierInput.type = 'hidden';
            courierInput.name = 'selected_courier';
            document.getElementById('checkout-form').appendChild(courierInput);
        }
        
        if (!serviceInput) {
            serviceInput = document.createElement('input');
            serviceInput.type = 'hidden';
            serviceInput.name = 'selected_service';
            document.getElementById('checkout-form').appendChild(serviceInput);
        }
        
        shippingInput.value = shippingCost;
        courierInput.value = courier;
        serviceInput.value = method;
        
        // Log the shipping cost change for debugging
        console.log('Shipping cost updated:', {
            cost: shippingCost,
            courier: courier,
            service: method
        });
    }
}

// Helper function to update order summary (making globally accessible)
function updateOrderSummary(shippingCost) {
    let shippingRow = document.getElementById('shipping-row');
    const orderTotals = document.querySelector('.border-t.border-gray-200.pt-4.pb-2.space-y-2');
    
    if (!shippingRow) {
        // Create shipping row if it doesn't exist
        shippingRow = document.createElement('div');
        shippingRow.id = 'shipping-row';
        shippingRow.className = 'flex justify-between py-1';
        shippingRow.innerHTML = `
            <span class="text-sm text-gray-600">Shipping</span>
            <span class="text-sm font-medium" id="shipping-cost">Rp ${formatNumber(shippingCost)}</span>
        `;
        
        // Insert before discount row if it exists, otherwise append to the container
        const discountRow = document.getElementById('discount-row');
        if (discountRow && orderTotals) {
            discountRow.insertAdjacentElement('beforebegin', shippingRow);
        } else if (orderTotals) {
            orderTotals.appendChild(shippingRow);
        }
    } else {
        // Update existing shipping cost
        const shippingCostEl = document.getElementById('shipping-cost');
        if (shippingCostEl) {
            shippingCostEl.textContent = `Rp ${formatNumber(shippingCost)}`;
        }
    }
    
    // Recalculate total with the new shipping cost
    recalculateTotal();
}

// Utilities needed by our global functions
function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

function parseCurrency(currencyStr) {
    if (!currencyStr) return 0;
    return parseInt(currencyStr.replace(/[^\d]/g, '')) || 0;
}

function recalculateTotal() {
    const subtotalElement = document.getElementById('subtotal-amount');
    const taxElement = document.getElementById('tax-amount');
    const shippingElement = document.getElementById('shipping-cost');
    const discountElement = document.getElementById('discount-amount');
    const totalElement = document.getElementById('order-total');
    
    if (!subtotalElement || !totalElement) return;
    
    // Parse all currency values
    let subtotal = parseCurrency(subtotalElement.textContent);
    let tax = taxElement ? parseCurrency(taxElement.textContent) : 0;
    let shipping = 0; // Default to 0
    let discount = 0;
    
    // Only add shipping if the element exists (meaning shipping was selected)
    if (shippingElement) {
        shipping = parseCurrency(shippingElement.textContent);
        console.log('Current shipping cost:', shipping);
    }
    
    // Only subtract discount if it exists
    if (discountElement && window.getComputedStyle(discountElement.parentElement).display !== 'none') {
        discount = parseCurrency(discountElement.textContent);
    }
    
    // Calculate total
    const total = subtotal + tax + shipping - discount;
    console.log('Calculated total:', { subtotal, tax, shipping, discount, total });
    
    // Update the display
    totalElement.textContent = `Rp ${formatNumber(total)}`;
    
    // Also update the hidden input for the backend
    let totalInput = document.querySelector('input[name="final_total"]');
    if (!totalInput) {
        totalInput = document.createElement('input');
        totalInput.type = 'hidden';
        totalInput.name = 'final_total';
        document.getElementById('checkout-form').appendChild(totalInput);
    }
    totalInput.value = total;
    
    // Add or update discount information if there's a discount
    if (discount > 0) {
        // Find or create the discount info element
        let discountInfo = document.getElementById('discount-info');
        if (!discountInfo) {
            discountInfo = document.createElement('div');
            discountInfo.id = 'discount-info';
            discountInfo.className = 'mt-2 text-sm text-green-600';
            
            // Insert after the discount row
            const discountRow = document.getElementById('discount-row');
            if (discountRow) {
                discountRow.insertAdjacentElement('afterend', discountInfo);
            }
        }
        
        // Calculate discount percentage based on subtotal
        const discountPercentage = subtotal > 0 ? Math.round((discount / subtotal) * 100) : 0;
        // Modified to show only the percentage
        discountInfo.textContent = `Potongan ${discountPercentage}%`;
    } else {
        // Remove discount info if exists and no discount
        const discountInfo = document.getElementById('discount-info');
        if (discountInfo) {
            discountInfo.remove();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Checkout page loaded');
    
    // Initialize AOS
    try {
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    } catch(e) {
        console.error('AOS initialization error:', e);
    }

    // Get address data for RajaOngkir from selected address
    function getSelectedAddressData() {
        @if(isset($addresses) && count($addresses) > 0)
            @foreach($addresses as $address)
                @if($address->is_default)
                    // Check if we have the required city_id
                    @if(!empty($address->city_id))
                        return {
                            id: "{{ $address->id }}",
                            city: "{{ $address->city }}",
                            cityId: "{{ $address->city_id }}",
                            province: "{{ $address->province }}",
                            provinceId: "{{ $address->province_id }}",
                            postalCode: "{{ $address->postal_code }}"
                        };
                    @else
                        // Missing city_id - show a better error message
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Alamat Perlu Diperbarui',
                                text: 'Alamat pengiriman Anda perlu diperbarui untuk menggunakan fitur pengiriman. Silahkan perbarui alamat Anda.',
                                confirmButtonText: 'Perbarui Alamat',
                                showCancelButton: true,
                                cancelButtonText: 'Nanti'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('addresses.edit', $address->id) }}";
                                }
                            });
                        }, 500);
                        
                        return {
                            id: "{{ $address->id }}",
                            city: "{{ $address->city }}",
                            // Use a default city ID for Jakarta
                            cityId: "152",
                            province: "{{ $address->province }}",
                            provinceId: "6",
                            postalCode: "{{ $address->postal_code }}"
                        };
                    @endif
                @endif
            @endforeach
        @endif
        return null;
    }

    // Initialize address data
    const selectedAddress = getSelectedAddressData();
    if (selectedAddress) {
        console.log('Using address data for RajaOngkir:', selectedAddress);
        
        // Show shipping notice if we have address data
        document.getElementById('shipping-notice').style.display = 'block';
        
        // If we have city_id and province_id in the address data, we can use it directly
        // This is useful for RajaOngkir API calls
        if (selectedAddress.cityId && selectedAddress.provinceId) {
            // You could set up a hidden field with this data for the form submission
            const addressDataField = document.createElement('input');
            addressDataField.type = 'hidden';
            addressDataField.name = 'shipping_address_data';
            addressDataField.value = JSON.stringify({
                city_id: selectedAddress.cityId,
                province_id: selectedAddress.provinceId,
                address_id: selectedAddress.id
            });
            document.getElementById('checkout-form').appendChild(addressDataField);
        }
    }
    
    // Calculate initial total without shipping on page load
    // We need to make sure the total only includes subtotal + tax - discount initially
    recalculateTotal();
    
    // RajaOngkir API configuration
    const rajaOngkirConfig = {
        // Change to false when using production environment
        isSandbox: true,
        apiUrl: '/api/rajaongkir',
        originCity: '152', // Default origin city (Jakarta Pusat)
    };

    // Check shipping options (expeditions)
    const checkShippingBtn = document.getElementById('check-shipping');
    const courierSelect = document.getElementById('courier');
    const shippingOptionsContainer = document.getElementById('shipping-options');
    
    if (checkShippingBtn && courierSelect && shippingOptionsContainer) {
        checkShippingBtn.addEventListener('click', function() {
            const courier = courierSelect.value;
            
            if (!courier) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Kurir',
                    text: 'Silakan pilih kurir terlebih dahulu',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            if (!selectedAddress || !selectedAddress.cityId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Alamat Tidak Lengkap',
                    text: 'Alamat pengiriman tidak memiliki ID kota. Silakan perbarui alamat Anda.',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Show loading
            shippingOptionsContainer.innerHTML = `
                <div class="p-4 text-center">
                    <div class="inline-block animate-spin rounded-full h-6 w-6 border-t-2 border-blue-600 border-r-2 border-blue-600 border-b-2 border-blue-600 border-l-2 border-gray-100"></div>
                    <p class="mt-2 text-sm text-gray-600">Memuat opsi pengiriman...</p>
                </div>
            `;
            
            // Calculate cart weight - assume 500g per item for this example
            const cartItems = document.querySelectorAll('.max-h-60.overflow-y-auto > div');
            const totalWeight = Math.max(cartItems.length * 500, 1000); // 500g per item, minimum 1kg
            
            // Debug information
            console.log('Shipping request:', {
                origin: rajaOngkirConfig.originCity,
                destination: selectedAddress.cityId,
                weight: totalWeight,
                courier: courier
            });
            
            // Fetch shipping costs
            fetch('{{ route("checkout.shipping-cost") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    origin: rajaOngkirConfig.originCity,
                    destination: selectedAddress.cityId,
                    weight: totalWeight,
                    courier: courier
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Shipping response:', data);
                
                if (data.success && data.results && data.results.length > 0) {
                    let html = '<div class="space-y-3">';
                    
                    // Process courier services from the API structure
                    data.results.forEach(result => {
                        if (result.costs && result.costs.length > 0) {
                            result.costs.forEach((option, index) => {
                                const isChecked = index === 0 ? 'checked' : '';
                                const isSelected = index === 0 ? 'selected' : '';
                                
                                const serviceName = option.service;
                                const cost = parseInt(option.cost[0].value);
                                const etd = option.cost[0].etd || '1-3';
                                
                                html += `
                                    <div class="shipping-method ${isSelected} flex" onclick="selectShippingMethod(this, '${serviceName}')">
                                        <input type="radio" name="shipping_method" value="${serviceName}" ${isChecked} 
                                            data-cost="${cost}" data-courier="${courier.toUpperCase()}"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 mt-2">
                                        <div class="ml-3 flex-grow">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium text-gray-900">${courier.toUpperCase()} - ${serviceName}</span>
                                                <span class="font-medium text-blue-600">Rp ${formatNumber(cost)}</span>
                                            </div>
                                            <p class="text-sm text-gray-500">Estimasi pengiriman: ${etd.replace('HARI', '').trim()} hari</p>
                                        </div>
                                    </div>
                                `;
                            });
                        }
                    });
                    
                    html += '</div>';
                    shippingOptionsContainer.innerHTML = html;
                    
                    // Update shipping cost display with the first option
                    if (data.results[0].costs && data.results[0].costs.length > 0) {
                        const firstOption = data.results[0].costs[0];
                        const shippingCost = parseInt(firstOption.cost[0].value);
                        updateOrderSummary(shippingCost);
                        
                        // Add hidden input for the selected shipping method
                        let shippingInput = document.querySelector('input[name="selected_shipping_cost"]');
                        if (!shippingInput) {
                            shippingInput = document.createElement('input');
                            shippingInput.type = 'hidden';
                            shippingInput.name = 'selected_shipping_cost';
                            document.getElementById('checkout-form').appendChild(shippingInput);
                        }
                        shippingInput.value = shippingCost;
                    }
                    
                } else {
                    shippingOptionsContainer.innerHTML = `
                        <div class="p-4 text-sm text-red-500 border border-red-200 rounded-md bg-red-50">
                            Tidak ada opsi pengiriman tersedia untuk kurir dan tujuan yang dipilih.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching shipping costs:', error);
                shippingOptionsContainer.innerHTML = `
                    <div class="p-4 text-sm text-red-500 border border-red-200 rounded-md bg-red-50">
                        Gagal memuat opsi pengiriman. Silakan coba lagi.
                    </div>
                `;
            });
        });
    }
    
    // Form submission handler
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const button = document.getElementById('place-order-button');
            
            // Check if shipping method is selected
            const shippingMethodSelected = document.querySelector('input[name="shipping_method"]:checked');
            if (!shippingMethodSelected) {
                e.preventDefault();
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Shipping Method Required',
                    text: 'Silakan pilih metode pengiriman sebelum melanjutkan ke pembayaran',
                    confirmButtonText: 'OK'
                });
                
                // Scroll to shipping section
                const shippingMethodSection = document.querySelector('#shipping-options');
                if (shippingMethodSection) {
                    shippingMethodSection.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                
                return false;
            }
            
            if (button) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner mr-2"></span>Processing...';
                button.classList.add('opacity-75', 'cursor-not-allowed');
            }
            
            // If no address is selected, prevent form submission
            if (!selectedAddress) {
                e.preventDefault();
                button.disabled = false;
                button.innerHTML = '<span>Place Order</span><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>';
                button.classList.remove('opacity-75', 'cursor-not-allowed');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Alamat Pengiriman Diperlukan',
                    text: 'Anda harus menambahkan alamat pengiriman terlebih dahulu',
                    confirmButtonText: 'Tambah Alamat',
                    showCancelButton: true,
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('addresses.create') }}";
                    }
                });
            }
            
            @if(isset($addresses) && count($addresses) == 0)
            e.preventDefault();
            button.disabled = false;
            button.innerHTML = '<span>Place Order</span><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>';
            button.classList.remove('opacity-75', 'cursor-not-allowed');
            
            Swal.fire({
                icon: 'error',
                title: 'Alamat Pengiriman Diperlukan',
                text: 'Anda harus menambahkan alamat pengiriman terlebih dahulu',
                confirmButtonText: 'Tambah Alamat',
                showCancelButton: true,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('addresses.create') }}";
                }
            });
            @endif
        });
    }

    // Make function to select shipping method globally accessible
    function selectShippingMethod(element, method) {
        const shippingMethods = document.querySelectorAll('.shipping-method');
        shippingMethods.forEach(method => {
            method.classList.remove('selected');
        });
        
        element.classList.add('selected');
        
        const radio = element.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            
            // Get shipping cost and courier from data attribute
            const shippingCost = parseInt(radio.getAttribute('data-cost') || 0);
            const courier = radio.getAttribute('data-courier');
            
            // Update shipping cost in order summary
            updateOrderSummary(shippingCost);
            
            // Add hidden input fields for the form submission
            let shippingInput = document.querySelector('input[name="selected_shipping_cost"]');
            let courierInput = document.querySelector('input[name="selected_courier"]');
            let serviceInput = document.querySelector('input[name="selected_service"]');
            
            if (!shippingInput) {
                shippingInput = document.createElement('input');
                shippingInput.type = 'hidden';
                shippingInput.name = 'selected_shipping_cost';
                document.getElementById('checkout-form').appendChild(shippingInput);
            }
            
            if (!courierInput) {
                courierInput = document.createElement('input');
                courierInput.type = 'hidden';
                courierInput.name = 'selected_courier';
                document.getElementById('checkout-form').appendChild(courierInput);
            }
            
            if (!serviceInput) {
                serviceInput = document.createElement('input');
                serviceInput.type = 'hidden';
                serviceInput.name = 'selected_service';
                document.getElementById('checkout-form').appendChild(serviceInput);
            }
            
            shippingInput.value = shippingCost;
            courierInput.value = courier;
            serviceInput.value = method;
        }
    }

    // Make updateOrderSummary function globally accessible too
    function updateOrderSummary(shippingCost) {
        let shippingRow = document.getElementById('shipping-row');
        const orderTotals = document.querySelector('.border-t.border-gray-200.pt-4.pb-2.space-y-2');
        
        if (!shippingRow) {
            // Create shipping row if it doesn't exist
            shippingRow = document.createElement('div');
            shippingRow.id = 'shipping-row';
            shippingRow.className = 'flex justify-between py-1';
            shippingRow.innerHTML = `
                <span class="text-sm text-gray-600">Shipping</span>
                <span class="text-sm font-medium" id="shipping-cost">Rp ${formatNumber(shippingCost)}</span>
            `;
            
            // Insert before discount row if it exists, otherwise append to the container
            const discountRow = document.getElementById('discount-row');
            if (discountRow && orderTotals) {
                discountRow.insertAdjacentElement('beforebegin', shippingRow);
            } else if (orderTotals) {
                orderTotals.appendChild(shippingRow);
            }
        } else {
            // Update existing shipping cost
            const shippingCostEl = document.getElementById('shipping-cost');
            if (shippingCostEl) {
                shippingCostEl.textContent = `Rp ${formatNumber(shippingCost)}`;
            }
        }
        
        // Recalculate total with the new shipping cost
        recalculateTotal();
    }

    // Helper function to update the order summary with shipping cost
    function updateOrderSummary(shippingCost) {
        let shippingRow = document.getElementById('shipping-row');
        const orderTotals = document.querySelector('.border-t.border-gray-200.pt-4.pb-2.space-y-2');
        
        if (!shippingRow) {
            // Create shipping row if it doesn't exist
            shippingRow = document.createElement('div');
            shippingRow.id = 'shipping-row';
            shippingRow.className = 'flex justify-between py-1';
            shippingRow.innerHTML = `
                <span class="text-sm text-gray-600">Shipping</span>
                <span class="text-sm font-medium" id="shipping-cost">Rp ${formatNumber(shippingCost)}</span>
            `;
            
            // Insert before discount row if it exists, otherwise append to the container
            const discountRow = document.getElementById('discount-row');
            if (discountRow) {
                discountRow.insertAdjacentElement('beforebegin', shippingRow);
            } else {
                orderTotals.appendChild(shippingRow);
            }
        } else {
            // Update existing shipping cost
            document.getElementById('shipping-cost').textContent = `Rp ${formatNumber(shippingCost)}`;
        }
        
        // Recalculate total with the new shipping cost
        recalculateTotal();
    }

    // Utility function to properly parse Indonesian currency format
    function parseCurrency(currencyStr) {
        if (!currencyStr) return 0;
        return parseInt(currencyStr.replace(/[^\d]/g, '')) || 0;
    }

    // Global recalculateTotal function
    function recalculateTotal() {
        const subtotalElement = document.getElementById('subtotal-amount');
        const taxElement = document.getElementById('tax-amount');
        const shippingElement = document.getElementById('shipping-cost');
        const discountElement = document.getElementById('discount-amount');
        const totalElement = document.getElementById('order-total');
        
        if (!subtotalElement || !totalElement) return;
        
        // Parse all currency values
        let subtotal = parseCurrency(subtotalElement.textContent);
        let tax = taxElement ? parseCurrency(taxElement.textContent) : 0;
        let shipping = 0; // Default to 0
        let discount = 0;
        
        // Only add shipping if the element exists (meaning shipping was selected)
        if (shippingElement) {
            shipping = parseCurrency(shippingElement.textContent);
            console.log('Current shipping cost:', shipping);
        }
        
        // Only subtract discount if it exists
        if (discountElement && window.getComputedStyle(discountElement.parentElement).display !== 'none') {
            discount = parseCurrency(discountElement.textContent);
        }
        
        // Calculate total
        const total = subtotal + tax + shipping - discount;
        console.log('Calculated total:', { subtotal, tax, shipping, discount, total });
        
        // Update the display
        totalElement.textContent = `Rp ${formatNumber(total)}`;
        
        // Also update the hidden input for the backend
        let totalInput = document.querySelector('input[name="final_total"]');
        if (!totalInput) {
            totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'final_total';
            document.getElementById('checkout-form').appendChild(totalInput);
        }
        totalInput.value = total;
        
        // Add or update discount information if there's a discount
        if (discount > 0) {
            // Find or create the discount info element
            let discountInfo = document.getElementById('discount-info');
            if (!discountInfo) {
                discountInfo = document.createElement('div');
                discountInfo.id = 'discount-info';
                discountInfo.className = 'mt-2 text-sm text-green-600';
                
                // Insert after the discount row
                const discountRow = document.getElementById('discount-row');
                if (discountRow) {
                    discountRow.insertAdjacentElement('afterend', discountInfo);
                }
            }
            
            // Calculate discount percentage based on subtotal
            const discountPercentage = subtotal > 0 ? Math.round((discount / subtotal) * 100) : 0;
            // Modified to show only the percentage
            discountInfo.textContent = `Potongan ${discountPercentage}%`;
        } else {
            // Remove discount info if exists and no discount
            const discountInfo = document.getElementById('discount-info');
            if (discountInfo) {
                discountInfo.remove();
            }
        }
    }

    // Ketika nilai select berubah, update highlight logo
    document.getElementById('courier').addEventListener('change', function() {
        const selectedValue = this.value;
        const logos = document.querySelectorAll('.courier-logos img');
        
        logos.forEach(logo => {
            if (logo.alt.toLowerCase().includes(selectedValue.toLowerCase())) {
                logo.classList.remove('opacity-60');
                logo.classList.add('opacity-100');
            } else {
                logo.classList.add('opacity-60');
                logo.classList.remove('opacity-100');
            }
        });
    });

    // Apply Coupon Code - Fixed version
    const applyCouponButton = document.getElementById('apply-coupon');
    const removeCouponButton = document.getElementById('remove-coupon');
    const couponCodeInput = document.getElementById('coupon_code');
    const promoMessage = document.getElementById('promo-message');
    const discountRow = document.getElementById('discount-row');
    const discountAmount = document.getElementById('discount-amount');
    const orderTotal = document.getElementById('order-total');

    if (applyCouponButton) {
        applyCouponButton.addEventListener('click', function() {
            const code = couponCodeInput.value.trim();
            if (!code) {
                showPromoMessage('Please enter a coupon code', 'danger');
                return;
            }
            
            // Show loading state
            applyCouponButton.disabled = true;
            applyCouponButton.innerHTML = '<span class="spinner mr-2"></span> Applying...';
            
            // Get subtotal from the page
            const subtotalElement = document.querySelector('#subtotal-amount');
            const subtotal = parseCurrency(subtotalElement.textContent);
            
            // Use URLSearchParams for proper form data encoding
            const params = new URLSearchParams();
            params.append('coupon_code', code);
            params.append('subtotal', subtotal);
            
            // Send AJAX request to apply coupon using axios
            axios.post('{{ route("coupon.apply") }}', params)
                .then(function(response) {
                    const data = response.data;
                    if (data.success) {
                        // Reload the page to ensure proper state update
                        window.location.reload();
                    } else {
                        // Error
                        showPromoMessage(data.message || 'Failed to apply coupon code', 'danger');
                        applyCouponButton.disabled = false;
                        applyCouponButton.innerHTML = 'Apply';
                    }
                })
                .catch(function(error) {
                    console.error('Coupon application error:', error);
                    showPromoMessage('An error occurred while applying the coupon', 'danger');
                    applyCouponButton.disabled = false;
                    applyCouponButton.innerHTML = 'Apply';
                });
        });
    }

    // Fix the remove coupon functionality as well
    function setupRemoveCouponEvent() {
        const removeCoupon = document.getElementById('remove-coupon');
        if (removeCoupon) {
            removeCoupon.addEventListener('click', function() {
                // Show loading state
                removeCoupon.disabled = true;
                removeCoupon.innerHTML = '<span class="spinner mr-2"></span> Removing...';
                
                // Send AJAX request to remove coupon using axios
                axios.post('{{ route("coupon.remove") }}')
                    .then(function(response) {
                        const data = response.data;
                        if (data.success) {
                            // Reload the page to ensure proper state update
                            window.location.reload();
                        } else {
                            removeCoupon.disabled = false;
                            removeCoupon.innerHTML = `
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Remove
                            `;
                            showPromoMessage(data.message || 'Failed to remove promo code', 'danger');
                        }
                    })
                    .catch(function(error) {
                        console.error('Coupon removal error:', error);
                        removeCoupon.disabled = false;
                        removeCoupon.innerHTML = `
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Remove
                        `;
                        showPromoMessage('An error occurred while removing the coupon', 'danger');
                    });
            });
        }
    }

    // If remove button exists on page load, set up its event listener
    if (removeCouponButton) {
        setupRemoveCouponEvent();
    }

    // Helper function to show promo message
    function showPromoMessage(message, type) {
        promoMessage.textContent = message;
        promoMessage.className = 'mt-2 text-sm';
        promoMessage.classList.remove('hidden', 'text-green-600', 'text-red-600');
        
        if (type === 'success') {
            promoMessage.classList.add('text-green-600');
        } else if (type === 'danger') {
            promoMessage.classList.add('text-red-600');
        }
        
        promoMessage.classList.remove('hidden');
    }

    // Helper function to recalculate the total
    function recalculateTotal() {
        // Get all the necessary elements
        const subtotalElement = document.getElementById('subtotal-amount');
        const taxElement = document.getElementById('tax-amount');
        const shippingElement = document.getElementById('shipping-cost');
        const discountElement = document.getElementById('discount-amount');
        const totalElement = document.getElementById('order-total');
        
        if (!subtotalElement || !totalElement) return;
        
        // Parse all currency values
        let subtotal = parseCurrency(subtotalElement.textContent);
        let tax = taxElement ? parseCurrency(taxElement.textContent) : 0;
        let shipping = 0; // Default to 0
        let discount = 0;
        
        // Only add shipping if the element exists (meaning shipping was selected)
        if (shippingElement) {
            shipping = parseCurrency(shippingElement.textContent);
        }
        
        // Only subtract discount if it exists
        if (discountElement && window.getComputedStyle(discountElement.parentElement).display !== 'none') {
            discount = parseCurrency(discountElement.textContent);
        }
        
        // Calculate total
        const total = subtotal + tax + shipping - discount;
        
        // Update the display
        totalElement.textContent = `Rp ${formatNumber(total)}`;
        
        // Also update the hidden input for the backend
        let totalInput = document.querySelector('input[name="final_total"]');
        if (!totalInput) {
            totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'final_total';
            document.getElementById('checkout-form').appendChild(totalInput);
        }
        totalInput.value = total;
        
        // Add or update discount information if there's a discount
        if (discount > 0) {
            // Find or create the discount info element
            let discountInfo = document.getElementById('discount-info');
            if (!discountInfo) {
                discountInfo = document.createElement('div');
                discountInfo.id = 'discount-info';
                discountInfo.className = 'mt-2 text-sm text-green-600';
                
                // Insert after the discount row
                const discountRow = document.getElementById('discount-row');
                if (discountRow) {
                    discountRow.insertAdjacentElement('afterend', discountInfo);
                }
            }
            
            // Calculate discount percentage based on subtotal
            const discountPercentage = subtotal > 0 ? Math.round((discount / subtotal) * 100) : 0;
            // Modified to show only the percentage
            discountInfo.textContent = `Potongan ${discountPercentage}%`;
        } else {
            // Remove discount info if exists and no discount
            const discountInfo = document.getElementById('discount-info');
            if (discountInfo) {
                discountInfo.remove();
            }
        }
    }
});
</script>
@endsection
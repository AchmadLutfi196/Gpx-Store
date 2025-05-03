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

<!-- Error and Success Messages -->
@if ($errors->any())
    <div class="container mx-auto px-4 mb-6">
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium">Please fix the following errors:</h3>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

@if (session('success'))
    <div class="container mx-auto px-4 mb-6">
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert" data-aos="fade-down">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="container mx-auto px-4 mb-6">
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert" data-aos="fade-down">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    </div>
@endif

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
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" data-aos="fade-up">
                    <h2 class="section-title flex items-center">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        Customer Information
                    </h2>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded-r-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">Informasi ini akan digunakan untuk keperluan pengiriman dan konfirmasi pesanan.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-control" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control" required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-control" placeholder="e.g. 08123456789" required>
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alt_phone" class="form-label">Alternative Phone (Optional)</label>
                            <input type="text" id="alt_phone" name="alt_phone" value="{{ old('alt_phone') }}" class="form-control" placeholder="e.g. 08123456789">
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Addresses -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" data-aos="fade-up" data-aos-delay="100">
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
                        @if(count($addresses ?? []) > 0)
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
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" data-aos="fade-up" data-aos-delay="200">
                    <h2 class="section-title flex items-center">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </span>
                        Shipping Method
                    </h2>
                    
                    <div class="space-y-3 mt-4">
                        <div class="shipping-method selected" onclick="selectShippingMethod(this, 'regular')">
                            <input type="radio" name="shipping_method" value="regular" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="ml-3 flex-grow">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900">Regular Shipping</span>
                                    <span class="font-medium text-blue-600">Rp 10.000</span>
                                </div>
                                <p class="text-sm text-gray-500">Estimated delivery: 2-4 business days</p>
                            </div>
                        </div>
                        
                        <div class="shipping-method" onclick="selectShippingMethod(this, 'express')">
                            <input type="radio" name="shipping_method" value="express" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="ml-3 flex-grow">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900">Express Shipping</span>
                                    <span class="font-medium text-blue-600">Rp 25.000</span>
                                </div>
                                <p class="text-sm text-gray-500">Estimated delivery: 1-2 business days</p>
                            </div>
                        </div>
                        
                        <div class="shipping-method" onclick="selectShippingMethod(this, 'same_day')">
                            <input type="radio" name="shipping_method" value="same_day" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="ml-3 flex-grow">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900">Same Day Delivery</span>
                                    <span class="font-medium text-blue-600">Rp 50.000</span>
                                </div>
                                <p class="text-sm text-gray-500">Available for selected cities only (Jakarta, Surabaya, Bandung)</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Notes -->
                    <div class="mt-6">
                        <label for="notes" class="form-label">Order Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="3" class="form-control" placeholder="Special instructions for delivery or order">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="w-full lg:w-1/3" data-aos="fade-left">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden sticky top-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>
                    </div>
                    
                    <div class="px-6 py-4">
                        <!-- Order Items -->
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Items in Your Order ({{ count($cartItems) }})</h3>
                            
                            <div class="max-h-60 overflow-y-auto pr-2" style="scrollbar-width: thin;">
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
                            </div>
                        </div>
                        
                        <!-- Order Totals -->
                        <div class="border-t border-gray-200 pt-4 pb-2 space-y-2">
                            <div class="flex justify-between py-1">
                                <span class="text-sm text-gray-600">Subtotal</span>
                                <span class="text-sm font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="flex justify-between py-1" id="shipping-cost-display">
                                <span class="text-sm text-gray-600">Shipping</span>
                                <span class="text-sm font-medium">Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="flex justify-between py-1">
                                <span class="text-sm text-gray-600">Tax (11%)</span>
                                <span class="text-sm font-medium">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                            </div>
                            
                            <div id="discount-row" class="flex justify-between py-1 {{ $discount > 0 ? 'block' : 'hidden' }}">
                                <span class="text-sm text-gray-600">Discount</span>
                                <span class="text-sm font-medium text-red-600" id="discount-amount">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between py-3 border-t border-gray-200 mt-2">
                            <span class="text-base font-semibold text-gray-900">Total</span>
                            <span class="text-lg font-bold text-blue-600" id="order-total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        
                        <!-- Coupon Code -->
                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <label for="coupon_code" class="form-label">Coupon Code</label>
                            <div class="flex">
                                <input type="text" id="coupon_code" name="coupon_code" 
                                    placeholder="Enter coupon code" 
                                    class="flex-1 form-control rounded-r-none {{ isset($appliedPromo) ? 'bg-green-50 border-green-500' : '' }}"
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
                            
                            @if(isset($appliedPromo))
                                <div class="mt-2 text-sm text-green-600 font-medium">
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Promo "{{ $appliedPromo['code'] }}" applied: 
                                        @if($appliedPromo['discount_type'] === 'percentage')
                                            {{ $appliedPromo['discount_value'] }}% off
                                        @else
                                            Rp {{ number_format($appliedPromo['discount_value'], 0, ',', '.') }} off
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Payment Information -->
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Payment Information</h3>
                            <p class="text-xs text-gray-500 mb-4">After clicking "Place Order", you will be redirected to our secure payment gateway to complete your payment. We accept various payment methods.</p>
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
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });
    
    // Form submission handler
    const checkoutForm = document.getElementById('checkout-form');
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const button = document.getElementById('place-order-button');
            if (button) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner mr-2"></span>Processing...';
                button.classList.add('opacity-75', 'cursor-not-allowed');
            }
            
            @if(count($addresses ?? []) == 0)
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'No Shipping Address',
                text: 'Please add a shipping address before proceeding with your order.',
                confirmButtonText: 'Add Address',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('addresses.create') }}";
                } else {
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = '<span>Place Order</span><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>';
                        button.classList.remove('opacity-75', 'cursor-not-allowed');
                    }
                }
            });
            @endif
        });
    }
    
    // Helper functions for currency handling
    function parseCurrency(currencyStr) {
        return parseFloat(currencyStr.replace('Rp ', '').replaceAll('.', '').trim()) || 0;
    }
    
    function formatCurrency(number) {
        return 'Rp ' + number.toLocaleString('id-ID');
    }
    
    function recalculateTotal() {
        const subtotalElement = document.querySelector('.flex.justify-between.py-1:first-child .text-sm.font-medium');
        const shippingElement = document.querySelector('#shipping-cost-display .text-sm.font-medium');
        const taxElement = document.querySelector('.flex.justify-between.py-1:nth-child(3) .text-sm.font-medium');
        const discountElement = document.querySelector('#discount-amount');
        const discountRow = document.getElementById('discount-row');
        const orderTotal = document.getElementById('order-total');
        
        let subtotal = parseCurrency(subtotalElement.textContent);
        let shipping = parseCurrency(shippingElement.textContent);
        let tax = parseCurrency(taxElement.textContent);
        let discount = 0;
        
        if (!discountRow.classList.contains('hidden') && discountElement) {
            discount = parseCurrency(discountElement.textContent.replace('- ', ''));
        }
        
        const total = subtotal + shipping + tax - discount;
        orderTotal.textContent = formatCurrency(total);
    }

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
            
            applyCouponButton.disabled = true;
            applyCouponButton.innerHTML = '<span class="spinner mr-2"></span> Applying...';
            
            const subtotalElement = document.querySelector('.flex.justify-between.py-1:first-child .text-sm.font-medium');
            let subtotal = 0;
            if (subtotalElement) {
                subtotal = parseCurrency(subtotalElement.textContent);
            }
            
            axios.post('{{ route("coupon.apply") }}', {
                coupon_code: code,
                subtotal: subtotal,
                _token: '{{ csrf_token() }}'
            })
            .then(function (response) {
                handleCouponResponse(response);
            })
            .catch(function (error) {
                console.log(error);
                showPromoMessage('An error occurred while applying the coupon', 'danger');
                applyCouponButton.disabled = false;
                applyCouponButton.textContent = 'Apply';
            });
        });
    }
    
    function setupRemoveCouponEvent() {
        const removeCoupon = document.getElementById('remove-coupon');
        if (removeCoupon) {
            removeCoupon.addEventListener('click', function() {
                const currentCode = couponCodeInput.value;
                
                removeCoupon.disabled = true;
                removeCoupon.innerHTML = '<span class="spinner mr-2"></span> Removing...';
                
                axios.post('{{ route("coupon.remove") }}', {
                    _token: '{{ csrf_token() }}'
                })
                .then(function(response) {
                    const data = response.data;
                    if (data.success) {
                        couponCodeInput.readOnly = false;
                        couponCodeInput.classList.remove('bg-green-50', 'border-green-500');
                        
                        setTimeout(() => {
                            couponCodeInput.value = currentCode;
                            couponCodeInput.focus();
                        }, 50);
                        
                        discountRow.classList.add('hidden');
                        
                        removeCoupon.parentNode.innerHTML = `
                            <button type="button" id="apply-coupon" 
                                class="btn-outline rounded-l-none text-blue-600 hover:bg-blue-50 border-l-0">
                                Apply
                            </button>
                        `;
                        
                        const promoInfo = document.querySelector('.text-green-600.font-medium');
                        if (promoInfo) {
                            promoInfo.remove();
                        }
                        
                        promoMessage.textContent = '';
                        promoMessage.classList.add('hidden');
                        
                        recalculateTotal();
                        
                        const newApplyBtn = document.getElementById('apply-coupon');
                        if (newApplyBtn) {
                            newApplyBtn.addEventListener('click', function() {
                                const code = couponCodeInput.value.trim();
                                if (!code) {
                                    showPromoMessage('Please enter a coupon code', 'danger');
                                    return;
                                }
                                
                                this.disabled = true;
                                this.innerHTML = '<span class="spinner mr-2"></span> Applying...';
                                
                                const subtotalElement = document.querySelector('.flex.justify-between.py-1:first-child .text-sm.font-medium');
                                let subtotal = 0;
                                if (subtotalElement) {
                                    subtotal = parseCurrency(subtotalElement.textContent);
                                }
                                
                                axios.post('{{ route("coupon.apply") }}', {
                                    coupon_code: code,
                                    subtotal: subtotal,
                                    _token: '{{ csrf_token() }}'
                                })
                                .then(function (response) {
                                    handleCouponResponse(response);
                                })
                                .catch(function (error) {
                                    console.log(error);
                                    showPromoMessage('An error occurred while applying the coupon', 'danger');
                                    newApplyBtn.disabled = false;
                                    newApplyBtn.textContent = 'Apply';
                                });
                            });
                        }
                    } else {
                        removeCoupon.disabled = false;
                        removeCoupon.innerHTML = `
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Remove
                        `;
                        showPromoMessage('Failed to remove promo code', 'danger');
                    }
                })
                .catch(function(error) {
                    console.log(error);
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
    
    if (removeCouponButton) {
        setupRemoveCouponEvent();
    }
    
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

    function handleCouponResponse(response) {
        const data = response.data;
        if (data.success) {
            showPromoMessage(data.message, 'success');
            
            const appliedCode = data.promo.code;
            
            if (couponCodeInput) {
                couponCodeInput.value = appliedCode;
                couponCodeInput.readOnly = true;
                couponCodeInput.classList.add('bg-green-50', 'border-green-500');
            }
            
            discountRow.classList.remove('hidden');
            discountAmount.textContent = '- ' + data.promo.formatted_discount;
            
            const applyButton = document.getElementById('apply-coupon');
            if (applyButton) {
                applyButton.parentNode.innerHTML = `
                    <button type="button" id="remove-coupon" 
                        class="btn-outline rounded-l-none text-red-600 hover:bg-red-50 border-l-0 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Remove
                    </button>
                `;
            }
            
            const promoInfoHtml = `
                <div class="mt-2 text-sm text-green-600 font-medium">
                    <span class="inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Promo "${appliedCode}" applied: 
                        ${data.promo.discount_type === 'percentage' 
                            ? data.promo.discount_value + '% off' 
                            : data.promo.formatted_discount + ' off'}
                    </span>
                </div>
            `;
            promoMessage.insertAdjacentHTML('afterend', promoInfoHtml);
            
            recalculateTotal();
            
            setTimeout(() => {
                setupRemoveCouponEvent();
            }, 50);
        } else {
            showPromoMessage(data.message, 'danger');
            const applyButton = document.getElementById('apply-coupon');
            if (applyButton) {
                applyButton.disabled = false;
                applyButton.textContent = 'Apply';
            }
        }
    }
});

// Function to select shipping method
function selectShippingMethod(element, method) {
    const shippingMethods = document.querySelectorAll('.shipping-method');
    shippingMethods.forEach(method => {
        method.classList.remove('selected');
    });
    
    element.classList.add('selected');
    
    const radio = element.querySelector('input[type="radio"]');
    if (radio) {
        radio.checked = true;
    }
    
    let shippingCost = 10000;
    
    if (method === 'express') {
        shippingCost = 25000;
    } else if (method === 'same_day') {
        shippingCost = 50000;
    }
    
    const shippingDisplay = document.getElementById('shipping-cost-display');
    if (shippingDisplay) {
        const shippingText = shippingDisplay.querySelector('.text-sm.font-medium');
        if (shippingText) {
            shippingText.textContent = formatCurrency(shippingCost);
        }
    }
    
    recalculateTotal();
}

function formatCurrency(number) {
    return 'Rp ' + number.toLocaleString('id-ID');
}

function recalculateTotal() {
    const subtotalElement = document.querySelector('.flex.justify-between.py-1:first-child .text-sm.font-medium');
    const shippingElement = document.querySelector('#shipping-cost-display .text-sm.font-medium');
    const taxElement = document.querySelector('.flex.justify-between.py-1:nth-child(3) .text-sm.font-medium');
    const discountElement = document.querySelector('#discount-amount');
    const discountRow = document.getElementById('discount-row');
    const orderTotal = document.getElementById('order-total');
    
    let subtotal = parseCurrency(subtotalElement.textContent);
    let shipping = parseCurrency(shippingElement.textContent);
    let tax = parseCurrency(taxElement.textContent);
    let discount = 0;
    
    if (!discountRow.classList.contains('hidden') && discountElement) {
        discount = parseCurrency(discountElement.textContent.replace('- ', ''));
    }
    
    const total = subtotal + shipping + tax - discount;
    orderTotal.textContent = formatCurrency(total);
}

function parseCurrency(currencyStr) {
    return parseFloat(currencyStr.replace('Rp ', '').replaceAll('.', '').trim()) || 0;
}
</script>
@endsection
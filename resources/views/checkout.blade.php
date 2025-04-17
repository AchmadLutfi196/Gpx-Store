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
<div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 py-6">
    <div class="absolute inset-0 opacity-10">
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <defs>
                <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
                    <path d="M 20 0 L 0 0 0 20" fill="none" stroke="white" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
    </div>
    
    <div class="container mx-auto px-4 relative">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-blue-100 hover:text-white">
                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('cart') }}" class="ml-1 text-blue-100 md:ml-2 hover:text-white">Cart</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-white font-medium md:ml-2">Checkout</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <h1 class="text-3xl font-bold text-white mt-4" data-aos="fade-up">Checkout</h1>
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
                        <p class="text-sm text-gray-600">Choose a delivery address from your address book or create a new one.</p>
                    </div>

                    <!-- Address Selection -->
                    <div class="space-y-3 mb-6">
                        <!-- Saved Addresses -->
                        @if(count($addresses ?? []) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($addresses as $address)
                                <div class="address-card {{ $address->is_default ? 'selected pulse-animation' : '' }}" 
                                     data-address-id="{{ $address->id }}" 
                                     onclick="selectAddress(this, {{ $address->id }})">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $address->name }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $address->phone }}</p>
                                        </div>
                                        <div>
                                            @if($address->is_default)
                                                <span class="address-badge address-badge-primary">Default</span>
                                            @else
                                                <span class="address-badge address-badge-secondary">Saved</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 text-sm text-gray-700">
                                        <p>{{ $address->address_line1 }}</p>
                                        <p>{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                                    </div>
                                    
                                    <input type="radio" name="address_id" value="{{ $address->id }}" class="hidden address-radio" {{ $address->is_default ? 'checked' : '' }}>
                                </div>
                                @endforeach
                            </div>

                            <div class="mt-4 text-center">
                                <a href="{{ route('profile.addresses') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Kelola Alamat
                                </a>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm text-gray-600">or</span>
                                <button type="button" id="show-new-address-form" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add New Address
                                </button>
                            </div>
                            
                            <div class="border-t border-gray-200 my-4"></div>
                        @else
                            <div class="p-6 text-center">
                                <p class="text-gray-600 mb-4">Anda belum memiliki alamat tersimpan.</p>
                                <a href="{{ route('addresses.create') }}" class="btn-primary inline-block">
                                    Tambahkan Alamat Baru
                                </a>
                            </div>
                        @endif
                        
                        <!-- New Address Form -->
                        <div id="new-address-form" class="{{ count($addresses ?? []) > 0 ? 'hidden' : 'block' }}">
                            <h3 class="font-medium text-gray-900 mb-3">New Shipping Address</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="recipient_name" class="form-label">Recipient Name</label>
                                    <input type="text" id="recipient_name" name="recipient_name" class="form-control">
                                </div>
                                
                                <div>
                                    <label for="recipient_phone" class="form-label">Recipient Phone</label>
                                    <input type="text" id="recipient_phone" name="recipient_phone" class="form-control">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="address_line1" class="form-label">Street Address</label>
                                <input type="text" id="address_line1" name="address_line1" class="form-control" placeholder="House number and street name">
                                @error('address_line1')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="address_line2" class="form-label">Address Line 2 (Optional)</label>
                                <input type="text" id="address_line2" name="address_line2" class="form-control" placeholder="Apartment, suite, unit, etc.">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" id="city" name="city" class="form-control">
                                    @error('city')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="province" class="form-label">Province</label>
                                    <input type="text" id="province" name="province" class="form-control">
                                    @error('province')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" id="postal_code" name="postal_code" class="form-control">
                                    @error('postal_code')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="save_address" id="save_address" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Save this address to my address book</span>
                                </label>
                            </div>
                            
                            <div class="mt-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="set_as_default" id="set_as_default" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Set as my default address</span>
                                </label>
                            </div>
                            
                            @if(count($addresses ?? []) > 0)
                                <div class="mt-4">
                                    <button type="button" id="cancel-new-address" class="text-sm text-gray-600 hover:text-gray-800">
                                        Cancel
                                    </button>
                                </div>
                            @endif
                        </div>
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
    
    // Address form toggle
    const showNewAddressFormButton = document.getElementById('show-new-address-form');
    const cancelNewAddressButton = document.getElementById('cancel-new-address');
    const newAddressForm = document.getElementById('new-address-form');
    
    if (showNewAddressFormButton) {
        showNewAddressFormButton.addEventListener('click', function() {
            newAddressForm.classList.remove('hidden');
            showNewAddressFormButton.classList.add('hidden');
            
            // Uncheck all address radio buttons
            const addressRadios = document.querySelectorAll('.address-radio');
            addressRadios.forEach(radio => {
                radio.checked = false;
            });
            
            // Remove selected class from all address cards
            const addressCards = document.querySelectorAll('.address-card');
            addressCards.forEach(card => {
                card.classList.remove('selected');
                card.classList.remove('pulse-animation');
            });
        });
    }
    
    if (cancelNewAddressButton) {
        cancelNewAddressButton.addEventListener('click', function() {
            newAddressForm.classList.add('hidden');
            showNewAddressFormButton.classList.remove('hidden');
            
            // Select the default address if available
            const defaultAddressRadio = document.querySelector('.address-card[data-address-id="1"] .address-radio');
            if (defaultAddressRadio) {
                defaultAddressRadio.checked = true;
                document.querySelector('.address-card[data-address-id="1"]').classList.add('selected');
            }
        });
    }

    // Apply Coupon Code
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
            const subtotalElement = document.querySelector('.flex.justify-between.py-1:first-child .text-sm.font-medium');
            let subtotal = 0;
            if (subtotalElement) {
                subtotal = parseFloat(subtotalElement.textContent.replace('Rp ', '').replaceAll('.', ''));
            }
            
            // Send AJAX request to apply coupon
            axios.post('{{ route("coupon.apply") }}', {
                coupon_code: code,
                subtotal: subtotal,
                _token: '{{ csrf_token() }}'
            })
            .then(function (response) {
                const data = response.data;
                if (data.success) {
                    // Success - update UI
                    showPromoMessage(data.message, 'success');
                    
                    // Make coupon field readonly and styled
                    couponCodeInput.value = data.promo.code;
                    couponCodeInput.readOnly = true;
                    couponCodeInput.classList.add('bg-green-50', 'border-green-500');
                    
                    // Update discount row
                    discountRow.classList.remove('hidden');
                    discountAmount.textContent = '- ' + data.promo.formatted_discount;
                    
                                        // Replace apply button with remove button
                                        applyCouponButton.parentNode.innerHTML = `
                        <button type="button" id="remove-coupon" 
                            class="btn-outline rounded-l-none text-red-600 hover:bg-red-50 border-l-0 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Remove
                        </button>
                    `;
                    
                    // Add promo info message
                    const promoInfoHtml = `
                        <div class="mt-2 text-sm text-green-600 font-medium">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Promo "${data.promo.code}" applied: 
                                ${data.promo.discount_type === 'percentage' 
                                    ? data.promo.discount_value + '% off' 
                                    : data.promo.formatted_discount + ' off'}
                            </span>
                        </div>
                    `;
                    promoMessage.insertAdjacentHTML('afterend', promoInfoHtml);
                    
                    // Recalculate total
                    recalculateTotal();
                    
                    // Setup the new remove button event
                    setupRemoveCouponEvent();
                } else {
                    // Error
                    showPromoMessage(data.message, 'danger');
                    applyCouponButton.disabled = false;
                    applyCouponButton.textContent = 'Apply';
                }
            })
            .catch(function (error) {
                console.log(error);
                showPromoMessage('An error occurred while applying the coupon', 'danger');
                applyCouponButton.disabled = false;
                applyCouponButton.textContent = 'Apply';
            });
        });
    }
    
    // Setup event handler for the remove coupon button
    function setupRemoveCouponEvent() {
        const removeCoupon = document.getElementById('remove-coupon');
        if (removeCoupon) {
            removeCoupon.addEventListener('click', function() {
                // Show loading state
                removeCoupon.disabled = true;
                removeCoupon.innerHTML = '<span class="spinner mr-2"></span> Removing...';
                
                // Send AJAX request to remove coupon
                axios.post('{{ route("coupon.remove") }}', {
                    _token: '{{ csrf_token() }}'
                })
                .then(function(response) {
                    const data = response.data;
                    if (data.success) {
                        // Reset coupon field
                        couponCodeInput.value = '';
                        couponCodeInput.readOnly = false;
                        couponCodeInput.classList.remove('bg-green-50', 'border-green-500');
                        
                        // Hide discount row
                        discountRow.classList.add('hidden');
                        
                        // Replace remove button with apply button
                        removeCoupon.parentNode.innerHTML = `
                            <button type="button" id="apply-coupon" 
                                class="btn-outline rounded-l-none text-blue-600 hover:bg-blue-50 border-l-0">
                                Apply
                            </button>
                        `;
                        
                        // Remove promo info message
                        const promoInfo = document.querySelector('.text-green-600.font-medium');
                        if (promoInfo) {
                            promoInfo.remove();
                        }
                        
                        // Clear any message
                        promoMessage.textContent = '';
                        promoMessage.classList.add('hidden');
                        
                        // Recalculate total
                        recalculateTotal();
                        
                        // Setup the new apply button event
                        const newApplyBtn = document.getElementById('apply-coupon');
                        if (newApplyBtn) {
                            newApplyBtn.addEventListener('click', applyCoupon);
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
        const subtotalElement = document.querySelector('.flex.justify-between.py-1:first-child .text-sm.font-medium');
        const shippingElement = document.querySelector('#shipping-cost-display .text-sm.font-medium');
        const taxElement = document.querySelector('.flex.justify-between.py-1:nth-child(3) .text-sm.font-medium');
        const discountElement = document.querySelector('#discount-amount');
        
        // Parse values
        let subtotal = parseFloat(subtotalElement.textContent.replace('Rp ', '').replaceAll('.', ''));
        let shipping = parseFloat(shippingElement.textContent.replace('Rp ', '').replaceAll('.', ''));
        let tax = parseFloat(taxElement.textContent.replace('Rp ', '').replaceAll('.', ''));
        let discount = 0;
        
        if (!discountRow.classList.contains('hidden') && discountElement) {
            discount = parseFloat(discountElement.textContent.replace('- Rp ', '').replaceAll('.', ''));
        }
        
        // Calculate new total
        const total = subtotal + shipping + tax - discount;
        
        // Update the total display
        orderTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

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
            
            // Check if at least one address is selected or new address form is visible
            const selectedAddress = document.querySelector('.address-radio:checked');
            const newAddressVisible = !newAddressForm.classList.contains('hidden');
            
            if (!selectedAddress && !newAddressVisible) {
                e.preventDefault();
                alert('Please select a shipping address or add a new one.');
                // Re-enable the button
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<span>Place Order</span><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>';
                    button.classList.remove('opacity-75', 'cursor-not-allowed');
                }
                return;
            }
            
            // If new address form is visible, validate required fields
            if (newAddressVisible) {
                const requiredFields = ['recipient_name', 'recipient_phone', 'street_address', 'city', 'province', 'postal_code'];
                let hasError = false;
                
                requiredFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        hasError = true;
                        input.classList.add('border-red-500');
                    } else {
                        input.classList.remove('border-red-500');
                    }
                });
                
                if (hasError) {
                    e.preventDefault();
                    alert('Please fill in all required address fields.');
                    // Re-enable the button
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = '<span>Place Order</span><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>';
                        button.classList.remove('opacity-75', 'cursor-not-allowed');
                    }
                    return;
                }
            }
        });
    }
});

// Function to select address
function selectAddress(element, addressId) {
    // Remove selected class from all address cards
    const addressCards = document.querySelectorAll('.address-card');
    addressCards.forEach(card => {
        card.classList.remove('selected');
        card.classList.remove('pulse-animation');
    });
    
    // Add selected class to clicked address card
    element.classList.add('selected');
    
    // Check the radio button
    const radio = element.querySelector('.address-radio');
    if (radio) {
        radio.checked = true;
    }
    
    // Hide the new address form
    const newAddressForm = document.getElementById('new-address-form');
    if (newAddressForm) {
        newAddressForm.classList.add('hidden');
    }
    
    // Show the "Add New Address" button
    const showNewAddressFormButton = document.getElementById('show-new-address-form');
    if (showNewAddressFormButton) {
        showNewAddressFormButton.classList.remove('hidden');
    }
}

// Function to select shipping method
function selectShippingMethod(element, method) {
    // Remove selected class from all shipping methods
    const shippingMethods = document.querySelectorAll('.shipping-method');
    shippingMethods.forEach(method => {
        method.classList.remove('selected');
    });
    
    // Add selected class to clicked shipping method
    element.classList.add('selected');
    
    // Check the radio button
    const radio = element.querySelector('input[type="radio"]');
    if (radio) {
        radio.checked = true;
    }
    
    // Update the shipping cost and total
    let shippingCost = 10000; // Default: Regular shipping
    
    if (method === 'express') {
        shippingCost = 25000;
    } else if (method === 'same_day') {
        shippingCost = 50000;
    }
    
    // Update the displayed shipping cost
    const shippingDisplay = document.getElementById('shipping-cost-display');
    if (shippingDisplay) {
        const shippingText = shippingDisplay.querySelector('.text-sm.font-medium');
        if (shippingText) {
            shippingText.textContent = 'Rp ' + shippingCost.toLocaleString('id-ID');
        }
    }
    
    // Recalculate total
    const subtotalElement = document.querySelector('.flex.justify-between.py-1:first-child .text-sm.font-medium');
    const taxElement = document.querySelector('.flex.justify-between.py-1:nth-child(3) .text-sm.font-medium');
    const discountElement = document.querySelector('#discount-amount');
    const discountRow = document.getElementById('discount-row');
    
    let subtotal = 0;
    let tax = 0;
    let discount = 0;
    
    if (subtotalElement) {
        subtotal = parseFloat(subtotalElement.textContent.replace('Rp ', '').replaceAll('.', ''));
    }
    
    if (taxElement) {
        tax = parseFloat(taxElement.textContent.replace('Rp ', '').replaceAll('.', ''));
    }
    
    if (!discountRow.classList.contains('hidden') && discountElement) {
        discount = parseFloat(discountElement.textContent.replace('- Rp ', '').replaceAll('.', ''));
    }
    
    const total = subtotal + shippingCost + tax - discount;
    
    // Update the total
    const totalElement = document.getElementById('order-total');
    if (totalElement) {
        totalElement.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
}
</script>
@endsection
@extends('layouts.app')

@section('styles')
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
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
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
                        <span class="ml-1 text-gray-500 md:ml-2">Checkout</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Checkout Content -->
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Checkout</h1>
    
    <!-- Tambahkan debugging untuk melihat jika ada error validasi -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong class="font-bold">Error!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tambahkan debugging untuk melihat jika ada pesan dari session -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Customer Information and Shipping Details -->
            <div class="w-full lg:w-2/3">
                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="section-title">Customer Information</h2>
                    
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
                
                <!-- Shipping Details -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="section-title">Shipping Details</h2>
                    
                    <div class="mb-4">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" class="form-control" placeholder="Street address" required>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="city" class="form-label">City</label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}" class="form-control" required>
                            @error('city')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="state" class="form-label">Province</label>
                            <input type="text" id="state" name="state" value="{{ old('state') }}" class="form-control" required>
                            @error('state')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="zipcode" class="form-label">Postal Code</label>
                            <input type="text" id="zipcode" name="zipcode" value="{{ old('zipcode') }}" class="form-control" required>
                            @error('zipcode')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="notes" class="form-label">Order Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="3" class="form-control" placeholder="Notes about your order, e.g. special delivery instructions">{{ old('notes') }}</textarea>
                    </div>
                </div>
                
                <!-- Shipping Method -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="section-title">Shipping Method</h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="shipping_method" value="regular" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Regular Shipping</span>
                                <span class="block text-sm text-gray-500">2-4 Business days</span>
                            </div>
                            <span class="ml-auto text-sm font-medium text-gray-900">Rp 10.000</span>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="shipping_method" value="express" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Express Shipping</span>
                                <span class="block text-sm text-gray-500">1-2 Business days</span>
                            </div>
                            <span class="ml-auto text-sm font-medium text-gray-900">Rp 25.000</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden sticky top-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
                    </div>
                    
                    <div class="px-6 py-4">
                        <!-- Order Items -->
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Items in Your Order</h3>
                            
                            <div class="max-h-60 overflow-y-auto">
                                @foreach($cartItems as $item)
                                    <div class="flex items-center py-3 border-b border-gray-200">
                                        <div class="flex-shrink-0 w-16 h-16 border border-gray-200 rounded overflow-hidden">
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $item->product->name }}</h4>
                                            <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                        </div>
                                        <div class="ml-4 text-right">
                                            @if($item->product->discount_price && $item->product->discount_price < $item->product->price)
                                                <p class="text-sm font-medium text-blue-600">Rp {{ number_format($item->product->discount_price * $item->quantity, 0, ',', '.') }}</p>
                                            @else
                                                <p class="text-sm font-medium text-blue-600">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Order Totals -->
                        <div class="border-t border-gray-200 pt-4 pb-2">
                            <div class="flex justify-between py-1">
                                <span class="text-sm text-gray-600">Subtotal</span>
                                <span class="text-sm font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="flex justify-between py-1">
                                <span class="text-sm text-gray-600">Shipping</span>
                                <span class="text-sm font-medium">Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="flex justify-between py-1">
                                <span class="text-sm text-gray-600">Tax (11%)</span>
                                <span class="text-sm font-medium">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($discount > 0)
                                <div class="flex justify-between py-1">
                                    <span class="text-sm text-gray-600">Discount</span>
                                    <span class="text-sm font-medium text-red-600">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex justify-between py-3 border-t border-gray-200 mt-2">
                            <span class="text-base font-medium text-gray-900">Total</span>
                            <span class="text-base font-bold text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        
                        <!-- Coupon Code -->
                        <div class="mt-4">
                            <div class="flex">
                                <input type="text" id="coupon_code" name="coupon_code" placeholder="Enter coupon code" class="flex-1 border border-gray-300 rounded-l-md py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-r-md transition duration-200">Apply</button>
                            </div>
                        </div>
                        
                        <!-- Payment Information -->
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Payment Information</h3>
                            <p class="text-xs text-gray-500 mb-4">After clicking "Place Order", you will be redirected to Midtrans to complete your payment. We accept various payment methods including credit cards, bank transfers, e-wallets, and more.</p>
                            
                            <div class="flex space-x-2 items-center mb-4">
                                <img src="https://via.placeholder.com/40x25?text=Visa" alt="Visa" class="h-6">
                                <img src="https://via.placeholder.com/40x25?text=MC" alt="Mastercard" class="h-6">
                                <img src="https://via.placeholder.com/40x25?text=BCA" alt="BCA" class="h-6">
                                <img src="https://via.placeholder.com/40x25?text=BNI" alt="BNI" class="h-6">
                                <img src="https://via.placeholder.com/40x25?text=BRI" alt="BRI" class="h-6">
                                <img src="https://via.placeholder.com/40x25?text=Mandiri" alt="Mandiri" class="h-6">
                                <img src="https://via.placeholder.com/40x25?text=OVO" alt="OVO" class="h-6">
                            </div>
                        </div>
                        
                        <button type="submit" id="place-order-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-md transition duration-200">
                            Place Order
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug - log when DOM is ready
    console.log('DOM loaded');
    
    // Debug - check if form exists
    const checkoutForm = document.getElementById('checkout-form');
    console.log('Checkout form found:', !!checkoutForm);
    
    if (checkoutForm) {
        // Debug - add listener for form submit
        checkoutForm.addEventListener('submit', function(e) {
            console.log('Form submission intercepted');
            
            // Add loading state to button
            const button = document.getElementById('place-order-button');
            if (button) {
                button.disabled = true;
                button.innerText = 'Processing...';
                button.classList.add('bg-gray-500');
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            }
            
            // Continue with form submission
            console.log('Form submission proceeding');
        });
        
        // Additional debug - add click handler to button
        const placeOrderButton = document.getElementById('place-order-button');
        if (placeOrderButton) {
            console.log('Place order button found');
            placeOrderButton.addEventListener('click', function() {
                console.log('Place order button clicked');
            });
        }
    }
});
</script>
@endsection
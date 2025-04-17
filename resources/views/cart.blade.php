@extends('layouts.app')

@section('styles')
<style>
    .cart-item {
        transition: all 0.3s ease;
    }
    
    .cart-item:hover {
        background-color: #f9fafb;
    }
    
    .quantity-input {
        width: 60px;
        text-align: center;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }
    
    .price {
        color: #3b82f6;
        font-weight: 600;
    }
    
    .remove-btn {
        transition: all 0.2s ease;
    }
    
    .remove-btn:hover {
        color: #ef4444;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                        <span class="ml-1 text-gray-500 md:ml-2">Cart</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Cart Content -->
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Shopping Cart</h1>
    
    <!-- Empty Cart State -->
    @if(!isset($cartItems) || count($cartItems) === 0)
        <div class="text-center py-16 bg-white rounded-lg shadow-sm">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Your cart is empty</h3>
            <p class="mt-2 text-sm text-gray-500">Looks like you haven't added any products to your cart yet.</p>
            <div class="mt-6">
                <a href="{{ route('shop') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    Continue Shopping
                </a>
            </div>
        </div>
    @else
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Cart Items -->
            <div class="w-full md:w-2/3">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Items in your cart ({{ $cartItems->sum('quantity') }})</h2>
                    </div>
                    
                    <ul class="divide-y divide-gray-200" id="cart-items-list">
                        @foreach($cartItems as $item)
                            <li class="cart-item p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4" data-id="{{ $item->id }}">
                                <!-- Product Image -->
                                <div class="flex-shrink-0 w-24 h-24">
                                    <img 
                                        src="{{ asset('storage/' . $item->product->image) }}" 
                                        alt="{{ $item->product->name }}" 
                                        class="w-full h-full object-cover rounded-md"
                                    >
                                </div>
                                
                                <!-- Product Details -->
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row justify-between">
                                        <!-- Product Info -->
                                        <div class="mb-2 sm:mb-0">
                                            <h3 class="text-base font-medium text-gray-900">
                                                <a href="{{ route('product', ['id' => $item->product->id]) }}" class="hover:text-blue-600">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h3>
                                            @if(isset($item->product->brand) && $item->product->brand)
                                                <p class="mt-1 text-sm text-gray-500">{{ $item->product->brand->name }}</p>
                                            @endif
                                        </div>
                                        
                                        <!-- Price -->
                                        <div class="text-right">
                                            @if($item->product->discount_price && $item->product->discount_price < $item->product->price)
                                                <p class="price" data-price="{{ $item->product->discount_price }}">
                                                    Rp {{ number_format($item->product->discount_price, 0, ',', '.') }}
                                                </p>
                                                <p class="text-sm text-gray-500 line-through">
                                                    Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                                </p>
                                            @else
                                                <p class="price" data-price="{{ $item->product->price }}">
                                                    Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity and Subtotal -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-4 gap-3">
                                        <!-- Quantity Controls -->
                                        <div class="inline-flex items-center border border-gray-300 rounded-md">
                                            <button type="button" class="px-3 py-1 text-gray-600 hover:bg-gray-100 btn-decrease">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <input 
                                                type="number" 
                                                value="{{ $item->quantity }}" 
                                                class="quantity-input" 
                                                min="1" 
                                                max="{{ $item->product->stock ?? 99 }}"
                                            >
                                            <button type="button" class="px-3 py-1 text-gray-600 hover:bg-gray-100 btn-increase">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Subtotal and Remove Button -->
                                        <div class="flex items-center">
                                            <span class="font-medium mr-3 item-subtotal">
                                                Rp {{ number_format(($item->product->discount_price ?? $item->product->price) * $item->quantity, 0, ',', '.') }}
                                            </span>
                                            <button type="button" class="remove-btn text-gray-500 hover:text-red-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                        <a href="{{ route('shop') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Continue Shopping
                        </a>
                        
                        <button type="button" class="text-gray-600 hover:text-red-600 flex items-center" id="clear-cart">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="w-full md:w-1/3">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
                    </div>
                    
                    <div class="px-6 py-4">
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium" id="cart-subtotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium" id="cart-shipping">Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-medium" id="cart-tax">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($discount > 0)
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Discount</span>
                                <span class="font-medium text-red-600" id="cart-discount">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between py-4 border-t border-gray-200 mt-2">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-lg font-bold" id="cart-total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('checkout') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md font-medium transition-colors duration-200 text-center">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Quantity controls
        const cartItems = document.querySelectorAll('.cart-item');
        
        cartItems.forEach(function(item) {
            const decreaseBtn = item.querySelector('.btn-decrease');
            const increaseBtn = item.querySelector('.btn-increase');
            const quantityInput = item.querySelector('.quantity-input');
            const itemId = item.getAttribute('data-id');
            const priceElement = item.querySelector('.price');
            const subtotalElement = item.querySelector('.item-subtotal');
            
            if (decreaseBtn && increaseBtn && quantityInput) {
                decreaseBtn.addEventListener('click', function() {
                    const currentValue = parseInt(quantityInput.value);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                        updateCartItem(itemId, currentValue - 1, item);
                    }
                });
                
                increaseBtn.addEventListener('click', function() {
                    const currentValue = parseInt(quantityInput.value);
                    const maxStock = parseInt(quantityInput.getAttribute('max'));
                    if (!maxStock || currentValue < maxStock) {
                        quantityInput.value = currentValue + 1;
                        updateCartItem(itemId, currentValue + 1, item);
                    } else {
                        Swal.fire({
                            title: 'Stock Limit',
                            text: `Sorry, only ${maxStock} items available in stock.`,
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                        });
                    }
                });
                
                quantityInput.addEventListener('change', function() {
                    const newValue = parseInt(this.value);
                    const maxStock = parseInt(this.getAttribute('max'));
                    
                    if (newValue < 1) {
                        this.value = 1;
                        updateCartItem(itemId, 1, item);
                    } else if (maxStock && newValue > maxStock) {
                        this.value = maxStock;
                        updateCartItem(itemId, maxStock, item);
                        Swal.fire({
                            title: 'Stock Limit',
                            text: `Sorry, only ${maxStock} items available in stock.`,
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                        });
                    } else {
                        updateCartItem(itemId, newValue, item);
                    }
                });
            }
        });
        
        // Remove buttons
        const removeButtons = document.querySelectorAll('.remove-btn');
        
        removeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const cartItem = this.closest('.cart-item');
                const itemId = cartItem.getAttribute('data-id');
                
                Swal.fire({
                    title: 'Remove item?',
                    text: "Are you sure you want to remove this item from your cart?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        removeCartItem(itemId, cartItem);
                    }
                });
            });
        });
        
        // Clear cart
        const clearCartButton = document.getElementById('clear-cart');
        
        if (clearCartButton) {
            clearCartButton.addEventListener('click', function() {
                Swal.fire({
                    title: 'Clear cart?',
                    text: "Are you sure you want to remove all items from your cart?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Yes, clear it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        clearCart();
                    }
                });
            });
        }
        
        // Promo code form
        const promoForm = document.getElementById('promo-form');
        
        if (promoForm) {
            promoForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const promoCode = document.getElementById('promo_code').value;
                
                if (!promoCode) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Please enter a promo code.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                    });
                    return;
                }
                
                // Here you would typically make an AJAX request to validate the promo code
                // For demo purposes, let's just show a success message
                Swal.fire({
                    title: 'Promo Applied!',
                    text: 'Your promo code has been applied successfully.',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                });
                
                // Update the UI to reflect the discount
                document.getElementById('cart-discount').textContent = '- Rp 50.000';
                updateCartTotals();
            });
        }
        
        // Shipping method radios
        const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');
        
        if (shippingRadios.length > 0) {
            shippingRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Update shipping cost based on selection
                    const shippingCost = this.value === 'express' ? 25000 : 10000;
                    document.getElementById('cart-shipping').textContent = `Rp ${numberFormat(shippingCost)}`;
                    updateCartTotals();
                });
            });
        }
        
        // Function to update cart item (AJAX)
        function updateCartItem(itemId, quantity, cartItem) {
            const priceElement = cartItem.querySelector('.price');
            const subtotalElement = cartItem.querySelector('.item-subtotal');
            const price = parseFloat(priceElement.getAttribute('data-price'));
            
            // Update the subtotal immediately for better UX
            const newSubtotal = price * quantity;
            subtotalElement.textContent = `Rp ${numberFormat(newSubtotal)}`;
            
            // Send AJAX request to update the item
            fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    cart_item_id: itemId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the cart total
                    updateCartTotals();
                    
                    // Update cart count in navbar if it exists
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement && data.cart_count) {
                        cartCountElement.textContent = data.cart_count;
                    }
                } else {
                    // Show error
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Failed to update cart.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                });
            });
        }
        
        // Function to remove cart item (AJAX)
        function removeCartItem(itemId, cartItem) {
            // Animation before removal
            cartItem.style.opacity = '0';
            cartItem.style.transform = 'translateX(30px)';
            
            // Send AJAX request to remove the item
            fetch('{{ route('cart.remove') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    cart_item_id: itemId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the item from the DOM
                    setTimeout(function() {
                        cartItem.remove();
                        
                        // Update the cart total
                        updateCartTotals();
                        
                        // Update cart count in navbar if it exists
                        const cartCountElement = document.querySelector('.cart-count');
                        if (cartCountElement && data.cart_count !== undefined) {
                            cartCountElement.textContent = data.cart_count;
                        }
                        
                        // Check if cart is empty after removal
                        const remainingItems = document.querySelectorAll('.cart-item');
                        if (remainingItems.length === 0) {
                            location.reload(); // Refresh to show empty cart state
                        }
                        
                        // Show success toast
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        Toast.fire({
                            icon: 'success',
                            title: 'Item removed from cart'
                        });
                    }, 300);
                } else {
                    // Reset the animation and show error
                    cartItem.style.opacity = '1';
                    cartItem.style.transform = 'translateX(0)';
                    
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Failed to remove item from cart.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Reset the animation
                cartItem.style.opacity = '1';
                cartItem.style.transform = 'translateX(0)';
                
                Swal.fire({
                    title: 'Error',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                });
            });
        }
        
        // Function to clear the entire cart (AJAX)
        function clearCart() {
            fetch('{{ route('cart.clear') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Cart Cleared!',
                        text: 'Your shopping cart has been cleared.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                    }).then(() => {
                        location.reload(); // Refresh to show empty cart state
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Failed to clear cart.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                });
            });
        }
        
        // Function to recalculate cart totals
        function updateCartTotals() {
            // Calculate new subtotal from items in DOM
            let subtotal = 0;
            const cartItems = document.querySelectorAll('.cart-item');
            
            cartItems.forEach(item => {
                const quantity = parseInt(item.querySelector('.quantity-input').value);
                const price = parseFloat(item.querySelector('.price').getAttribute('data-price'));
                subtotal += price * quantity;
            });
            
            // Get shipping cost
            const shippingText = document.getElementById('cart-shipping').textContent;
            const shipping = parseFloat(shippingText.replace(/[^0-9]/g, ''));
            
            // Calculate tax (11%)
            const tax = subtotal * 0.11;
            
            // Get discount if any
            let discount = 0;
            const discountElement = document.getElementById('cart-discount');
            if (discountElement) {
                const discountText = discountElement.textContent;
                discount = parseFloat(discountText.replace(/[^0-9]/g, ''));
            }
            
            // Calculate total
            const total = subtotal + shipping + tax - discount;
            
            // Update UI
            document.getElementById('cart-subtotal').textContent = `Rp ${numberFormat(subtotal)}`;
            document.getElementById('cart-tax').textContent = `Rp ${numberFormat(tax)}`;
            document.getElementById('cart-total').textContent = `Rp ${numberFormat(total)}`;
        }
        
        // Helper function to format numbers with thousand separators
        function numberFormat(number) {
            return new Intl.NumberFormat('id-ID').format(Math.round(number));
        }
    });
</script>
@endsection
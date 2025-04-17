@extends('layouts.app')

@section('styles')
<style>
    .product-image {
        transition: transform 0.3s ease;
    }
    
    .thumbnail {
        cursor: pointer;
        transition: all 0.2s ease;
        opacity: 0.7;
    }
    
    .thumbnail:hover {
        opacity: 1;
    }
    
    .thumbnail.active {
        border-color: #3b82f6;
        opacity: 1;
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
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .discount-badge {
        background-color: #ef4444;
        color: white;
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.25rem;
    }
</style>
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Add CSRF token meta tag for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">
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
                        <a href="{{ route('shop') }}" class="ml-1 text-gray-500 md:ml-2 hover:text-blue-600">Shop</a>
                    </div>
                </li>
                @if($product->category)
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('shop', ['category' => $product->category_id]) }}" class="ml-1 text-gray-500 md:ml-2 hover:text-blue-600">{{ $product->category->name }}</a>
                    </div>
                </li>
                @endif
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">{{ $product->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Detail -->
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Product Images -->
        <div class="w-full lg:w-1/2">
            <div class="relative mb-4">
                <img id="main-image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-auto rounded-lg product-image">
                @if($product->discount_price && $product->discount_price < $product->price)
                    @php
                        $discountPercentage = round((($product->price - $product->discount_price) / $product->price) * 100);
                    @endphp
                    <span class="absolute top-4 left-4 discount-badge">-{{ $discountPercentage }}%</span>
                @endif
            </div>
            
            <!-- Thumbnails -->
            <div class="grid grid-cols-5 gap-2">
                <div class="thumbnail active border-2 rounded-md overflow-hidden" data-image="{{ asset('storage/' . $product->image) }}">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-20 object-cover">
                </div>
                
                <!-- Additional Images (if available) -->
                @if(isset($product->gallery) && is_array($product->gallery))
                    @foreach($product->gallery as $image)
                        <div class="thumbnail border-2 rounded-md overflow-hidden" data-image="{{ asset('storage/' . $image) }}">
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" class="w-full h-20 object-cover">
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="w-full lg:w-1/2">
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            
            <!-- Product Rating - Dynamically generated based on actual ratings if available -->
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
                <span class="text-gray-500 ml-2">({{ number_format($rating, 1) }}) - {{ $reviewCount }} Reviews</span>
            </div>
            
            <div class="mt-6">
                @if($product->discount_price && $product->discount_price < $product->price)
                    <div class="flex items-center">
                        <span class="text-3xl price">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                        <span class="text-xl text-gray-400 line-through ml-3">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>
                @else
                    <span class="text-3xl price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                @endif
            </div>
            
            <div class="mt-4">
                <div class="text-sm text-gray-500">Availability:</div>
                @if(isset($product->stock) && $product->stock > 0)
                    <div class="font-medium text-green-600 flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        In Stock ({{ $product->stock }} items)
                    </div>
                @else
                    <div class="font-medium text-red-600 flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Out of Stock
                    </div>
                @endif
            </div>
            
            @if(isset($product->short_description) && !empty($product->short_description))
                <div class="mt-4 text-gray-600">
                    {{ $product->short_description }}
                </div>
            @endif
            
            <!-- Add to Cart Form -->
            @auth
            <form id="add-to-cart-form" class="mt-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex items-center border border-gray-300 rounded-md">
                        <button type="button" id="decrease-qty" class="px-3 py-2 text-gray-600 hover:bg-gray-100">-</button>
                        <input type="number" id="quantity" name="quantity" min="1" value="1" max="{{ $product->stock ?? 10 }}" class="quantity-input">
                        <button type="button" id="increase-qty" class="px-3 py-2 text-gray-600 hover:bg-gray-100">+</button>
                    </div>
                    
                    <button type="submit" id="add-to-cart" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md flex items-center justify-center transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50" {{ isset($product->stock) && $product->stock <= 0 ? 'disabled' : '' }}>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Add to Cart
                    </button>
                </div>
            </form>
            @else
            <div class="mt-6">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Login to Add to Cart
                </a>
                <p class="text-sm text-gray-500 mt-2 text-center">You need to be logged in to add items to your cart</p>
            </div>
            @endauth
            
            <!-- Tambahkan ini di bagian detail produk, di dekat tombol Add to Cart -->
            @auth
            <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="mt-3">
                @csrf
                @php
                    $inWishlist = App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->exists();
                @endphp
                <button type="submit" class="flex items-center text-sm font-medium {{ $inWishlist ? 'text-red-600' : 'text-gray-600 hover:text-red-600' }} transition-colors">
                    <svg class="h-5 w-5 mr-1" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    {{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="flex items-center mt-3 text-sm font-medium text-gray-600 hover:text-red-600 transition-colors">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                Add to Wishlist
            </a>
            @endauth
            <!-- Product Description -->
            <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="font-medium text-gray-800 mb-2">Product Description</h4>
                <div class="text-gray-600 prose max-w-none">
                    @if(isset($product->description) && !empty($product->description))
                        {!! $product->description !!}
                    @else
                        <p>No detailed description available for this product.</p>
                    @endif
                </div>
            </div>
            <!-- Product Meta -->
            <div class="mt-6 border-t border-gray-200 pt-4">
                @if(isset($product->sku) && !empty($product->sku))
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <span class="font-medium mr-2">SKU:</span>
                        <span>{{ $product->sku }}</span>
                    </div>
                @endif
                
                <div class="flex items-center text-sm text-gray-500 mb-2">
                    <span class="font-medium mr-2">Category:</span>
                    @if($product->category)
                        <a href="{{ route('shop', ['category' => $product->category_id]) }}" class="text-blue-600 hover:underline">{{ $product->category->name }}</a>
                    @else
                        <span>Uncategorized</span>
                    @endif
                </div>
                
                @if(isset($product->brand) && $product->brand)
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <span class="font-medium mr-2">Brand:</span>
                        <span>{{ $product->brand->name }}</span>
                    </div>
                @endif
                
                <!-- Social Share Buttons -->
                <div class="flex items-center mt-4">
                    <span class="text-sm text-gray-500 mr-3">Share:</span>
                    <div class="flex space-x-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="text-gray-500 hover:text-blue-600" aria-label="Share on Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($product->name) }}&url={{ urlencode(request()->url()) }}" target="_blank" class="text-gray-500 hover:text-blue-400" aria-label="Share on Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.instagram.com/" target="_blank" class="text-gray-500 hover:text-pink-600" aria-label="Share on Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($product->name . ' ' . request()->url()) }}" target="_blank" class="text-gray-500 hover:text-green-600" aria-label="Share on WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Rest of the content remains unchanged -->
    <!-- ... -->

</div>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup CSRF token for all AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Quantity Input
        const decreaseBtn = document.getElementById('decrease-qty');
        const increaseBtn = document.getElementById('increase-qty');
        const quantityInput = document.getElementById('quantity');
        
        if (decreaseBtn && increaseBtn && quantityInput) {
            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                const maxStock = parseInt(quantityInput.getAttribute('max'));
                if (!maxStock || currentValue < maxStock) {
                    quantityInput.value = currentValue + 1;
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
                const currentValue = parseInt(this.value);
                const maxStock = parseInt(this.getAttribute('max'));
                
                if (currentValue < 1) {
                    this.value = 1;
                } else if (maxStock && currentValue > maxStock) {
                    this.value = maxStock;
                    Swal.fire({
                        title: 'Stock Limit',
                        text: `Sorry, only ${maxStock} items available in stock.`,
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                    });
                }
            });
        }
        
        // Thumbnail Images
        const mainImage = document.getElementById('main-image');
        const thumbnails = document.querySelectorAll('.thumbnail');
        
        if (mainImage && thumbnails.length > 0) {
            thumbnails.forEach(function(thumbnail) {
                thumbnail.addEventListener('click', function() {
                    // Update main image
                    mainImage.src = this.getAttribute('data-image');
                    
                    // Update active state
                    thumbnails.forEach(function(thumb) {
                        thumb.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });
        }
        
        // Tabs
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');
        
        if (tabLinks.length > 0 && tabContents.length > 0) {
            tabLinks.forEach(function(tabLink) {
                tabLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const tabId = this.getAttribute('data-tab');
                    
                    // Update active tab
                    tabLinks.forEach(function(link) {
                        link.classList.remove('border-blue-600', 'text-blue-600', 'active');
                        link.classList.add('border-transparent', 'text-gray-600');
                    });
                    
                    this.classList.remove('border-transparent', 'text-gray-600');
                    this.classList.add('border-blue-600', 'text-blue-600', 'active');
                    
                    // Show active content
                    tabContents.forEach(function(content) {
                        content.classList.remove('active');
                    });
                    
                    document.getElementById(tabId).classList.add('active');
                });
            });
        }
        
        // Add to Cart Form with AJAX
        const addToCartForm = document.getElementById('add-to-cart-form');
        
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const quantity = parseInt(document.getElementById('quantity').value || 1);
                const addToCartBtn = document.getElementById('add-to-cart');
                const productId = {{ $product->id }};
                
                // Disable button and show loading state
                addToCartBtn.disabled = true;
                addToCartBtn.classList.add('bg-gray-400');
                addToCartBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                addToCartBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                
                // Send AJAX request to add item to cart
                fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart count in navbar if it exists
                        const cartCountElement = document.querySelector('.cart-count');
                        if (cartCountElement) {
                            cartCountElement.textContent = data.cart_count;
                            
                            // Add animation to the cart count
                            cartCountElement.classList.add('animate-pulse');
                            setTimeout(() => {
                                cartCountElement.classList.remove('animate-pulse');
                            }, 2000);
                        }
                        
                        // Success animation on button
                        addToCartBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Added to Cart';
                        addToCartBtn.classList.remove('bg-gray-400');
                        addToCartBtn.classList.add('bg-green-600');
                        
                        // Show success message with SweetAlert
                        Swal.fire({
                            title: 'Added to Cart!',
                            text: `${quantity} item(s) added to your cart.`,
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Continue Shopping',
                            showDenyButton: true,
                            denyButtonText: 'View Cart',
                            denyButtonColor: '#10B981'
                        }).then((result) => {
                            if (result.isDenied) {
                                // Redirect to cart page
                                window.location.href = "{{ route('cart') }}";
                            } else {
                                // Reset button
                                setTimeout(() => {
                                    addToCartBtn.disabled = false;
                                    addToCartBtn.classList.remove('bg-green-600');
                                    addToCartBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                                    addToCartBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg> Add to Cart';
                                }, 2000);
                            }
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                        
                        // Reset button
                        addToCartBtn.disabled = false;
                        addToCartBtn.classList.remove('bg-gray-400');
                        addToCartBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                        addToCartBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg> Add to Cart';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6'
                    });
                    
                    // Reset button
                    addToCartBtn.disabled = false;
                    addToCartBtn.classList.remove('bg-gray-400');
                    addToCartBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    addToCartBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg> Add to Cart';
                });
            });
        }
        
        // Add to Wishlist
        const addToWishlistBtn = document.getElementById('add-to-wishlist');
        
        if (addToWishlistBtn) {
            addToWishlistBtn.addEventListener('click', function() {
                // You can implement wishlist functionality here with AJAX
                
                // SweetAlert notification
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Added to Wishlist',
                    showConfirmButton: false,
                    timer: 1500
                });
                
                // Change button appearance
                const icon = this.querySelector('svg');
                icon.classList.add('text-red-600');
            });
        }

        // Rest of your JavaScript code...
        
    });
</script>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity Input
        const decreaseBtn = document.getElementById('decrease-qty');
        const increaseBtn = document.getElementById('increase-qty');
        const quantityInput = document.getElementById('quantity');
        
        if (decreaseBtn && increaseBtn && quantityInput) {
            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                const maxStock = parseInt(quantityInput.getAttribute('max'));
                if (!maxStock || currentValue < maxStock) {
                    quantityInput.value = currentValue + 1;
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
                const currentValue = parseInt(this.value);
                const maxStock = parseInt(this.getAttribute('max'));
                
                if (currentValue < 1) {
                    this.value = 1;
                } else if (maxStock && currentValue > maxStock) {
                    this.value = maxStock;
                    Swal.fire({
                        title: 'Stock Limit',
                        text: `Sorry, only ${maxStock} items available in stock.`,
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                    });
                }
            });
        }
        
        // Thumbnail Images
        const mainImage = document.getElementById('main-image');
        const thumbnails = document.querySelectorAll('.thumbnail');
        
        if (mainImage && thumbnails.length > 0) {
            thumbnails.forEach(function(thumbnail) {
                thumbnail.addEventListener('click', function() {
                    // Update main image
                    mainImage.src = this.getAttribute('data-image');
                    
                    // Update active state
                    thumbnails.forEach(function(thumb) {
                        thumb.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });
        }
        
        // Tabs
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');
        
        if (tabLinks.length > 0 && tabContents.length > 0) {
            tabLinks.forEach(function(tabLink) {
                tabLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const tabId = this.getAttribute('data-tab');
                    
                    // Update active tab
                    tabLinks.forEach(function(link) {
                        link.classList.remove('border-blue-600', 'text-blue-600', 'active');
                        link.classList.add('border-transparent', 'text-gray-600');
                    });
                    
                    this.classList.remove('border-transparent', 'text-gray-600');
                    this.classList.add('border-blue-600', 'text-blue-600', 'active');
                    
                    // Show active content
                    tabContents.forEach(function(content) {
                        content.classList.remove('active');
                    });
                    
                    document.getElementById(tabId).classList.add('active');
                });
            });
        }
        
        // Add to Cart Form with SweetAlert
        const addToCartForm = document.getElementById('add-to-cart-form');
        
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const quantity = parseInt(document.getElementById('quantity').value || 1);
                const addToCartBtn = document.getElementById('add-to-cart');
                const productId = this.querySelector('input[name="product_id"]').value;
                
                // AJAX request to add item to cart would go here
                // For demonstration, we'll simulate with a timeout
                addToCartBtn.disabled = true;
                addToCartBtn.classList.add('bg-gray-400');
                addToCartBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                addToCartBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                
                setTimeout(() => {
                    // Create success message with SweetAlert
                    Swal.fire({
                        title: 'Added to Cart!',
                        text: `${quantity} item(s) added to your cart.`,
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Continue Shopping',
                        showDenyButton: true,
                        denyButtonText: 'View Cart',
                        denyButtonColor: '#10B981'
                    }).then((result) => {
                        if (result.isDenied) {
                            // Redirect to cart page
                            window.location.href = "{{ route('cart') }}";
                        }
                    });
                    
                    // Reset button
                    addToCartBtn.disabled = false;
                    addToCartBtn.classList.remove('bg-gray-400');
                    addToCartBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    addToCartBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg> Add to Cart';
                }, 1000);
            });
        }
        
        // Quick Add to Cart Buttons
        const quickAddButtons = document.querySelectorAll('.add-to-cart-quick');
        
        if (quickAddButtons.length > 0) {
            quickAddButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    
                    // AJAX request would go here
                    this.innerHTML = '<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                    
                    setTimeout(() => {
                        // Reset and show success
                        this.innerHTML = '<i class="fas fa-check"></i>';
                        
                        // SweetAlert notification
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        Toast.fire({
                            icon: 'success',
                            title: 'Item added to cart'
                        });
                        
                        setTimeout(() => {
                            this.innerHTML = '<i class="fas fa-shopping-cart"></i>';
                        }, 2000);
                    }, 800);
                });
            });
        }
        
        // Add to Wishlist
        const addToWishlistBtn = document.getElementById('add-to-wishlist');
        
        if (addToWishlistBtn) {
            addToWishlistBtn.addEventListener('click', function() {
                // AJAX request would go here
                
                // SweetAlert notification
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Added to Wishlist',
                    showConfirmButton: false,
                    timer: 1500
                });
                
                // Change button appearance
                const icon = this.querySelector('svg');
                icon.classList.add('text-red-600');
            });
        }
        
        // Rating stars
        const stars = document.querySelectorAll('.star-rating');
        const ratingInput = document.getElementById('rating-value');
        
        if (stars.length > 0 && ratingInput) {
            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('text-yellow-400');
                        } else {
                            s.classList.remove('text-yellow-400');
                        }
                    });
                });
                
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    ratingInput.value = rating;
                    
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('text-yellow-400');
                        } else {
                            s.classList.remove('text-yellow-400');
                        }
                    });
                });
            });
            
            document.querySelector('.rating-stars').addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value);
                
                stars.forEach((s, index) => {
                    if (index < currentRating) {
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                    }
                });
            });
        }
        
        // Review submission
        const reviewForm = document.getElementById('review-form');
        
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // AJAX request would go here
                

                // Clear form and show success
                Swal.fire({
                    title: 'Review Submitted!',
                    text: 'Thank you for your feedback.',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    this.reset();
                    ratingInput.value = 5;
                    stars.forEach((s, index) => {
                        if (index < 5) {
                            s.classList.add('text-yellow-400');
                        } else {
                            s.classList.remove('text-yellow-400');
                        }
                    });
                });
            });
        }
    });
</script>
@endsection
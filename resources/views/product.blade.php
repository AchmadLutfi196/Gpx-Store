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
            <form id="add-to-cart-form" class="mt-6" action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex items-center border border-gray-300 rounded-md">
                        <button type="button" id="decrease-qty" class="px-3 py-2 text-gray-600 hover:bg-gray-100">-</button>
                        <input type="number" id="quantity" name="quantity" min="1" value="1" max="{{ $product->stock ?? 10 }}" class="quantity-input">
                        <button type="button" id="increase-qty" class="px-3 py-2 text-gray-600 hover:bg-gray-100">+</button>
                    </div>
                    
                    <button type="submit" id="add-to-cart" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md flex items-center justify-center transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50" {{ isset($product->stock) && $product->stock <= 0 ? 'disabled' : '' }}>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
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
    
    <!-- Product Reviews Section -->
    <div class="mt-12 border-t border-gray-200 pt-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Customer Reviews</h2>
        </div>

        <!-- Review Summary -->
        <div class="flex flex-col md:flex-row gap-8 mb-8">
            <!-- Rating Statistics -->
            <div class="w-full md:w-1/3 bg-gray-50 rounded-lg p-6">
                <div class="text-center">
                    <div class="text-5xl font-bold text-gray-900 mb-2">{{ number_format($rating, 1) }}</div>
                    <div class="flex justify-center text-yellow-400 mb-2">
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
                    <div class="text-gray-500">Based on {{ $reviewCount }} reviews</div>
                </div>

                <!-- Rating Bars -->
                <div class="mt-6 space-y-3">
                    @php
                        $ratingCounts = [0, 0, 0, 0, 0];
                        foreach($product->reviews as $review) {
                            if ($review->rating >= 1 && $review->rating <= 5) {
                                $ratingCounts[$review->rating - 1]++;
                            }
                        }
                    @endphp

                    @for ($i = 5; $i >= 1; $i--)
                        @php 
                            $percentage = $reviewCount > 0 ? ($ratingCounts[$i - 1] / $reviewCount) * 100 : 0;
                        @endphp
                        <div class="flex items-center">
                            <div class="flex items-center w-16">
                                <span class="text-sm text-gray-600 mr-2">{{ $i }}</span>
                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 ml-2 mr-2">
                                <div class="bg-yellow-400 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="w-10 text-right text-sm text-gray-600">{{ $ratingCounts[$i - 1] }}</div>
                        </div>
                    @endfor
                </div>
            </div>

            

        <!-- Review List -->
        <div class="mt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $reviewCount }} Reviews</h3>
            
            @if($product->reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($product->reviews->sortByDesc('created_at') as $review)
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <h4 class="ml-3 font-medium text-gray-900">{{ $review->title }}</h4>
                                </div>
                                <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-500 mb-1">by {{ $review->user->name }}</p>
                            <p class="text-gray-600">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-6 text-center">
                    <p class="text-gray-600">This product has no reviews yet. Be the first to review this product!</p>
                </div>
            @endif
        </div>
    </div>

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

        // add to cart
        document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const quantity = parseInt(document.getElementById('quantity').value);
            const maxStock = parseInt(document.getElementById('quantity').getAttribute('max'));

            // Validate quantity against stock before submitting
            if (maxStock === 0) {
            Swal.fire({
                title: 'Out of Stock',
                text: 'Sorry, this product is currently out of stock',
                icon: 'error',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
            return;
            }

            if (quantity > maxStock) {
            // Reset quantity input to max stock
            document.getElementById('quantity').value = maxStock;
            
            Swal.fire({
                title: 'Stock Limit Exceeded',
                text: `Sorry, only ${maxStock} items available in stock`,
                icon: 'warning',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
            return;
            }

            if (quantity <= 0) {
            Swal.fire({
                title: 'Invalid Quantity',
                text: 'Please enter a valid quantity',
                icon: 'warning',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
            return;
            }
            
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
                title: 'Success!',
                text: 'Product added to cart successfully',
                icon: 'success',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
            })
            .catch(error => {
            let errorMessage = 'Failed to add product to cart';
            
            if (error.status === 422) {
                errorMessage = 'Product stock not available';
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


        document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const addToCartForm = document.getElementById('add-to-cart-form');
        const maxStock = parseInt(quantityInput?.getAttribute('max') || 0);
        
        if (addToCartForm && quantityInput) {
            addToCartForm.addEventListener('submit', function(e) {
                const quantity = parseInt(quantityInput.value);
                
                // Validasi stok
                if (isNaN(quantity) || quantity < 1) {
                    e.preventDefault();
                    alert('Jumlah minimal adalah 1');
                    return false;
                }
                
                if (quantity > maxStock) {
                    e.preventDefault();
                    alert(`Stok tidak cukup. Maksimal ${maxStock} item`);
                    return false;
                }
            });
        }
        
        // Validasi input langsung saat diubah
        if (quantityInput) {
            quantityInput.addEventListener('change', function() {
                let value = parseInt(this.value);
                
                if (isNaN(value) || value < 1) {
                    this.value = 1;
                } else if (value > maxStock) {
                    this.value = maxStock;
                    alert(`Jumlah maksimal adalah ${maxStock}`);
                }
            });
        }
    });


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
    });
</script>
@endsection
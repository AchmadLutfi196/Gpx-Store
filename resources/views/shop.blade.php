@extends('layouts.app')

@section('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .product-card img {
        transition: transform 0.3s ease;
    }
    
    .product-card:hover img {
        transform: scale(1.05);
    }
    
    .price {
        color: #3b82f6;
        font-weight: 600;
    }
    
    .discount-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #ef4444;
        color: white;
        padding: 3px 8px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 4px;
    }
    
    .wishlist-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    
    .wishlist-btn:hover {
        background-color: #fee2e2;
        color: #ef4444;
    }
    
    .category-card {
        transition: all 0.3s ease;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@section('content')
<!-- Banner -->
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    @if($selectedCategory)
                        {{ $selectedCategory->name }}
                    @elseif($search)
                        Search Results
                    @else
                        Shop
                    @endif
                </h1>
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
                                <span class="ml-1 text-gray-500 md:ml-2">Shop</span>
                            </div>
                        </li>
                        @if($selectedCategory)
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-gray-500 md:ml-2">{{ $selectedCategory->name }}</span>
                            </div>
                        </li>
                        @endif
                    </ol>
                </nav>
            </div>
            <div class="mt-4 md:mt-0">
                <select name="sort" class="bg-white border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="window.location.href = this.value">
                    <option value="{{ route('shop', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}" {{ $sort == 'newest' ? 'selected' : '' }}>Sort by: Newest</option>
                    <option value="{{ route('shop', array_merge(request()->except('sort'), ['sort' => 'price_asc'])) }}" {{ $sort == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="{{ route('shop', array_merge(request()->except('sort'), ['sort' => 'price_desc'])) }}" {{ $sort == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="{{ route('shop', array_merge(request()->except('sort'), ['sort' => 'name_asc'])) }}" {{ $sort == 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                    <option value="{{ route('shop', array_merge(request()->except('sort'), ['sort' => 'name_desc'])) }}" {{ $sort == 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Category Filter (Mobile) -->
    <div class="md:hidden mb-6">
        <button id="mobile-filter-button" class="w-full bg-white border border-gray-300 rounded-lg py-2 px-4 flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500">
            <span>Filter by Category</span>
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div id="mobile-filter-menu" class="hidden mt-2">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <ul class="space-y-2">
                    @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('shop', ['category' => $cat->id]) }}" class="block py-2 text-gray-600 hover:text-blue-600 {{ $category == $cat->id ? 'text-blue-600 font-medium' : '' }}">
                                {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row">
        <!-- Sidebar Filters (Desktop) -->
        <div class="hidden md:block w-64 mr-8">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="font-medium text-lg mb-4 border-b pb-2">Categories</h3>
                <ul class="space-y-2">
                    @foreach($categories as $cat)
                        <li class="flex items-center">
                            <a href="{{ route('shop', ['category' => $cat->id]) }}" class="text-gray-600 hover:text-blue-600 {{ $category == $cat->id ? 'text-blue-600 font-medium' : '' }}">
                                {{ $cat->name }}
                                @if(isset($cat->products))
                                    ({{ $cat->products->count() }})
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="font-medium text-lg mb-4 border-b pb-2">Price Range</h3>
                <form action="{{ route('shop') }}" method="GET">
                    @if($category)
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif
                    @if($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    <div class="mb-4">
                        <input type="range" min="0" max="2000000" step="10000" value="{{ $priceMax ?? 2000000 }}" class="w-full" id="price-slider">
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="bg-gray-100 rounded-md p-2 w-20">
                            <input type="number" name="price_min" value="{{ $priceMin ?? 0 }}" class="w-full bg-transparent border-none text-sm text-gray-600 focus:outline-none" min="0">
                        </div>
                        <span class="text-gray-500 mx-2">-</span>
                        <div class="bg-gray-100 rounded-md p-2 w-28">
                            <input type="number" name="price_max" value="{{ $priceMax ?? 2000000 }}" class="w-full bg-transparent border-none text-sm text-gray-600 focus:outline-none" min="0">
                        </div>
                    </div>
                    <button type="submit" class="w-full mt-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Apply</button>
                </form>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-medium text-lg mb-4 border-b pb-2">Brand</h3>
                <form action="{{ route('shop') }}" method="GET">
                    @if($category)
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif
                    @if($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    @if($priceMin)
                        <input type="hidden" name="price_min" value="{{ $priceMin }}">
                    @endif
                    @if($priceMax)
                        <input type="hidden" name="price_max" value="{{ $priceMax }}">
                    @endif
                    <ul class="space-y-2">
                        @foreach($brands as $brand)
                            <li class="flex items-center">
                                <input type="checkbox" id="brand{{ $brand->id }}" name="brand[]" value="{{ $brand->id }}" 
                                    {{ (is_array(request('brand')) && in_array($brand->id, request('brand'))) ? 'checked' : '' }}
                                    onchange="this.form.submit()" class="mr-3">
                                <label for="brand{{ $brand->id }}" class="text-gray-600">
                                    {{ $brand->name }}
                                    @if(isset($brand->products))
                                        ({{ $brand->products->count() }})
                                    @endif
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </form>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="flex-1">
            @if($search)
                <div class="mb-6">
                    <p class="text-gray-600">Search results for: <span class="font-medium">{{ $search }}</span></p>
                </div>
            @endif
            
            @if(isset($products) && count($products) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="relative overflow-hidden">
                                <a href="{{ route('product', ['id' => $product->id]) }}">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                    @else
                                        <img src="https://via.placeholder.com/300x300?text={{ urlencode($product->name) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                    @endif
                                </a>
                                @if($product->discount_price && $product->discount_price < $product->price)
                                    @php
                                        $discountPercentage = round((($product->price - $product->discount_price) / $product->price) * 100);
                                    @endphp
                                    <span class="discount-badge">-{{ $discountPercentage }}%</span>
                                @endif
                                {{-- <button class="wishlist-btn">
                                    <i class="far fa-heart"></i>
                                </button> --}}
                                @auth
                                <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="mt-3">
                                    @csrf
                                    @php
                                        $inWishlist = App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->exists();
                                    @endphp
                                    <button type="submit" class="wishlist-btn {{ $inWishlist ? 'text-red-600' : 'text-gray-600 hover:text-red-600' }} transition-colors">
                                        <i class="far fa-heart" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                                        </i>
                                        
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
                            </div>
                            <div class="p-4">
                                <a href="{{ route('product', ['id' => $product->id]) }}">
                                    <h2 class="text-gray-800 font-medium mb-1 hover:text-blue-600">{{ $product->name }}</h2>
                                </a>
                                @if(isset($product->brand) && $product->brand)
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        <span class="font-medium mr-2">Brand:</span>
                                        <span>{{ $product->brand->name }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= 4)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-gray-500 text-xs ml-1">({{ rand(5, 20) }})</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    @if($product->discount_price && $product->discount_price < $product->price)
                                        <div>
                                            <span class="price">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                            <span class="text-gray-400 text-sm line-through ml-1">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <span class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    @endif
                                    @auth
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="bg-gray-500 text-white p-2 rounded-full hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                            <i class="fas fa-shopping-cart"></i>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-10">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-10">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                    <div class="mt-6">
                        <a href="{{ route('shop') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            Clear all filters
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Mobile filter toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileFilterButton = document.getElementById('mobile-filter-button');
        const mobileFilterMenu = document.getElementById('mobile-filter-menu');
        
        if (mobileFilterButton && mobileFilterMenu) {
            mobileFilterButton.addEventListener('click', function() {
                mobileFilterMenu.classList.toggle('hidden');
            });
        }
        
        // Price slider (if you want to implement it)
        const priceSlider = document.getElementById('price-slider');
        const priceMaxInput = document.querySelector('input[name="price_max"]');
        
        if (priceSlider && priceMaxInput) {
            priceSlider.addEventListener('input', function() {
                priceMaxInput.value = this.value;
            });
        }
    });
</script>
@endsection
@extends('layouts.app')

@section('styles')
<style>
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .wishlist-remove {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        opacity: 0.8;
    }
    
    .wishlist-remove:hover {
        opacity: 1;
        background-color: #f56565;
        color: white;
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
                        <span class="ml-1 text-gray-500 md:ml-2">Wishlist</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Wishlist Content -->
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Wishlist</h1>
        
        @if($wishlists->count() > 0)
            <form action="{{ route('wishlist.clear') }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm" onclick="return confirm('Are you sure you want to clear your wishlist?')">
                    Clear All
                </button>
            </form>
        @endif
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    
    @if($wishlists->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($wishlists as $wishlist)
                <div class="bg-white rounded-lg shadow product-card relative">
                    <form action="{{ route('wishlist.remove', $wishlist->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="wishlist-remove">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </form>
                    
                    <a href="{{ route('product', $wishlist->product->id) }}">
                        <img src="{{ asset('storage/' . $wishlist->product->image) }}" alt="{{ $wishlist->product->name }}" class="w-full h-48 object-cover rounded-t-lg">
                    </a>
                    
                    <div class="p-4">
                        <a href="{{ route('product', $wishlist->product->id) }}">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $wishlist->product->name }}</h3>
                        </a>
                        
                        <div class="flex justify-between items-center mb-3">
                            @if($wishlist->product->discount_price && $wishlist->product->discount_price < $wishlist->product->price)
                                <div>
                                    <span class="text-lg font-bold text-blue-600">Rp {{ number_format($wishlist->product->discount_price, 0, ',', '.') }}</span>
                                    <span class="text-sm text-gray-500 line-through ml-2">Rp {{ number_format($wishlist->product->price, 0, ',', '.') }}</span>
                                </div>
                            @else
                                <span class="text-lg font-bold text-blue-600">Rp {{ number_format($wishlist->product->price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        
                        <form action="{{ route('cart.add', $wishlist->product->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Your wishlist is empty</h3>
            <p class="mt-1 text-gray-500">Browse our products and add items to your wishlist.</p>
            <div class="mt-6">
                <a href="{{ route('shop') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Browse Products
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission with AJAX
    const wishlistForms = document.querySelectorAll('form[action^="{{ route('wishlist.remove', 0) }}'.replace('/0', ''));
    
    wishlistForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const productCard = this.closest('.product-card');
            const formAction = this.getAttribute('action');
            const token = this.querySelector('input[name="_token"]').value;
            
            if (productCard) {
                productCard.style.opacity = '0.5';
            }
            
            fetch(formAction, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (productCard) {
                        productCard.remove();
                    }
                    
                    // If no more items in wishlist, reload page to show empty state
                    const remainingItems = document.querySelectorAll('.product-card');
                    if (remainingItems.length === 0) {
                        window.location.reload();
                    }
                } else {
                    if (productCard) {
                        productCard.style.opacity = '1';
                    }
                    alert(data.message || 'Failed to remove item from wishlist.');
                }
            })
            .catch(error => {
                if (productCard) {
                    productCard.style.opacity = '1';
                }
                console.error('Error:', error);
                alert('An error occurred while processing your request.');
            });
        });
    });
});
</script>
@endsection
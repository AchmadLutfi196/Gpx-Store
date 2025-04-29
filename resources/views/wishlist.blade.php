@extends('layouts.app')

@section('styles')
<style>
    .product-card {
        transition: all 0.4s ease;
        will-change: transform;
    }
    
    .product-card:hover {
        transform: translateY(-12px);
    }
    
    .product-card .quick-view {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }
    
    .product-card:hover .quick-view {
        opacity: 1;
        transform: translateY(0);
    }
    
    .product-card .product-img {
        transform: scale(1);
        transition: transform 0.6s cubic-bezier(0.215, 0.61, 0.355, 1);
    }
    
    .product-card:hover .product-img {
        transform: scale(1.08);
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
        z-index: 10;
    }
    
    .wishlist-remove:hover {
        opacity: 1;
        background-color: #f56565;
        color: white;
    }
    
    .quick-view {
        opacity: 0;
    }
    
    .product-card:hover .quick-view {
        opacity: 1;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
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
            <form action="{{ route('wishlist.clear') }}" method="POST" class="inline" id="clear-wishlist-form">
                @csrf
                @method('DELETE')
                <button type="button" class="text-red-600 hover:text-red-800 font-medium text-sm" onclick="confirmClearWishlist()">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($wishlists as $index => $wishlist)
            <div class="product-card bg-white rounded-xl shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <form action="{{ route('wishlist.remove', $wishlist->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="wishlist-remove" onclick="confirmRemove(event, '{{ $wishlist->product->name }}')">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </form>
                    
                    <div class="relative overflow-hidden">
                        <img class="product-img h-64 w-full object-cover" 
                             src="{{ asset('storage/' . $wishlist->product->image) }}" 
                             alt="{{ $wishlist->product->name }}">
                        
                        <!-- Quick View Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-30 opacity-0 transition-opacity duration-300 flex items-center justify-center quick-view">
                            <a href="{{ route('product', $wishlist->product->id) }}" class="bg-white rounded-full p-3 transform transition-all duration-300 hover:scale-110 hover:bg-blue-50 shadow-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors duration-300">{{ $wishlist->product->name }}</h3>
                            <div class="flex">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 {{ $i < ($wishlist->product->rating ?? 4) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-3 truncate overflow-hidden whitespace-normal line-clamp-2 h-10">
                            {{ $wishlist->product->description ?? 'No description available' }}
                        </p>
                        
                        <div class="flex justify-between items-center">
                            @if($wishlist->product->discount_price && $wishlist->product->discount_price < $wishlist->product->price)
                                <span class="text-blue-600 font-bold text-lg">Rp {{ number_format($wishlist->product->discount_price, 0, ',', '.') }}</span>
                            @else
                                <span class="text-blue-600 font-bold text-lg">Rp {{ number_format($wishlist->product->price, 0, ',', '.') }}</span>
                            @endif
                            
                            <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $wishlist->product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="p-2 bg-blue-600 hover:bg-blue-700 rounded-full text-white transition-colors duration-300 add-to-cart-button">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </button>
                                </form>
                        </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            
            // Create form data
            const formData = new FormData();
            formData.append('_token', token);
            formData.append('_method', 'DELETE');
            
            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (productCard) {
                        productCard.remove();
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Removed!',
                        text: data.message || 'Item telah dihapus dari wishlist.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // If no more items in wishlist, reload page to show empty state
                    const remainingItems = document.querySelectorAll('.product-card');
                    if (remainingItems.length === 0) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                } else {
                    if (productCard) {
                        productCard.style.opacity = '1';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: data.message || 'gagal menghapus dari wishlist.'
                    });
                }
            })
            .catch(error => {
                if (productCard) {
                    productCard.style.opacity = '1';
                }
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'An error occurred while processing your request.'
                });
            });
        });
    });
    
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
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
                    title: 'Berhasil!',
                    text: 'Produk berhasil ditambahkan ke keranjang',
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                let errorMessage = 'Gagal menambahkan produk ke keranjang';
                
                if (error.status === 422) {
                    errorMessage = 'Stok produk tidak tersedia';
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
    });
});
// Confirm remove item from wishlist
    function confirmClearWishlist() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak dapat mengembalikan tindakan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus semua!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('clear-wishlist-form').submit();
            }
        });
    }
</script>
@endsection

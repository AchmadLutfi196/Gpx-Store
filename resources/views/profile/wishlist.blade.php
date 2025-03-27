@extends('profile.layout')

@section('title', 'My Wishlist')

@section('breadcrumb')
<li aria-current="page">
    <div class="flex items-center">
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
        <span class="ml-1 text-gray-500 md:ml-2">Wishlist</span>
    </div>
</li>
@endsection

@section('profile-content')
@if(!isset($wishlists) || (isset($wishlists) && $wishlists->isEmpty()))
    <div class="text-center py-8">
        <div class="text-gray-400 mb-3">
            <i class="fas fa-heart fa-3x"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-1">Your wishlist is empty</h3>
        <p class="text-gray-500">Save items you like in your wishlist!</p>
        <a href="{{ route('shop') }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
            Browse Products
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($wishlists as $item)
        <div class="border rounded-lg overflow-hidden group relative">
            <form method="POST" action="{{ route('wishlist.remove', $item->id) }}" class="absolute top-2 right-2 z-10">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 bg-white rounded-full shadow-sm">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
            
            <div class="aspect-w-1 aspect-h-1 w-full">
                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" 
                     class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">
            </div>
            
            <div class="p-4">
                <h3 class="font-medium mb-2">
                    <a href="{{ route('product', $item->product->id) }}" class="hover:text-blue-600">
                        {{ $item->product->name }}
                    </a>
                </h3>
                
                <div class="flex justify-between items-center mb-3">
                    @if($item->product->discount_price && $item->product->discount_price < $item->product->price)
                        <div class="flex items-center">
                            <span class="text-lg font-semibold text-blue-600">Rp {{ number_format($item->product->discount_price, 0, ',', '.') }}</span>
                            <span class="text-sm text-gray-400 line-through ml-2">Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div>
                            <span class="text-lg font-semibold text-blue-600">Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
                
                <form method="POST" action="{{ route('cart.add', $item->product->id) }}">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="mt-8">
        {{ $wishlists->links() }}
    </div>
@endif
@endsection

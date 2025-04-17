@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Ulasan Saya</h1>
    
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
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
    @endif
    
    <!-- Pesanan yang belum direview -->
    @if($completedOrders->count() > 0)
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Pesanan yang Menunggu Ulasan</h2>
            <div class="space-y-4">
                @foreach($completedOrders as $order)
                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium">Order #{{ $order->order_number }}</p>
                            <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y') }}</p>
                            <p class="text-sm text-gray-700 mt-1">
                                {{ $order->items_count - $order->reviews_count }} dari {{ $order->items_count }} produk belum direview
                            </p>
                        </div>
                        <a href="{{ route('reviews.create', $order->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition">
                            Tulis Ulasan
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Ulasan yang sudah ditulis -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold mb-4">Ulasan Saya</h2>
        
        @if($reviews->count() > 0)
            <div class="space-y-6">
                @foreach($reviews as $review)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-shrink-0">
                                @if($review->product->image)
                                    <img src="{{ asset('storage/' . $review->product->image) }}" alt="{{ $review->product->name }}" class="w-16 h-16 object-cover rounded">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded">
                                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $review->product->name }}</h3>
                                        <div class="flex items-center mt-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="text-gray-600 text-sm mt-2">{{ $review->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <a href="{{ route('reviews.edit', $review->id) }}" class="text-blue-600 hover:text-blue-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        
                                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Yakin ingin menghapus ulasan ini?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <p class="text-gray-700">{{ $review->review }}</p>
                                </div>
                                
                                <div class="mt-3 text-xs text-gray-500">
                                    <p>Order #{{ $review->order->order_number }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada ulasan</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Anda belum menulis ulasan untuk produk yang Anda beli.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
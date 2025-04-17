@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Ulasan</h1>
            <p class="text-sm text-gray-600">Produk: {{ $review->product->name }}</p>
        </div>
        <div>
            <a href="{{ route('reviews.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Ulasan Saya
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
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
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex mb-6">
            <div class="flex-shrink-0">
                @if($review->product->image)
                    <img src="{{ asset('storage/' . $review->product->image) }}" alt="{{ $review->product->name }}" class="w-20 h-20 object-cover rounded">
                @else
                    <div class="w-20 h-20 bg-gray-200 flex items-center justify-center rounded">
                        <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">{{ $review->product->name }}</h3>
                <p class="text-sm text-gray-600">Dari order #{{ $review->order->order_number }}</p>
                <p class="text-sm text-gray-600">Ditambahkan pada: {{ $review->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <form action="{{ route('reviews.update', $review->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <div class="flex space-x-1" x-data="{ rating: {{ $review->rating }} }">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" @click="rating = {{ $i }}; document.getElementById('rating').value = {{ $i }}" class="focus:outline-none">
                        <svg class="w-8 h-8" :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </button>
                    @endfor
                    <input type="hidden" name="rating" id="rating" value="{{ $review->rating }}">
                </div>
            </div>
            
            <div>
                <label for="review" class="block text-sm font-medium text-gray-700 mb-2">Ulasan</label>
                <textarea id="review" name="review" rows="5" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Bagaimana pengalaman Anda menggunakan produk ini?">{{ $review->review }}</textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('reviews.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
@endpush
@endsection
@extends('profile.layout')

@section('title', 'My Orders')

@section('breadcrumb')
<li aria-current="page">
    <div class="flex items-center">
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
        <span class="ml-1 text-gray-500 md:ml-2">Orders</span>
    </div>
</li>
@endsection

@section('profile-content')
@if($orders->isEmpty())
    <div class="text-center py-8">
        <div class="text-gray-400 mb-3">
            <i class="fas fa-shopping-bag fa-3x"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-1">No orders yet</h3>
        <p class="text-gray-500">Looks like you haven't made any orders yet.</p>
        <a href="{{ route('shop') }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
            Start Shopping
        </a>
    </div>
@else
    <div class="space-y-6">
        @foreach($orders as $order)
        <div class="border rounded-lg overflow-hidden">
            <div class="bg-gray-50 p-4 flex flex-wrap items-center justify-between border-b">
                <div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-500">Order #:</span>
                        <span class="ml-2 text-blue-600 font-medium">{{ $order->order_number }}</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('F d, Y') }}</div>
                </div>
                
                <div class="mt-2 sm:mt-0">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->status == 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ !in_array($order->status, ['completed', 'processing', 'cancelled', 'pending']) ? 'bg-red-300 text-red-800' : '' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>                
                <div class="w-full sm:w-auto mt-2 sm:mt-0">
                    <a href="{{ route('orders.show', $order->id) }}" class="text-blue-600 hover:underline text-sm">
                        View Details
                    </a>
                </div>
            </div>
            
            <div class="p-4">
                <div class="space-y-3">
                    @foreach($order->items->take(2) as $item)
                    <div class="flex items-center">
                        <div class="w-16 h-16 flex-shrink-0 bg-gray-100 rounded overflow-hidden">
                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="ml-4">
                            <div class="font-medium">{{ $item->product->name }}</div>
                            <div class="text-sm text-gray-500">
                                Qty: {{ $item->quantity }} × Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($order->items->count() > 2)
                        <div class="text-sm text-gray-500">
                            + {{ $order->items->count() - 2 }} more item(s)
                        </div>
                    @endif
                </div>
                
                <div class="mt-4 border-t pt-4 flex justify-between">
                    <div class="text-gray-500">Total:</div>
                    <div class="font-medium">Rp {{ number_format($order->items->sum(fn($item) => ($item->price ?? 0) * $item->quantity), 0, ',', '.') }}</div>
                </div>
                
                <div class="mt-4 border-t pt-4 flex justify-between items-center">
                    
                    @if($order->status === 'processing')
                    <form action="{{ route('orders.complete', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition duration-150 ease-in-out shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Pesanan Diterima
                        </button>
                    </form>
                    @elseif($order->status === 'pending' || $order->payment_status === 'pending')
                        <div class="flex space-x-2">
                            <form action="{{ route('orders.regenerate-payment', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition duration-150 ease-in-out shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Lanjutkan Pembayaran
                                </button>
                            </form>
                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" id="cancelForm-{{ $order->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmCancel({{ $order->id }})" class="inline-flex items-center justify-center px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition duration-150 ease-in-out shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Batalkan Pesanan
                                </button>
                            </form>
                        </div>
                    @endif
                    
                    @if($order->status === 'completed')
                        <div class="mt-4">
                            @if($order->unreviewedItemsCount > 0)
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('reviews.create', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out shadow-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                        Tulis Ulasan
                                    </a>
                                </div>
                            @else
                                <div class="p-3 bg-gray-50 rounded-md text-center">
                                    <p class="text-sm text-gray-700">Terima kasih! Anda sudah memberikan ulasan untuk semua produk dalam pesanan ini.</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
    @endif
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmCancel(orderId) {
        Swal.fire({
            title: 'Konfirmasi Pembatalan',
            text: 'Apakah Anda yakin ingin membatalkan pesanan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancelForm-' + orderId).submit();
            }
        });
    }
    </script>

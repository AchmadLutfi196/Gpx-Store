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
                        @if($order->status == 'completed') bg-green-100 text-green-800
                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
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
                                Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
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
                    <div class="font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
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
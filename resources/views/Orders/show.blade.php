@extends('layouts.app')

@section('title', 'Order Details #' . $order->order_number)

@section('styles')
<style>
    .order-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .status-pending {
        background-color: #FEF3C7;
        color: #92400E;
    }
    
    .status-processing {
        background-color: #DBEAFE;
        color: #1E40AF;
    }
    
    .status-shipped {
        background-color: #D1FAE5;
        color: #065F46;
    }
    
    .status-delivered {
        background-color: #ECFDF5;
        color: #064E3B;
    }
    
    .status-cancelled {
        background-color: #FEE2E2;
        color: #991B1B;
    }
    
    .timeline-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #60A5FA;
    }
    
    .timeline-connector {
        width: 2px;
        background-color: #E5E7EB;
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
                        <a href="{{ route('profile.orders') }}" class="ml-1 text-gray-500 md:ml-2 hover:text-blue-600">My Orders</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">Order #{{ $order->order_number }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Order Details Content -->
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Order Summary and Status -->
        <div class="w-full lg:w-8/12">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                <span class="order-status {{ 'status-' . strtolower($order->status) }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-1">Order Details</h2>
                        <p class="text-gray-500 text-sm">Placed on {{ $order->created_at->format('F d, Y, h:i A') }}</p>
                    </div>
                    
                    <a href="{{ route('profile.orders') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                        <span class="hidden md:inline">Back to</span> All Orders
                    </a>
                </div>
                
                <!-- Order Timeline -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-base font-semibold mb-4">Order Timeline</h3>
                    
                    <div class="space-y-6">
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="timeline-dot"></div>
                                <div class="timeline-connector h-full"></div>
                            </div>
                            <div>
                                <p class="font-medium">Order Placed</p>
                                <p class="text-sm text-gray-500">{{ $order->created_at->format('F d, Y, h:i A') }}</p>
                            </div>
                        </div>
                        
                        @if($order->status == 'processing' || $order->status == 'shipped' || $order->status == 'delivered')
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="timeline-dot"></div>
                                <div class="timeline-connector h-full"></div>
                            </div>
                            <div>
                                <p class="font-medium">Processing Order</p>
                                <p class="text-sm text-gray-500">{{ $order->processed_at ? $order->processed_at->format('F d, Y, h:i A') : 'In progress' }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($order->status == 'shipped' || $order->status == 'delivered')
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="timeline-dot"></div>
                                <div class="timeline-connector h-full"></div>
                            </div>
                            <div>
                                <p class="font-medium">Order Shipped</p>
                                <p class="text-sm text-gray-500">{{ $order->shipped_at ? $order->shipped_at->format('F d, Y, h:i A') : 'In transit' }}</p>
                                @if($order->tracking_number)
                                <p class="text-sm text-gray-700 mt-1">Tracking Number: <span class="font-semibold">{{ $order->tracking_number }}</span></p>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($order->status == 'delivered')
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="timeline-dot"></div>
                                <div class="timeline-connector h-full"></div>
                            </div>
                            <div>
                                <p class="font-medium">Order Delivered</p>
                                <p class="text-sm text-gray-500">{{ $order->delivered_at ? $order->delivered_at->format('F d, Y, h:i A') : 'Completed' }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($order->status == 'cancelled')
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="timeline-dot bg-red-500"></div>
                                <div class="timeline-connector h-full"></div>
                            </div>
                            <div>
                                <p class="font-medium text-red-700">Order Cancelled</p>
                                <p class="text-sm text-gray-500">{{ $order->cancelled_at ? $order->cancelled_at->format('F d, Y, h:i A') : 'Cancelled' }}</p>
                                @if($order->cancellation_reason)
                                <p class="text-sm text-gray-700 mt-1">Reason: {{ $order->cancellation_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
                
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <div class="py-4 flex flex-wrap sm:flex-nowrap">
                        <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                            <img src="{{ asset('storage/' . ($item->product->image ?? 'products/default.jpg')) }}" alt="{{ $item->product_name }}" class="h-full w-full object-cover object-center">
                        </div>
                        
                        <div class="ml-4 flex flex-1 flex-col">
                            <div>
                                <div class="flex justify-between text-base font-medium text-gray-900">
                                    <h3>
                                        <a href="{{ route('product', $item->product_id) }}">{{ $item->product_name }}</a>
                                    </h3>
                                    <p class="ml-4">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                                @if($item->options)
                                <p class="mt-1 text-sm text-gray-500">
                                    @foreach(json_decode($item->options, true) ?? [] as $key => $value)
                                        <span class="mr-2">{{ ucfirst($key) }}: {{ is_array($value) ? implode(', ', $value) : $value }}</span>
                                    @endforeach
                                </p>
                                @endif
                            </div>
                            <div class="flex flex-1 items-end justify-between text-sm">
                                <p class="text-gray-500">Qty {{ $item->quantity }}</p>
                                
                                <div class="flex">
                                    <p class="font-medium">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Order Summary and Shipping Info -->
        <div class="w-full lg:w-4/12">
            <!-- Order Summary -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <p class="text-gray-600">Subtotal</p>
                        <p class="font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="flex justify-between">
                        <p class="text-gray-600">Shipping</p>
                        <p class="font-medium">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</p>
                    </div>
                    
                    @if($order->discount > 0)
                    <div class="flex justify-between text-blue-600">
                        <p>Discount</p>
                        <p class="font-medium">- Rp {{ number_format($order->discount, 0, ',', '.') }}</p>
                    </div>
                    @endif
                    
                    @if($order->tax > 0)
                    <div class="flex justify-between">
                        <p class="text-gray-600">Tax</p>
                        <p class="font-medium">Rp {{ number_format($order->tax, 0, ',', '.') }}</p>
                    </div>
                    @endif
                    
                    <div class="border-t border-gray-200 pt-3 flex justify-between">
                        <p class="text-lg font-semibold">Total</p>
                        <p class="text-lg font-semibold text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-3">
                        <p class="text-gray-600">Payment Method</p>
                        <p class="font-medium">{{ $order->payment_method ?? 'Bank Transfer' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Shipping Information -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Information</h2>
                
                @if($order->address)
                <div class="space-y-1 mb-4">
                    <p class="font-medium">{{ $order->address->name }}</p>
                    <p>{{ $order->address->phone }}</p>
                    <p>{{ $order->address->address_line1 }}</p>
                    @if($order->address->address_line2)
                    <p>{{ $order->address->address_line2 }}</p>
                    @endif
                    <p>{{ $order->address->district }}, {{ $order->address->city }}</p>
                    <p>{{ $order->address->province }}, {{ $order->address->postal_code }}</p>
                </div>
                @else
                <p class="text-gray-500">Shipping information not available</p>
                @endif
                
                @if($order->notes)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-base font-medium mb-2">Order Notes</h3>
                    <p class="text-gray-600">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('styles')
<style>
    /* Status badges */
    .order-status {
        @apply inline-block px-4 py-1.5 text-sm font-medium rounded-full shadow-sm;
    }
    .status-pending {
        @apply bg-yellow-100 text-yellow-800 border border-yellow-200;
    }
    .status-processing {
        @apply bg-blue-100 text-blue-800 border border-blue-200;
    }
    .status-completed {
        @apply bg-green-100 text-green-800 border border-green-200;
    }
    .status-failed {
        @apply bg-red-100 text-red-800 border border-red-200;
    }
    
    /* Section styling */
    .detail-section {
        @apply bg-white rounded-lg shadow-md p-7 mb-8 border border-gray-100;
    }
    
    .section-title {
        @apply text-xl font-semibold mb-6 text-gray-800 pb-3 border-b border-gray-200;
    }
    
    /* Tables */
    .table-responsive {
        @apply overflow-x-auto rounded-lg border border-gray-200 mt-4;
    }
    
    /* Buttons */
    .btn-primary {
        @apply inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition shadow-sm;
    }
    
    .btn-secondary {
        @apply inline-flex items-center justify-center px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-md hover:bg-gray-200 transition border border-gray-200 shadow-sm;
    }
    
    /* Info rows */
    .info-row {
        @apply flex justify-between py-3 border-b border-gray-100 last:border-0;
    }
    
    /* Payment card */
    .payment-badge {
        @apply px-3 py-1.5 rounded-md text-xs font-medium;
    }
    
    /* Courier badge */
    .courier-badge {
        @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border;
    }
    
    .courier-logo {
        height: 20px;
        width: auto;
        margin-right: 6px;
    }
    
    /* Shipping timeline */
    .shipping-timeline {
        @apply relative pl-6 pb-6;
    }
    
    .shipping-timeline:before {
        content: "";
        @apply absolute top-0 bottom-0 left-2.5 w-px bg-gray-200;
    }
    
    .shipping-timeline-item {
        @apply relative mb-4 last:mb-0;
    }
    
    .shipping-timeline-item:before {
        content: "";
        @apply absolute top-2 left-[-18px] w-4 h-4 rounded-full bg-white border-2 border-blue-500;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-6 py-10 max-w-7xl">
    <!-- Header with back button -->
    <div class="mb-8 flex items-center justify-between bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Detail Pesanan</h1>
            <p class="text-sm text-gray-600 flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Order ID: <span class="font-medium ml-1">{{ $order->order_number }}</span>
            </p>
        </div>
        <a href="{{ route('profile.orders') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Pesanan
        </a>
    </div>

    <!-- Notifications -->
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-5 mb-8 shadow-sm rounded-r-md" role="alert">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-5 mb-8 shadow-sm rounded-r-md" role="alert">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Order Overview -->
    <div class="detail-section">
        <div class="flex flex-col md:flex-row justify-between items-center border-b pb-6 mb-8">
            <div>
                <div class="flex items-center mb-3">
                    <h2 class="text-xl font-bold text-gray-900 mr-4">Informasi Pesanan</h2>
                    
                    @php
                        $statusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'completed' => 'bg-green-100 text-green-800 border-green-200',
                            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                            'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'failed' => 'bg-gray-100 text-gray-800 border-gray-200',
                        ];
                        
                        $statusClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                    @endphp
                    
                    <span class="px-3 py-1 text-sm font-medium rounded-full border {{ $statusClass }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-600 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Tanggal Pemesanan: {{ $order->created_at->format('d F Y, H:i') }}
                </p>
            </div>
            
            <div class="mt-5 md:mt-0 bg-blue-50 px-8 py-5 rounded-lg border border-blue-100">
                <p class="text-gray-600 text-sm mb-1">Total Pembayaran</p>
                <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Detail Items -->
        <div class="mb-10">
            <h3 class="font-semibold text-gray-800 mb-5 flex items-center">
                <svg class="w-5 h-5 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Items yang Dibeli
            </h3>
            
            <div class="table-responsive rounded-xl overflow-hidden shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        @if(isset($item->product) && $item->product && $item->product->image)
                                            <div class="flex-shrink-0 h-16 w-16 border border-gray-200 rounded-md overflow-hidden">
                                                <img class="h-16 w-16 object-cover" src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->name }}">
                                            </div>
                                            <div class="ml-5">
                                                <p class="text-gray-900 font-medium">{{ $item->name }}</p>
                                            </div>
                                        @else
                                            <p class="text-gray-900 font-medium">{{ $item->name }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-8 h-8 px-3 bg-gray-100 text-gray-800 font-medium rounded">{{ $item->quantity }}</span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="font-medium text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="border-t pt-7 mt-2">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Ringkasan Biaya
            </h3>
            
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-100">
                <div class="info-row">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium">Rp {{ number_format($order->total_amount - $order->shipping_amount - $order->tax_amount + $order->discount_amount, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-600">
                        Biaya Pengiriman 
                        <span class="inline-flex items-center bg-blue-50 text-blue-700 px-3 py-1 rounded-md text-xs font-medium ml-2">
                            @if($order->selected_courier && $order->selected_service)
                                <img src="{{ asset('images/couriers/' . strtolower($order->selected_courier) . '.png') }}" 
                                     alt="{{ $order->selected_courier }}" 
                                     class="h-4 mr-1.5 object-contain"
                                     onerror="this.style.display='none'">
                                {{ $order->selected_courier }} {{ $order->selected_service }}
                            @elseif($order->shipping_courier && $order->shipping_method)
                                <img src="{{ asset('images/couriers/' . strtolower($order->shipping_courier) . '.png') }}" 
                                     alt="{{ $order->shipping_courier }}" 
                                     class="h-4 mr-1.5 object-contain"
                                     onerror="this.style.display='none'">
                                {{ strtoupper($order->shipping_courier) }} {{ $order->shipping_method }}
                            @elseif($order->shipping_method)
                                {{ ucfirst($order->shipping_method) }}
                            @else
                                Reguler
                            @endif
                        </span>
                    </span>
                    <span class="font-medium">Rp {{ number_format($order->shipping_amount, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-600">Pajak (11%)</span>
                    <span class="font-medium">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>

                @if($order->discount_amount > 0)
                    <div class="info-row">
                        <span class="text-gray-600">Diskon</span>
                        <span class="font-medium text-red-600">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="flex justify-between py-4 mt-3 border-t border-gray-200">
                    <span class="text-base font-bold">Total</span>
                    <span class="text-lg font-bold text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    @php
    $shippingAddress = null;
    if (!empty($order->shipping_address)) {
        $shippingAddress = json_decode($order->shipping_address, true);
    }
    
    // Ambil nama dari user jika recipient_name null
    $recipientName = $shippingAddress['recipient_name'] ?? '';
    if (empty($recipientName) && !empty($order->user_id)) {
        $user = \App\Models\User::find($order->user_id);
        $recipientName = $user ? $user->name : 'Pelanggan';
    }
    @endphp

    <!-- Shipping & Payment Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Shipping Information -->
        <div class="detail-section">
            <h2 class="section-title flex items-center">
                <svg class="w-5 h-5 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Informasi Pengiriman
            </h2>
            
            <div class="bg-blue-50 rounded-lg p-6 border border-blue-100 mb-5">
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h3 class="font-medium text-blue-700">Penerima</h3>
                </div>
                <p class="text-gray-700 font-medium mb-2">{{ $recipientName ?: 'N/A' }}</p>
                <p class="text-gray-700">{{ $shippingAddress['phone'] ?? $order->shipping_phone ?? 'N/A' }}</p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <h3 class="font-medium text-gray-700">Alamat Pengiriman</h3>
                </div>
                <p class="text-gray-700 mb-2">
                    {{ $shippingAddress['address_line1'] ?: ($order->user->address ?? 'Alamat tidak tersedia') }}
                </p>
                
                @if(!empty($shippingAddress['address_line2']))
                    <p class="text-gray-700 mb-2">{{ $shippingAddress['address_line2'] }}</p>
                @endif
                
                <p class="text-gray-700">
                    {{ $shippingAddress['city'] ?? '' }}
                    {{ !empty($shippingAddress['city']) ? ',' : '' }} 
                    {{ $shippingAddress['province'] ?? '' }} 
                    {{ $shippingAddress['postal_code'] ?? $order->shipping_postal_code ?? '' }}
                </p>
            </div>
        </div>
        
        <!-- Payment Information -->
<div class="detail-section">
    <h2 class="section-title flex items-center">
        <svg class="w-5 h-5 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
        </svg>
        Status Pembayaran
    </h2>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-7 pb-5 border-b">
    <div>
        <p class="text-sm font-medium text-gray-700 mb-3">Status Saat Ini:</p>
        <div>
            @php
                // Prioritaskan status pesanan jika status adalah 'cancelled'
                $currentStatus = $order->status === 'cancelled' ? 'cancelled' : ($order->payment_status ?? $order->status);
                
                $statusClasses = [
                    // Status pesanan
                    'pending' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                    'processing' => 'bg-blue-100 text-blue-800 border border-blue-200',
                    'completed' => 'bg-green-100 text-green-800 border border-green-200',
                    'cancelled' => 'bg-red-100 text-red-800 border border-red-200',
                    'shipped' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                    'delivered' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                    'failed' => 'bg-gray-100 text-gray-800 border border-gray-200',
                    
                    // Status pembayaran
                    'paid' => 'bg-green-100 text-green-800 border border-green-200',
                    'unpaid' => 'bg-red-100 text-red-800 border border-red-200',
                    'refunded' => 'bg-purple-100 text-purple-800 border border-purple-200',
                    'expired' => 'bg-gray-100 text-gray-800 border border-gray-200',
                ];
                
                $statusClass = $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-800 border border-gray-200';
                
                // Tampilkan status pembayaran yang sesuai jika status adalah 'cancelled'
                $paymentStatusText = $order->status === 'cancelled' ? 'Pembayaran Dibatalkan' : ucfirst($currentStatus);
            @endphp
            
            <span class="px-3 py-1 text-sm font-medium rounded-full inline-block {{ $statusClass }}">
                {{ $paymentStatusText }}
            </span>
        </div>
    </div>

    {{-- Tombol aksi pembayaran --}}
    <div class="mt-4 md:mt-0">
        @if(($order->status === 'pending' || $order->payment_status === 'pending') && $order->status !== 'cancelled')
            @if($order->payment_token)
            <button id="pay-button" class="inline-flex items-center px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition duration-150 ease-in-out shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Selesaikan Pembayaran
            </button>
            @else
                <div class="flex flex-col sm:flex-row gap-2">
                    <form action="{{ route('orders.regenerate-payment', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition duration-150 ease-in-out shadow-sm w-full sm:w-auto justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Lanjutkan Pembayaran
                        </button>
                    </form>
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" id="cancelForm-{{ $order->id }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmCancel({{ $order->id }})" class="inline-flex items-center px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition duration-150 ease-in-out shadow-sm w-full sm:w-auto justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>
            @endif
        @elseif($order->status === 'cancelled')
            <p class="text-sm font-medium text-gray-700 mb-3">Pesanan ini telah dibatalkan:</p>
            <div class="w-full">
                <div class="bg-red-50 border border-red-100 rounded-lg p-1">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-10">
                        <div class="mt-3 md:mt-0 md:ml-auto">
                            @if($order->cancelled_at)
                                <span class="text-sm text-red-600">
                                    Dibatalkan pada: {{ \Carbon\Carbon::parse($order->cancelled_at)->format('d M Y, H:i') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

    @if($order->payment_details)
        <div>
            <p class="text-sm font-medium text-gray-700 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Detail Pembayaran:
            </p>
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                @php
                    $paymentDetails = json_decode($order->payment_details, true);
                    
                    // Jika detail pembayaran ada, tampilkan dengan rapi
                    if ($paymentDetails && is_array($paymentDetails)) {
                        // Format informasi penting terlebih dahulu
                        $importantInfo = [
                            'Status Transaksi' => $paymentDetails['transaction_status'] ?? null,
                            'Kode Status' => $paymentDetails['status_code'] ?? null,
                            'Order ID' => $paymentDetails['order_id'] ?? null,
                        ];
                @endphp
                
                <div class="space-y-3">
                    @foreach($importantInfo as $key => $value)
                        @if($value)
                        <div class="flex justify-between items-center py-2 px-4 rounded-md hover:bg-gray-100">
                            <span class="font-medium text-gray-700">{{ $key }}:</span>
                            <span class="
                                @if($key == 'Status Transaksi')
                                    @if($value == 'settlement' || $value == 'capture')
                                        payment-badge bg-green-100 text-green-800
                                    @elseif($value == 'pending')
                                        payment-badge bg-yellow-100 text-yellow-800
                                    @elseif($value == 'deny' || $value == 'cancel' || $value == 'expire')
                                        payment-badge bg-red-100 text-red-800
                                    @endif
                                @endif
                            ">
                                {{ ucfirst($value) }}
                            </span>
                        </div>
                        @endif
                    @endforeach
                </div>
                            
                        @php
                            // Hapus yang sudah ditampilkan di atas untuk menghindari duplikasi
                            unset($paymentDetails['transaction_status'], $paymentDetails['status_code'], $paymentDetails['order_id']);
                            
                            // Ensure payment details are properly formatted
                            // Skip non-scalar values that might cause template errors
                            $cleanDetails = [];
                            if(!empty($paymentDetails)) {
                                foreach($paymentDetails as $key => $value) {
                                    if(is_scalar($value) && !is_null($value) && $value !== '') {
                                        $cleanDetails[$key] = $value;
                                    }
                                }
                            }
                            
                            // Tampilkan detail tambahan jika ada
                            if(!empty($cleanDetails)) {
                        @endphp
                        <div class="mt-5 pt-4 border-t border-gray-200">
                            <p class="text-xs font-medium text-gray-700 mb-3">Informasi Tambahan:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                @foreach($cleanDetails as $key => $value)
                                    <div class="flex justify-between bg-white p-3 rounded border border-gray-100">
                                        <span class="text-gray-600">{{ str_replace('_', ' ', ucfirst($key)) }}:</span>
                                        <span class="font-medium">{{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @php
                            }
                        } else {
                        @endphp
                            <pre class="text-xs text-gray-600 bg-gray-50 p-4 rounded">{{ $order->payment_details }}</pre>
                        @php
                        }
                        @endphp
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Order Action Button -->
    @if($order->status === 'processing')
    <div class="bg-white rounded-lg shadow-md p-8 mt-8 text-center border border-gray-100">
        <p class="text-gray-700 mb-5">Pesanan Anda sedang dalam perjalanan. Jika Anda sudah menerima pesanan, silakan konfirmasi:</p>
        <form action="{{ route('orders.complete', $order->id) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center px-8 py-3.5 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition shadow-sm">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Pesanan Diterima
            </button>
        </form>
        <p class="mt-4 text-sm text-gray-500">
            Klik tombol di atas jika Anda sudah menerima pesanan Anda dengan baik.
        </p>
    </div>
    @endif

    {{-- Tombol cetak faktur --}}
    @if($order->status !== 'cancelled')
        <div class="border-t mt-8 pt-6 flex justify-end">
            <button
                id="print-invoice-button"
                type="button"
                class="inline-flex items-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5h4m16 0v5a2 2 0 01-2 2h-2m-8 4h4m-4 0v-4m4 4v-4" />
                </svg>
                Cetak Faktur
            </button>
        </div>
    @endif
    
    
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const printBtn = document.getElementById('print-invoice-button')
        if (!printBtn) return

        printBtn.addEventListener('click', () => {
            const style = document.createElement('style')
            style.textContent = `
                @page {
                    size: A4 portrait;
                    margin: 1cm;
                }
                @media print {
                    /* hide interactive and footer elements */
                    footer,
                    .btn-primary,
                    .btn-secondary,
                    #print-invoice-button,
                    #pay-button,
                    form,
                    a { display: none !important; }

                    /* full–width container */
                    .container {
                        max-width: 100% !important;
                        margin: 0 !important;
                        padding: 0 !important;
                    }

                    /* remove all backgrounds and shadows, force black text */
                    *,
                    *::before,
                    *::after {
                        background: none !important;
                        box-shadow: none !important;
                        -webkit-print-color-adjust: exact;
                        color: #000 !important;
                    }

                    /* table styling */
                    table {
                        width: 100% !important;
                        border-collapse: collapse !important;
                    }
                    th, td {
                        border: 1px solid #333 !important;
                        padding: 0.3em !important;
                        page-break-inside: avoid !important;
                    }

                    /* avoid splitting sections across pages */
                    .detail-section,
                    .table-responsive,
                    .info-row,
                    thead,
                    tbody {
                        page-break-inside: avoid !important;
                    }

                    /* optional: reduce base font-size for fitting one page */
                    body {
                        font-size: 12px !important;
                    }
                }
            `
            document.head.appendChild(style)
            window.print()
        })

        // SweetAlert untuk konfirmasi pembatalan pesanan
        const cancelForm = document.querySelector('form[action*="cancel"]');
        if (cancelForm) {
            cancelForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Batalkan Pesanan?',
                    text: "Apakah Anda yakin ingin membatalkan pesanan ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                })
            });
        }
    })
</script>
@if($order->status === 'pending' || $order->payment_status === 'pending')
    @if($order->payment_token)
        <!-- Tambahkan Midtrans Snap.js -->
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            document.getElementById('pay-button').onclick = function() {
                // Simpan SnapToken ke variabel
                var snapToken = '{{ $order->payment_token }}';
                
                // Memanggil snap.pay dan membuka popup pembayaran
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        /* Anda dapat menyimpan hasil transaksi di sini, 
                           atau cukup mengarahkan ke halaman terima kasih */
                        window.location.href = '{{ route("payment.finish", $order->id) }}?' + 
                            'transaction_status=' + result.transaction_status +
                            '&status_code=' + result.status_code +
                            '&order_id=' + result.order_id;
                    },
                    onPending: function(result) {
                        window.location.href = '{{ route("payment.finish", $order->id) }}?' + 
                            'transaction_status=' + result.transaction_status +
                            '&status_code=' + result.status_code +
                            '&order_id=' + result.order_id;
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal: ' + result.status_message);
                        window.location.href = '{{ route("payment.finish", $order->id) }}?' + 
                            'transaction_status=error' +
                            '&status_code=' + result.status_code +
                            '&status_message=' + result.status_message +
                            '&order_id=' + result.order_id;
                    },
                    onClose: function() {
                        alert('Anda menutup popup pembayaran tanpa menyelesaikan pembayaran');
                    }
                });
            };
        </script>
    @endif
@endif
@endsection
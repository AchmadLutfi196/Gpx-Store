@extends('layouts.app')

@section('styles')
<style>
    .order-status {
        @apply inline-block px-3 py-1 text-sm font-semibold rounded-full;
    }
    .status-pending {
        @apply bg-yellow-100 text-yellow-800;
    }
    .status-processing {
        @apply bg-blue-100 text-blue-800;
    }
    .status-completed {
        @apply bg-green-100 text-green-800;
    }
    .status-failed {
        @apply bg-red-100 text-red-800;
    }
    
    .detail-section {
        @apply bg-white rounded-lg shadow-sm p-6 mb-6;
    }
    
    .section-title {
        @apply text-xl font-semibold mb-4;
    }
    
    .table-responsive {
        @apply overflow-x-auto;
    }
    
    .btn-primary {
        @apply inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition;
    }
    
    .btn-secondary {
        @apply inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Pesanan</h1>
            <p class="text-sm text-gray-600">Order ID: {{ $order->order_number }}</p>
        </div>
        <div>
            <a href="{{ route('profile.orders') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Pesanan
            </a>
        </div>
    </div>

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

    <div class="detail-section">
        <div class="flex flex-col md:flex-row justify-between border-b pb-4 mb-4">
            <div>
                <h2 class="section-title">Informasi Pesanan</h2>
                <p class="text-sm text-gray-600">Tanggal Pemesanan: {{ $order->created_at->format('d F Y, H:i') }}</p>
                <div class="mt-2">
                    <span class="order-status {{ 'status-' . $order->status }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="text-right">
                    <p class="text-gray-600 text-sm">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Detail Items -->
        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Items yang Dibeli</h3>
            
            <div class="table-responsive border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if(isset($item->product) && $item->product && $item->product->image)
                                            <div class="flex-shrink-0 h-16 w-16">
                                                <img class="h-16 w-16 rounded object-cover" src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->name }}">
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-gray-900 font-medium">{{ $item->name }}</p>
                                            </div>
                                        @else
                                            <p class="text-gray-900 font-medium">{{ $item->name }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-gray-900">{{ $item->quantity }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="border-t pt-4">
            <div class="flex justify-between py-1">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-medium">Rp {{ number_format($order->total_amount - $order->shipping_amount - $order->tax_amount + $order->discount_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-gray-600">Biaya Pengiriman ({{ ucfirst($order->shipping_method) }})</span>
                <span class="font-medium">Rp {{ number_format($order->shipping_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-gray-600">Pajak (11%)</span>
                <span class="font-medium">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
            </div>

            @if($order->discount_amount > 0)
                <div class="flex justify-between py-1">
                    <span class="text-gray-600">Diskon</span>
                    <span class="font-medium text-red-600">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="flex justify-between py-3 border-t mt-2">
                <span class="text-base font-semibold">Total</span>
                <span class="text-lg font-bold text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Shipping Information -->
    <div class="detail-section">
        <h2 class="section-title">Informasi Pengiriman</h2>
        
        @php
            $shippingAddress = json_decode($order->shipping_address, true);
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-900 mb-1">Penerima:</p>
                <p class="text-gray-700">{{ $shippingAddress['recipient_name'] ?? 'Belum ada' }}</p>
                <p class="text-gray-700">{{ $shippingAddress['phone'] ?? $order->shipping_phone }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-900 mb-1">Alamat Pengiriman:</p>
                <p class="text-gray-700">{{ $shippingAddress['street_address'] ?? 'Belum ada' }}</p>
                @if(!empty($shippingAddress['address_line2']))
                    <p class="text-gray-700">{{ $shippingAddress['address_line2'] }}</p>
                @endif
                <p class="text-gray-700">
                    {{ $shippingAddress['city'] ?? '' }}, 
                    {{ $shippingAddress['province'] ?? '' }} 
                    {{ $shippingAddress['postal_code'] ?? $order->shipping_postal_code }}
                </p>
            </div>
        </div>
        
        <div class="mt-4 border-t pt-4">
            <p class="text-sm font-medium text-gray-900 mb-1">Metode Pengiriman:</p>
            <p class="text-gray-700">{{ ucfirst($order->shipping_method) }} Shipping</p>
            
            @if(!empty($order->notes))
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-900 mb-1">Catatan Pesanan:</p>
                    <p class="text-gray-700">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Payment Information -->
    <div class="detail-section">
        <h2 class="section-title">Status Pembayaran</h2>
        
        <div class="flex items-center justify-between border-b pb-4 mb-4">
            <div>
                <p class="text-sm font-medium text-gray-900">Status:</p>
                <div class="mt-2">
                    <span class="order-status {{ 'status-' . ($order->payment_status ?? $order->status) }}">
                        {{ ucfirst($order->payment_status ?? $order->status) }}
                    </span>
                </div>
            </div>
            
            <div class="text-right">
                @if($order->status === 'pending' || $order->payment_status === 'pending')
                    @if($order->payment_token)
                    <button id="pay-button" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition">
                        Selesaikan Pembayaran
                    </button>
                    @else
                        <form action="{{ route('orders.regenerate-payment', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-primary">
                                Lanjutkan Pembayaran
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
        
        @if($order->payment_details)
            <div>
                <p class="text-sm font-medium text-gray-900 mb-2">Detail Pembayaran:</p>
                <div class="bg-gray-50 p-3 rounded">
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
                    
                    <div class="space-y-1">
                        @foreach($importantInfo as $key => $value)
                            @if($value)
                            <div class="flex justify-between">
                                <span class="font-medium">{{ $key }}:</span>
                                <span class="
                                    @if($key == 'Status Transaksi')
                                        @if($value == 'settlement' || $value == 'capture')
                                            text-green-600 font-medium
                                        @elseif($value == 'pending')
                                            text-yellow-600 font-medium
                                        @elseif($value == 'deny' || $value == 'cancel' || $value == 'expire')
                                            text-red-600 font-medium
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
                        
                        // Tampilkan detail tambahan jika ada
                        if(!empty($paymentDetails)) {
                    @endphp
                    <div class="mt-2 pt-2 border-t border-gray-200">
                        <p class="text-xs font-medium text-gray-700 mb-1">Informasi Tambahan:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-1 text-xs">
                            @foreach($paymentDetails as $key => $value)
                                @if(is_scalar($value) && !is_null($value) && $value !== '')
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ str_replace('_', ' ', ucfirst($key)) }}:</span>
                                    <span>{{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @php
                        }
                    } else {
                    @endphp
                        <pre class="text-xs text-gray-600">{{ $order->payment_details }}</pre>
                    @php
                    }
                    @endphp
                </div>
            </div>
        @endif
        @if($order->status === 'processing')
        <div class="mt-6 text-center">
            <form action="{{ route('orders.complete', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Pesanan Diterima
                </button>
            </form>
            <p class="mt-2 text-sm text-gray-500">
                Klik tombol di atas jika Anda sudah menerima pesanan Anda.
            </p>
        </div>
        @endif


@endsection

@section('scripts')
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
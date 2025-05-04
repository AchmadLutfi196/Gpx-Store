@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <div class="bg-white rounded-lg shadow-md p-8 text-center mb-8">
        <div class="flex flex-col items-center">
            <!-- Error Icon -->
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            
            <!-- Error Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Error</h1>
            <p class="text-lg text-gray-700 mb-8">{{ $errorMessage }}</p>
            
            <!-- Order Details -->
            <div class="w-full max-w-md p-6 bg-gray-50 rounded-lg mb-8">
                <h2 class="text-xl font-medium text-gray-800 mb-4">Order Summary</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order Number:</span>
                        <span class="font-medium">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span>{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Payment Failed
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Retry Button -->
            <div class="flex flex-col space-y-4 w-full max-w-xs">
                <a href="{{ route('order.retry-payment', $order) }}" class="btn-primary w-full flex justify-center items-center py-3">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Retry Payment
                </a>
                
                <a href="{{ route('orders') }}" class="text-blue-600 hover:text-blue-800 text-center">
                    View Order History
                </a>
                
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-800 text-center">
                    Return to Home Page
                </a>
            </div>
        </div>
    </div>
    
    <!-- Customer Support Information -->
    <div class="text-center">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Need Help?</h3>
        <p class="text-gray-600 mb-2">Contact our customer support team</p>
        <p class="text-blue-600">support@gpxstore.com</p>
        <p class="text-blue-600">+62 812-3456-7890</p>
    </div>
</div>
@endsection
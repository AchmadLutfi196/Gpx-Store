@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Payment Failed</h3>
                <div class="mt-3">
                    <p class="text-sm text-gray-500">We couldn't process your payment. Please try again or contact our customer support for assistance.</p>
                </div>
                
                <div class="mt-6">
                    <div class="text-sm text-gray-600 border-t border-b border-gray-200 py-4">
                        <p>Order ID: {{ $order_id }}</p>
                        <p>Status: {{ $transaction_status }}</p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-center gap-4">
                    <a href="{{ route('checkout') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Try Again
                    </a>
                    <a href="{{ route('shop') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
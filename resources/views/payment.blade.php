@extends('layouts.app')

@section('styles')
<style>
    .payment-container {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .payment-status {
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }
    
    .payment-details {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .payment-details h3 {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
    }

    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 10px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="payment-container">
        <h1 class="text-2xl font-bold text-center mb-8">Complete Your Payment</h1>
        
        <div class="payment-status bg-blue-50 text-blue-700">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>Please complete your payment to proceed with the order.</p>
            </div>
        </div>
        
        <div class="payment-details">
            <h3 class="text-lg font-semibold">Order Summary</h3>
            
            <div class="detail-row">
                <span class="text-gray-600">Order Number:</span>
                <span class="font-medium">{{ $order->order_number }}</span>
            </div>
            
            <div class="detail-row">
                <span class="text-gray-600">Total Amount:</span>
                <span class="font-medium">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            
            <div class="mt-8">
                <button id="pay-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition duration-200 flex items-center justify-center">
                    <span>Pay Now</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
            </div>
            
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">You will be redirected to the payment gateway.</p>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Return to Home Page</a>
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div id="loading-overlay" class="loading-overlay">
    <div class="spinner"></div>
    <p>Processing payment, please wait...</p>
</div>
@endsection

@section('scripts')
<!-- Make sure to use the correct Midtrans environment -->
@if(config('app.env') === 'production')
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@else
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const payButton = document.getElementById('pay-button');
        const loadingOverlay = document.getElementById('loading-overlay');
        
        // Function to show loading overlay
        function showLoading() {
            loadingOverlay.style.display = 'flex';
        }
        
        // Function to hide loading overlay
        function hideLoading() {
            loadingOverlay.style.display = 'none';
        }
        
        // Check if snap token exists
        const snapToken = '{{ $snapToken ?? "" }}';
        
        if (!snapToken) {
            console.error('Snap token is missing');
            payButton.disabled = true;
            payButton.classList.add('bg-gray-400');
            payButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            alert('Payment initialization failed. Please contact support.');
        }
        
        payButton.addEventListener('click', function() {
            showLoading();
            
            try {
                // Make sure snap is loaded
                if (typeof window.snap === 'undefined') {
                    console.error('Snap.js is not loaded properly');
                    hideLoading();
                    alert('Payment system is not available. Please try again later.');
                    return;
                }
                
                // Trigger snap popup
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        window.location.href = '{{ route("payment.finish", $order->id) }}?transaction_status=settlement&order_id={{ $order->order_number }}&transaction_id=' + (result.transaction_id || '');
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        window.location.href = '{{ route("payment.finish", $order->id) }}?transaction_status=pending&order_id={{ $order->order_number }}&transaction_id=' + (result.transaction_id || '');
                    },
                    onError: function(result) {
                        console.error('Payment error:', result);
                        hideLoading();
                        alert('Payment failed: ' + (result.message || 'Unknown error'));
                        window.location.href = '{{ route("payment.finish", $order->id) }}?transaction_status=deny&order_id={{ $order->order_number }}&transaction_id=' + (result.transaction_id || '');
                    },
                    onClose: function() {
                        console.log('Customer closed the payment window');
                        hideLoading();
                        alert('Payment cancelled. Please complete your payment to process the order.');
                    }
                });
            } catch (error) {
                console.error('Error initiating payment:', error);
                hideLoading();
                alert('Failed to initialize payment. Please try again or contact support.');
            }
        });
        
        // Auto trigger payment popup after a short delay for better UX
        setTimeout(function() {
            payButton.click();
        }, 1500);
    });
</script>
@endsection
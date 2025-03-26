@extends('layouts.app')

@section('styles')
<style>
    .loading-spinner {
        width: 40px;
        height: 40px;
        margin: 100px auto;
        background-color: #3b82f6;
        border-radius: 100%;  
        -webkit-animation: sk-scaleout 1.0s infinite ease-in-out;
        animation: sk-scaleout 1.0s infinite ease-in-out;
    }

    @-webkit-keyframes sk-scaleout {
        0% { -webkit-transform: scale(0) }
        100% {
            -webkit-transform: scale(1.0);
            opacity: 0;
        }
    }

    @keyframes sk-scaleout {
        0% { 
            -webkit-transform: scale(0);
            transform: scale(0);
        } 
        100% {
            -webkit-transform: scale(1.0);
            transform: scale(1.0);
            opacity: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Processing Payment</h1>
            <p class="text-gray-600 mb-8">Please wait while we redirect you to our payment gateway...</p>
            
            <div class="loading-spinner mb-6"></div>
            
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-700">Order Number: {{ $order->order_number }}</p>
                <p class="text-sm text-gray-500">Total Amount: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
            
            <div class="text-xs text-gray-500">
                <p>If you are not redirected automatically, please click the button below.</p>
            </div>
            
            <button id="pay-button" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                Pay Now
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include Midtrans Snap JS library -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Debug info
        console.log('DOM loaded');
        console.log('Snap Token:', '{{ $snapToken }}');
        console.log('Client Key:', '{{ $clientKey }}');
        
        // Automatically trigger the payment popup after a short delay
        setTimeout(function() {
            console.log('Triggering payment button click');
            document.getElementById('pay-button').click();
        }, 1500);
        
        // Snap.js initialization
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            console.log('Payment button clicked');
            try {
                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        /* You may add your own implementation here */
                        console.log('Payment success:', result);
                        window.location.href = '{{ route('checkout.finish') }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status;
                    },
                    onPending: function(result) {
                        /* You may add your own implementation here */
                        console.log('Payment pending:', result);
                        window.location.href = '{{ route('checkout.unfinish') }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status;
                    },
                    onError: function(result) {
                        /* You may add your own implementation here */
                        console.log('Payment error:', result);
                        window.location.href = '{{ route('checkout.error') }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status;
                    },
                    onClose: function() {
                        /* You may add your own implementation here */
                        console.log('Payment widget closed without completion');
                        alert('You closed the payment window without completing your payment');
                    }
                });
            } catch (e) {
                console.error('Error in snap.pay:', e);
                alert('Payment error: ' + e.message);
            }
        });
    });
</script>
@endsection
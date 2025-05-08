<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #3b82f6;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Confirmed!</h1>
        </div>
        <div class="content">
            <p>Hello {{ $customerName }},</p>
            
            <p>Thank you for your purchase. We are pleased to confirm that your payment for order #{{ $orderNumber }} has been successfully processed.</p>
            
            <p>Your payment of Rp {{ number_format($amount, 0, ',', '.') }} has been received.</p>
            
            <p>Your order is now being processed and will be shipped soon.</p>
            
            <p>Thank you for shopping with us!</p>
            
            <p>Best regards,<br>GPX Store Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} GPX Store. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

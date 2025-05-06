<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #718096;
        }
        .order-details {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Confirmed!</h1>
        </div>
        <div class="content">
            <p>Hello {{ $order->name }},</p>
            
            <p>Thank you for your purchase. We are pleased to confirm that your payment has been successfully processed.</p>
            
            <div class="order-details">
                <h3>Order #{{ $order->order_number }}</h3>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                <p><strong>Payment Method:</strong> Credit/Debit Card</p>
                <p><strong>Shipping Address:</strong> {{ $order->address }}</p>
                
                <h4>Order Summary:</h4>
                
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right"><strong>Subtotal:</strong></td>
                            <td>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right"><strong>Shipping:</strong></td>
                            <td>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right"><strong>Tax:</strong></td>
                            <td>Rp {{ number_format($order->tax, 0, ',', '.') }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td colspan="2" class="text-right"><strong>Discount:</strong></td>
                            <td>- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="2" class="text-right"><strong>Total:</strong></td>
                            <td><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <p>Your items will be shipped soon. You will receive a shipping confirmation email once your order is on its way.</p>
            
            <a href="{{ route('orders.show', $order->id) }}" class="button">View Order Details</a>
            
            <p>If you have any questions about your order, please contact our customer service team at <a href="mailto:support@gpxstore.com">support@gpxstore.com</a> or call us at (021) 1234-5678.</p>
            
            <p>Thank you for shopping with us!</p>
            
            <p>Best regards,<br>GPX Store Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} GPX Store. All rights reserved.</p>
            <p>This email was sent to {{ $order->email }} because you made a purchase at our store.</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Email GPX Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
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
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 0 0 5px 5px;
            border: 1px solid #e5e7eb;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.8rem;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Konfigurasi Email Berhasil</h1>
    </div>
    
    <div class="content">
        <h2>Selamat!</h2>
        <p>Email ini adalah konfirmasi bahwa sistem email GPX Store telah berhasil dikonfigurasi dan berfungsi dengan baik.</p>
        <p>Anda sekarang dapat mengirimkan:</p>
        <ul>
            <li>Notifikasi pembelian</li>
            <li>Konfirmasi pesanan</li>
            <li>Newsletter</li>
            <li>Promo dan penawaran khusus</li>
        </ul>
        <p>Waktu pengiriman: <strong>{{ now()->format('d M Y, H:i:s') }}</strong></p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} GPX Store. All rights reserved.</p>
    </div>
</body>
</html>

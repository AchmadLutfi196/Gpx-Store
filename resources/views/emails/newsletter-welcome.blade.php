<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to GPX Store Newsletter</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
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
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 10px 20px;
            margin: 20px 0;
            text-decoration: none;
            border-radius: 5px;
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
        <h1>Selamat Datang di Newsletter GPX Store!</h1>
    </div>
    <div class="content">
        <p>Halo!</p>
        <p>Terima kasih telah berlangganan newsletter GPX Store. Kami senang Anda bergabung dengan komunitas kami!</p>
        <p>Dengan berlangganan, Anda akan menerima:</p>
        <ul>
            <li>Informasi produk terbaru</li>
            <li>Promo dan diskon eksklusif</li>
            <li>Tips dan trik seputar produk kami</li>
            <li>Berita dan update dari GPX Store</li>
        </ul>
        <p>Sebagai ucapan terima kasih, kami memberikan kode diskon <strong>WELCOME15</strong> untuk pembelian pertama Anda dengan diskon 15%.</p>
        <a href="{{ route('shop') }}" class="button">Belanja Sekarang</a>
        <p>Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami di <a href="mailto:support@gpxstore.com">support@gpxstore.com</a>.</p>
        <p>Salam hangat,<br>Tim GPX Store</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} GPX Store. All rights reserved.</p>
        <p>Jika Anda tidak ingin menerima email dari kami di masa mendatang, silakan <a href="#">klik di sini</a> untuk berhenti berlangganan.</p>
    </div>
</body>
</html>

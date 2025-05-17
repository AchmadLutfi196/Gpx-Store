<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->title }}</title>
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
        <h1>{{ $newsletter->title }}</h1>
    </div>
    <div class="content">
        {!! $newsletter->content !!}
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} GPX Store. All rights reserved.</p>
        <p>Jika Anda tidak ingin menerima email dari kami di masa mendatang, silakan <a href="#">klik di sini</a> untuk berhenti berlangganan.</p>
    </div>
</body>
</html>

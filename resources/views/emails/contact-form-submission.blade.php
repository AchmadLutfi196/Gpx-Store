@component('mail::message')
# Terima Kasih atas Pesan Anda

Halo {{ $contactMessage->name }},

Terima kasih telah menghubungi Gpx-Store. Pesan Anda dengan subjek **{{ $contactMessage->subject }}** telah kami terima dan sedang dalam proses.

**ID Pesan Anda:** #{{ $contactMessage->id }}

Tim customer service kami akan segera merespons pesan Anda. Anda dapat mengecek status pesan Anda kapan saja dengan mengklik tombol di bawah:

@component('mail::button', ['url' => route('message.check-status')])
Cek Status Pesan
@endcomponent

Saat mengecek status, Anda akan memerlukan:
- Email: {{ $contactMessage->email }}
- ID Pesan: {{ $contactMessage->id }}

Salam,<br>
Tim Customer Service {{ config('app.name') }}

---
*Ini adalah email otomatis. Mohon tidak membalas email ini.*
@endcomponent
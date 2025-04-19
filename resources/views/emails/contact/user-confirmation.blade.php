@component('mail::message')
# Pesan Baru dari Form Kontak

Anda telah menerima pesan baru dari form kontak website Gpx-Store.

**Pengirim:** {{ $contactMessage->name }}  
**Email:** {{ $contactMessage->email }}  
**Telepon:** {{ $contactMessage->phone ?? 'Tidak disertakan' }}  
**Subjek:** {{ $contactMessage->subject }}

**Pesan:**  
{{ $contactMessage->message }}

@if(isset($url))
@component('mail::button', ['url' => $url])
Lihat di Admin Panel
@endcomponent
@endif

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
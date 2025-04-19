@component('mail::message')
# Pesan Baru dari Form Kontak

Anda telah menerima pesan baru dari form kontak website Gpx-Store.

**Pengirim:** {{ $contactMessage->name }}  
**Email:** {{ $contactMessage->email }}  
**Telepon:** {{ $contactMessage->phone ?? 'Tidak disertakan' }}  
**Subjek:** {{ $contactMessage->subject }}

**Pesan:**  
{{ $contactMessage->message }}

@component('mail::button', ['url' => config('app.url') . '/admin/resources/contact-messages/' . $contactMessage->id])
Lihat di Admin Panel
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
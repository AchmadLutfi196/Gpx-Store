@component('mail::message')
# Balasan untuk Pesan Anda 

Halo {{ $message->name }},

Terima kasih telah menghubungi Gpx-Store. Kami telah menanggapi pesan Anda dengan subjek "{{ $message->subject }}".

## Pesan Anda:
{{ $message->message }}

## Balasan dari Tim Customer Service:
{{ $message->admin_response }}

Jika masih ada pertanyaan, silakan hubungi kembali tim customer service kami dengan membalas email ini atau melalui form kontak di website kami.

Terima kasih,<br>
Tim Customer Service {{ config('app.name') }}
@endcomponent
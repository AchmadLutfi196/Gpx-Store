@extends('layouts.app')

@section('title', 'Cek Status Pesan - Gpx-Store')

@section('meta_description', 'Cek status dan balasan pesan kontak Anda di Gpx-Store.')

@section('content')
<div class="bg-gradient-to-b from-blue-50 to-white py-12">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Cek Status Pesan</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Masukkan detail pesan Anda untuk melihat status dan balasan dari tim customer service kami.</p>
        </div>

        <div class="max-w-md mx-auto">
            <div class="bg-white p-8 rounded-lg shadow-md">
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <form action="{{ route('message.view-status') }}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Anda <span class="text-red-500">*</span></label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" 
                                value="{{ old('email') }}" 
                                placeholder="Email yang digunakan saat mengirim pesan"
                                required
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message_id" class="block text-sm font-medium text-gray-700 mb-1">ID Pesan <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md">
                                    #
                                </span>
                                <input 
                                    type="number" 
                                    id="message_id" 
                                    name="message_id" 
                                    class="flex-1 min-w-0 block w-full px-4 py-2 border border-gray-300 rounded-none rounded-r-md focus:ring-blue-500 focus:border-blue-500 @error('message_id') border-red-500 @enderror" 
                                    value="{{ old('message_id') }}" 
                                    placeholder="ID pesan yang diberikan saat konfirmasi"
                                    required
                                >
                            </div>
                            @error('message_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">ID pesan diberikan saat konfirmasi pengiriman pesan (contoh: #123)</p>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Cek Status Pesan
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tidak Ingat ID Pesan?</h3>
                    <p class="text-gray-600 mb-4">Jika Anda tidak ingat ID pesan Anda, silakan periksa:</p>
                    <ul class="list-disc pl-5 space-y-2 text-gray-600">
                        <li>Email konfirmasi yang dikirim setelah Anda mengirim pesan.</li>
                        <li>Halaman konfirmasi yang muncul setelah mengirim pesan.</li>
                        <li>Jika tetap tidak bisa menemukan ID pesan, <a href="{{ route('contact.index') }}" class="text-blue-600 hover:text-blue-800">hubungi kami</a> dengan menyertakan detail pesan sebelumnya.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
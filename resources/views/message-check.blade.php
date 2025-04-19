@extends('layouts.app')

@section('title', 'Cek Status Pesan - Gpx-Store')

@section('meta_description', 'Cek status dan balasan pesan kontak Anda di Gpx-Store.')

@section('content')
<div class="bg-gradient-to-b from-blue-50 to-white py-12">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Cek Status Pesan</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Masukkan email Anda untuk melihat semua pesan yang telah Anda kirim dan balasan dari tim customer service kami.</p>
        </div>

        <div class="max-w-md mx-auto">
            <div class="bg-white p-8 rounded-lg shadow-md">
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 mb-6" role="alert">
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

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Lihat Semua Pesan
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi</h3>
                    <p class="text-gray-600 mb-4">Dengan memasukkan email Anda, sistem akan menampilkan semua pesan yang pernah Anda kirim ke Gpx-Store.</p>
                    <ul class="list-disc pl-5 space-y-2 text-gray-600">
                        <li>Anda dapat melihat status dari setiap pesan.</li>
                        <li>Jika pesan sudah dibalas, Anda dapat membaca balasan dari tim customer service.</li>
                        <li>Jika Anda menggunakan email yang berbeda saat mengirim pesan, cek menggunakan email tersebut.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
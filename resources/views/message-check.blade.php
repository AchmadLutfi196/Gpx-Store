@extends('layouts.app')

@section('title', 'Pusat Bantuan - Gpx-Store')

@section('meta_description', 'Akses dukungan dan layanan pelanggan di Pusat Bantuan Gpx-Store.')

@section('content')
<div class="bg-gradient-to-b from-blue-50 to-white py-12">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Pusat Bantuan</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Untuk berkomunikasi dengan tim customer service dan melacak status pesan Anda, silakan masuk ke akun Anda.</p>
        </div>

        <div class="max-w-lg mx-auto">
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <svg class="w-16 h-16 mx-auto text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Sistem Baru: Pusat Layanan Pelanggan</h2>
                
                <p class="text-gray-600 mb-6">
                    Kami telah meningkatkan sistem layanan pelanggan kami. Sekarang Anda dapat mengirim pesan, melihat riwayat percakapan, dan mendapatkan balasan dari tim kami di tempat yang sama.
                </p>

                @auth
                    <a href="{{ route('customer-support.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Akses Pusat Bantuan
                    </a>
                @else
                    <div class="space-y-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Masuk ke Akun
                        </a>
                        
                        <div class="text-sm text-gray-600">
                            Belum memiliki akun?
                            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Daftar sekarang</a>
                        </div>
                    </div>
                @endauth

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Kenapa harus membuat akun?</h3>
                    <ul class="text-gray-600 space-y-2 text-left">
                        <li class="flex items-start">
                            <svg class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="ml-2">Riwayat percakapan tersimpan dan mudah diakses</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="ml-2">Notifikasi saat ada balasan dari customer service</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="ml-2">Lebih mudah untuk menindaklanjuti pertanyaan atau masalah</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="ml-2">Integrasi dengan informasi pesanan dan akun Anda</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
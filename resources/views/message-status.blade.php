@extends('layouts.app')

@section('title', 'Detail Status Pesan #' . $message->id . ' - Gpx-Store')

@section('meta_description', 'Detail dan status pesan kontak #' . $message->id . ' di Gpx-Store.')

@section('content')
<div class="bg-gradient-to-b from-blue-50 to-white py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detail Pesan #{{ $message->id }}</h1>
                <p class="text-gray-600 mt-1">Dikirim pada: {{ $message->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <a href="{{ route('message.check-status') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Cek Pesan Lain
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Status Header -->
            <div class="bg-gray-50 px-6 py-4 border-b">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ $message->subject }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Dari: {{ $message->name }} ({{ $message->email }})</p>
                    </div>
                    <div>
                        @if($message->response_sent)
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Sudah Dibalas
                            </span>
                        @else
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Menunggu Balasan
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Original Message -->
            <div class="p-6 border-b">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Pesan Anda:</h3>
                    <div class="bg-gray-50 rounded-md p-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $message->message }}</p>
                    </div>
                </div>

                <!-- Contact Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 text-sm">
                    <div class="border rounded p-3">
                        <span class="block text-gray-500">Nama:</span>
                        <span class="font-medium text-gray-800">{{ $message->name }}</span>
                    </div>
                    <div class="border rounded p-3">
                        <span class="block text-gray-500">Email:</span>
                        <span class="font-medium text-gray-800">{{ $message->email }}</span>
                    </div>
                    @if($message->phone)
                    <div class="border rounded p-3">
                        <span class="block text-gray-500">Telepon:</span>
                        <span class="font-medium text-gray-800">{{ $message->phone }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Admin Response -->
            <div class="p-6">
                @if($message->response_sent && $message->admin_response)
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 p-2 rounded-full mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Balasan dari Tim Customer Service</h3>
                                <p class="text-sm text-gray-500">Dibalas pada: {{ \Carbon\Carbon::parse($message->updated_at)->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="bg-blue-50 rounded-md p-5 border border-blue-100">
                            <p class="text-gray-700 whitespace-pre-line">{{ $message->admin_response }}</p>
                        </div>
                    </div>
                    
                    <!-- Rating/Feedback Form (Optional) -->
                    <div class="mt-8 border-t pt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Bagaimana pengalaman Anda?</h4>
                        <p class="text-gray-600 mb-4">Apakah balasan kami membantu menyelesaikan pertanyaan atau masalah Anda?</p>
                        
                        <div class="flex space-x-4">
                            <button type="button" class="flex-1 bg-white border border-gray-300 rounded-md py-2 px-4 flex items-center justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"></path>
                                </svg>
                                Ya, Sangat Membantu
                            </button>
                            <button type="button" class="flex-1 bg-white border border-gray-300 rounded-md py-2 px-4 flex items-center justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.105-1.79l-.05-.025A4 4 0 0011.055 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z"></path>
                                </svg>
                                Tidak Membantu
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 p-6 border border-yellow-100 rounded-md">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-yellow-800">Pesan Anda sedang diproses</h3>
                                <div class="mt-2 text-yellow-700">
                                    <p>Tim customer service kami sedang meninjau pesan Anda dan akan segera memberikan balasan. Proses ini biasanya membutuhkan waktu 1-2 hari kerja.</p>
                                    <p class="mt-3">Terima kasih atas kesabaran Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t">
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('contact.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium text-sm flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim Pesan Baru
                    </a>
                    <a href="#" onclick="window.print();" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 py-2 px-4 rounded-md font-medium text-sm flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak Percakapan
                    </a>
                </div>
            </div>
        </div>

        <!-- Related FAQs (Optional) -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Pertanyaan yang Mungkin Terkait</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Bagaimana cara melacak pesanan?</h3>
                    <p class="text-gray-600 mb-4">Setiap pesanan yang Anda lakukan akan diberikan nomor resi. Anda dapat menggunakan nomor tersebut untuk melacak status pengiriman pesanan Anda.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">Baca selengkapnya</a>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Berapa lama waktu pengiriman?</h3>
                    <p class="text-gray-600 mb-4">Waktu pengiriman bervariasi tergantung lokasi. Untuk wilayah Jawa biasanya 2-4 hari kerja, sedangkan luar Jawa 4-7 hari kerja.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">Baca selengkapnya</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
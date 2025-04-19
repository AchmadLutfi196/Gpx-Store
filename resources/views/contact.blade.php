@extends('layouts.app')

@section('title', 'Hubungi Kami - Gpx-Store')

@section('meta_description', 'Hubungi tim customer service Gpx-Store untuk pertanyaan, bantuan, atau dukungan teknis.')

@section('content')
<div class="bg-gradient-to-b from-blue-50 to-white py-12">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Hubungi Kami</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Ada pertanyaan atau butuh bantuan? Tim customer service kami siap membantu. Silahkan isi form di bawah ini dan kami akan merespons secepat mungkin.</p>
        </div>

        <div class="max-w-5xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Contact Info Cards -->
                <div class="col-span-1">
                    <div class="space-y-6">
                        <!-- Email Support -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center space-x-4">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Email</h3>
                                    <p class="text-gray-600">support@gpx-store.com</p>
                                </div>
                            </div>
                        </div>

                        <!-- Phone Support -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center space-x-4">
                                <div class="bg-green-100 p-3 rounded-full">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Telepon</h3>
                                    <p class="text-gray-600">+62 812-3456-7890</p>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center space-x-4">
                                <div class="bg-red-100 p-3 rounded-full">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Alamat</h3>
                                    <p class="text-gray-600">Jl. Contoh No. 123, Jakarta</p>
                                </div>
                            </div>
                        </div>

                        <!-- Operating Hours -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center space-x-4">
                                <div class="bg-yellow-100 p-3 rounded-full">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Jam Operasional</h3>
                                    <p class="text-gray-600">Senin - Jumat: 08.00 - 17.00</p>
                                    <p class="text-gray-600">Sabtu: 09.00 - 15.00</p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Sosial Media</h3>
                            <div class="flex space-x-4">
                                <a href="#" class="text-blue-500 hover:text-blue-700">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"></path>
                                    </svg>
                                </a>
                                <a href="#" class="text-pink-500 hover:text-pink-700">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"></path>
                                    </svg>
                                </a>
                                <a href="#" class="text-blue-400 hover:text-blue-600">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                                    </svg>
                                </a>
                                <a href="#" class="text-red-500 hover:text-red-700">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-span-1 md:col-span-2">
                    <div class="bg-white p-8 rounded-lg shadow-md">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Kirim Pesan</h2>

                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-6 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M14.293 5.293a1 1 0 011.414 0l.293.293V7a1 1 0 01-2 0V5.586l.293.293a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        name="name" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                                        value="{{ old('name') }}" 
                                        required
                                    >
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" 
                                        value="{{ old('email') }}" 
                                        required
                                    >
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                    <input 
                                        type="text" 
                                        id="phone" 
                                        name="phone" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror" 
                                        value="{{ old('phone') }}"
                                        placeholder="Contoh: 081234567890"
                                    >
                                    @error('phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Subject -->
                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subjek <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        id="subject" 
                                        name="subject" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('subject') border-red-500 @enderror" 
                                        value="{{ old('subject') }}" 
                                        required
                                    >
                                    @error('subject')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Message -->
                                <div class="md:col-span-2">
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Pesan <span class="text-red-500">*</span></label>
                                    <textarea 
                                        id="message" 
                                        name="message" 
                                        rows="5" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('message') border-red-500 @enderror" 
                                        required
                                    >{{ old('message') }}</textarea>
                                    @error('message')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            Kirim Pesan
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <!-- Di bawah form kontak, tambahkan: -->
        <div class="mt-8 p-4 bg-blue-50 rounded-md">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Sudah mengirim pesan sebelumnya?</h3>
            <p class="text-blue-700 mb-3">Anda dapat mengecek status pesan dan balasan dari tim customer service kami.</p>
            <a href="{{ route('message.check-status') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Cek Status Pesan
            </a>
        </div>
    </div>

        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Pertanyaan Umum</h2>
            
            <div class="max-w-3xl mx-auto">
                <div class="space-y-4">
                    <!-- FAQ Item 1 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button class="w-full flex justify-between items-center p-4 focus:outline-none bg-white hover:bg-gray-50" onclick="toggleFaq(this)">
                            <span class="font-medium text-gray-800">Bagaimana cara melacak pesanan saya?</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="hidden p-4 bg-gray-50 faq-content">
                            <p class="text-gray-600">Anda dapat melacak pesanan dengan masuk ke akun Anda, pilih menu "Pesanan Saya" dan klik tombol "Lacak" pada pesanan yang ingin Anda lacak. Alternatif lain, Anda dapat menggunakan nomor resi yang dikirimkan ke email Anda dan memasukkannya ke halaman pelacakan di website kurir yang digunakan.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button class="w-full flex justify-between items-center p-4 focus:outline-none bg-white hover:bg-gray-50" onclick="toggleFaq(this)">
                            <span class="font-medium text-gray-800">Berapa lama waktu pengiriman?</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="hidden p-4 bg-gray-50 faq-content">
                            <p class="text-gray-600">Waktu pengiriman bervariasi tergantung lokasi dan metode pengiriman yang dipilih. Pengiriman reguler biasanya membutuhkan 2-4 hari kerja untuk wilayah Jawa dan 4-7 hari kerja untuk luar Jawa. Untuk pengiriman ekspres, biasanya membutuhkan 1-2 hari kerja.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button class="w-full flex justify-between items-center p-4 focus:outline-none bg-white hover:bg-gray-50" onclick="toggleFaq(this)">
                            <span class="font-medium text-gray-800">Bagaimana cara melakukan pengembalian produk?</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="hidden p-4 bg-gray-50 faq-content">
                            <p class="text-gray-600">Untuk melakukan pengembalian produk, ikuti langkah-langkah berikut:</p>
                            <ol class="list-decimal ml-5 mt-2">
                                <li>Masuk ke akun Anda</li>
                                <li>Buka halaman "Pesanan Saya"</li>
                                <li>Pilih pesanan yang ingin dikembalikan</li>
                                <li>Klik tombol "Ajukan Pengembalian"</li>
                                <li>Isi formulir pengembalian dengan alasan dan bukti foto (jika diperlukan)</li>
                                <li>Tunggu konfirmasi dari tim kami</li>
                            </ol>
                            <p class="mt-2 text-gray-600">Pengembalian harus diajukan dalam waktu 7 hari sejak produk diterima.</p>
                        </div>
                    </div>
                    
                    
                    <!-- FAQ Item 4 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button class="w-full flex justify-between items-center p-4 focus:outline-none bg-white hover:bg-gray-50" onclick="toggleFaq(this)">
                            <span class="font-medium text-gray-800">Metode pembayaran apa saja yang tersedia?</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="hidden p-4 bg-gray-50 faq-content">
                            <p class="text-gray-600">Kami menerima berbagai metode pembayaran, termasuk:</p>
                            <ul class="list-disc ml-5 mt-2">
                                <li>Kartu Kredit (Visa, Mastercard, JCB)</li>
                                <li>Transfer Bank (BCA, Mandiri, BNI, BRI)</li>
                                <li>E-wallet (GoPay, OVO, Dana, ShopeePay)</li>
                                <li>Virtual Account</li>
                                <li>Cicilan 0% (untuk pembelian minimal Rp 500.000)</li>
                            </ul>
                        </div>
                    </div>
                </div>
                    <!-- Map Section -->
                <div class="mt-16">
                    <div class="rounded-lg overflow-hidden shadow-md">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2773226946512!2d106.82768931536993!3d-6.22968996288741!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3bffbf44d8d%3A0xb4c14ce06c61066!2sJl.%20Sudirman%2C%20Daerah%20Khusus%20Ibukota%20Jakarta!5e0!3m2!1sid!2sid!4v1619023458189!5m2!1sid!2sid"
                            width="100%"
                            height="450"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFaq(element) {
        // Toggle icon rotation
        const icon = element.querySelector('.faq-icon');
        icon.classList.toggle('rotate-180');
        
        // Toggle content visibility
        const content = element.nextElementSibling;
        content.classList.toggle('hidden');
    }
</script>
@endsection
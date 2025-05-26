@extends('layouts.app')

@section('title', 'Pusat Bantuan - Customer Support')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Pusat Layanan Pelanggan</h1>
                <p class="mt-1 text-gray-600">Lihat semua percakapan dan dapatkan bantuan dari tim kami.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <!-- Header with Action Button -->
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-800">Riwayat Percakapan</h2>
                    <button type="button" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none"
                            onclick="toggleNewMessageForm()">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Pesan Baru
                    </button>
                </div>
                
                <!-- New Message Form (Hidden by Default) -->
                <div id="newMessageForm" class="hidden px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <form action="{{ route('customer-support.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subjek</label>
                            <input type="text" name="subject" id="subject" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Pesan</label>
                            <textarea name="message" id="message" rows="4" required
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
                                    onclick="toggleNewMessageForm()">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                                Kirim Pesan
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Conversation List -->
                <div>
                    @if($conversations->isEmpty())
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <h3 class="mt-2 text-base font-medium text-gray-900">Belum ada percakapan</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai percakapan baru dengan tim support kami.</p>
                            <div class="mt-6">
                                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none"
                                    onclick="toggleNewMessageForm()">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Pesan Baru
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="divide-y divide-gray-200">
                            @foreach($conversations as $conversationId => $messages)
                                @php 
                                    $lastMessage = $messages->first();
                                    $unreadCount = $messages->where('is_read', false)->where('admin_response', '!=', null)->count();
                                    $firstMessage = $messages->last();
                                @endphp
                                
                                <a href="{{ route('customer-support.conversation', $conversationId) }}" class="block p-6 hover:bg-gray-50 transition-colors relative">
                                    <!-- Unread indicator -->
                                    @if($unreadCount > 0)
                                        <span class="absolute right-6 top-6 flex h-5 w-5">
                                            <span class="animate-ping absolute h-5 w-5 rounded-full bg-blue-400 opacity-75"></span>
                                            <span class="relative rounded-full h-5 w-5 bg-blue-500 flex items-center justify-center text-xs text-white">
                                                {{ $unreadCount }}
                                            </span>
                                        </span>
                                    @endif
                                    
                                    <div class="sm:flex sm:justify-between sm:items-start">
                                        <div class="sm:flex-1">
                                            <h3 class="text-base font-medium text-gray-900">{{ $firstMessage->subject }}</h3>
                                            <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                                                {{ $lastMessage->admin_response ?? $lastMessage->message }}
                                            </p>
                                            
                                            <div class="mt-3 flex items-center text-sm text-gray-500">
                                                <div class="flex space-x-4">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span>{{ $lastMessage->created_at->format('d M Y') }}</span>
                                                    </div>
                                                    
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span>{{ $lastMessage->created_at->format('H:i') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 sm:mt-0 sm:ml-4">
                                            @if($lastMessage->admin_response)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Terjawab
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Menunggu
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleNewMessageForm() {
        const form = document.getElementById('newMessageForm');
        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            // Focus on subject field
            document.getElementById('subject').focus();
        } else {
            form.classList.add('hidden');
        }
    }
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}"
        });
    });
</script>
@endif
@endsection

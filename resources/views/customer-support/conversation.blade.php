@extends('layouts.app')

@section('title', 'Detail Percakapan - Customer Support')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('customer-support.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar Percakapan
                </a>
            </div>
            
            @php
                $firstMessage = $messages->first();
            @endphp
            
            <!-- Conversation Header -->
            <div class="bg-white rounded-t-lg shadow-sm px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">{{ $firstMessage->subject }}</h1>
                <div class="flex items-center mt-2 text-sm text-gray-500">
                    <span>Dimulai: {{ $firstMessage->created_at->format('d M Y, H:i') }}</span>
                    <span class="mx-2">•</span>
                    <span>Status: 
                        @if($messages->last()->admin_response)
                            <span class="text-green-600 font-medium">Terjawab</span>
                        @else
                            <span class="text-yellow-600 font-medium">Menunggu Balasan</span>
                        @endif
                    </span>
                </div>
            </div>
            
            <!-- Message Thread -->
            <div class="bg-white shadow-sm px-6 py-6">
                <div class="space-y-6">
                    @foreach($messages as $message)
                        {{-- <!-- User Message -->
                        <div class="flex justify-start">
                            <div class="max-w-[80%]">
                                <!-- User Message Content -->
                                <div class="rounded-lg px-4 py-3 bg-gray-100 text-gray-800">
                                    <div class="text-sm">{{ $message->message }}</div>
                                </div>
                                
                                <!-- User Message Footer -->
                                <div class="mt-1 text-xs text-gray-500 flex items-center">
                                    <span>Anda</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ $message->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Admin Response (if exists) -->
                        @if($message->admin_response)
                        <div class="flex justify-end">
                            <div class="max-w-[80%]">
                                <!-- Admin Response Content -->
                                <div class="rounded-lg px-4 py-3 bg-blue-50 text-blue-800">
                                    <div class="text-sm">{{ $message->admin_response }}</div>
                                </div>
                                
                                <!-- Admin Response Footer -->
                                <div class="mt-1 text-xs text-gray-500 flex items-center justify-end">
                                    <span>Admin</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ $message->updated_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div> --}}

                        <!-- User Message -->
                        <div class="flex justify-end">
                            <div class="max-w-[80%]">
                                <!-- User Message Content -->
                                <div class="rounded-lg px-4 py-3 bg-gray-100 text-gray-800">
                                    <div class="text-sm">{{ $message->message }}</div>
                                </div>
                                
                                <!-- User Message Footer -->
                                <div class="mt-1 text-xs text-gray-500 flex items-center">
                                    <span>Anda</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ $message->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Admin Response (if exists) -->
                        @if($message->admin_response)
                        <div class="flex justify-start">
                            <div class="max-w-[80%]">
                                <!-- Admin Response Content -->
                                <div class="rounded-lg px-4 py-3 bg-blue-50 text-blue-800">
                                    <div class="text-sm">{{ $message->admin_response }}</div>
                                </div>
                                
                                <!-- Admin Response Footer -->
                                <div class="mt-1 text-xs text-gray-500 flex items-center justify-end">
                                    <span>Admin</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ $message->updated_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            
            <!-- Reply Form -->
            <div class="bg-gray-50 rounded-b-lg shadow-sm px-6 py-4 border-t border-gray-200">
                <form action="{{ route('customer-support.reply', $conversationId) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Balas Pesan</label>
                        <textarea name="message" id="message" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Ketik balasan Anda..." required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

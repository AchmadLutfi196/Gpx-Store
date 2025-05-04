@extends('profile.layout')

@section('breadcrumb')
<li>
    <div class="flex items-center">
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
        <span class="ml-1 text-gray-500 md:ml-2">Alamat Saya</span>
    </div>
</li>
@endsection

@section('title', 'Alamat Saya')

@section('profile-content')
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('addresses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Alamat Baru
        </a>
    </div>

    @if($addresses->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-6 text-center border">
            <div class="mb-4">
                <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h3 class="text-base font-medium text-gray-900 mb-2">Belum ada alamat</h3>
            <p class="text-gray-500 mb-4 text-sm">Anda belum menambahkan alamat. Tambahkan alamat baru untuk memulai.</p>
            <a href="{{ route('addresses.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-sm">
                Tambah Alamat Baru
            </a>
        </div>
    @else
        <div class="grid md:grid-cols-2 gap-4">
            @foreach($addresses as $address)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border">
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-base font-medium text-gray-900">{{ $address->name }}</h3>
                                <p class="text-gray-500 text-sm">{{ $address->phone }}</p>
                            </div>
                            @if($address->is_default)
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Default</span>
                            @endif
                        </div>
                        
                        <div class="text-gray-700 mb-3 text-sm">
                            <p>{{ $address->address_line1 }}</p>
                            @if($address->address_line2)
                                <p>{{ $address->address_line2 }}</p>
                            @endif
                            <p>{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                            <p>{{ $address->country }}</p>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('addresses.edit', $address) }}" class="text-blue-600 hover:text-blue-800 border border-blue-600 hover:border-blue-800 text-xs px-2.5 py-1 rounded-md">
                                Edit
                            </a>
                            @if(!$address->is_default)
                                <form action="{{ route('addresses.set-default', $address) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-green-600 hover:text-green-800 border border-green-600 hover:border-green-800 text-xs px-2.5 py-1 rounded-md">
                                        Jadikan Default
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline" onsubmit="return confirm('Anda yakin ingin menghapus alamat ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 border border-red-600 hover:border-red-800 text-xs px-2.5 py-1 rounded-md">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
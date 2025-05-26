@extends('layouts.app')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-100 py-4">
    <div class="container mx-auto px-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-blue-600">Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">My Account</span>
                    </div>
                </li>
                @yield('breadcrumb')
            </ol>
        </nav>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar -->
        <div class="w-full lg:w-[31%]">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 bg-gray-50 border-b">
                    <div class="flex items-center">
                        <div class="mr-4">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-16 h-16 rounded-full">
                            @else
                                <div class="flex items-center justify-center bg-blue-500 text-white text-3xl font- w-16 h-16 rounded-full">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ Auth::user()->name }}</h3>
                            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4">
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('profile.index') }}" 
                               class="block px-4 py-2 rounded-md {{ request()->routeIs('profile.index') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50 text-gray-700' }}">
                                <i class="fas fa-user mr-2"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.orders') }}" 
                               class="block px-4 py-2 rounded-md {{ request()->routeIs('profile.orders') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50 text-gray-700' }}">
                                <i class="fas fa-shopping-bag mr-2"></i> My Orders
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('wishlist') }}" 
                               class="block px-4 py-2 rounded-md {{ request()->routeIs('wishlist') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50 text-gray-700' }}">
                                <i class="fas fa-heart mr-2"></i> My Wishlist
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reviews.index') }}"
                               class="block px-4 py-2 rounded-md {{ request()->routeIs('reviews.*') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50 text-gray-700' }}">
                                <i class="fas fa-star mr-2"></i> My Reviews
                            </a>
                        </li>
                        <!-- Update your sidebar menu to include addresses -->
                        <li>
                            <a href="{{ route('profile.addresses') }}" 
                               class="block px-4 py-2 rounded-md {{ request()->routeIs('profile.addresses*') || request()->routeIs('addresses.*') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50 text-gray-700' }}">
                                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Alamat Saya
                            </a>
                        </li>
                        <li class="border-t my-2 pt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 rounded-md hover:bg-gray-50 text-gray-700">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="w-full lg:w-3/4">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            
            
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 bg-gray-50 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('title')</h2>
                </div>
                
                <div class="p-6">
                    @yield('profile-content')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
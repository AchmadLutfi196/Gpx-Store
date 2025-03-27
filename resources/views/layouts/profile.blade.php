@extends('layouts.app')

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap -mx-4">
            <!-- Sidebar -->
            <div class="w-full lg:w-1/4 px-4 mb-8 lg:mb-0">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 rounded-full overflow-hidden mr-4">
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">{{ Auth::user()->name }}</h2>
                                <p class="text-gray-600 text-sm">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="mt-1">
                            <a href="{{ route('profile.edit') }}" class="text-sm text-blue-600 hover:underline">Edit Profile</a>
                        </div>
                    </div>
                    <nav class="p-4">
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 rounded-md {{ request()->routeIs('profile.show') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                    <i class="fas fa-user mr-3 w-5 text-center"></i> My Account
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile.orders') }}" class="block px-4 py-2 rounded-md {{ request()->routeIs('profile.orders*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                    <i class="fas fa-box mr-3 w-5 text-center"></i> Orders
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile.addresses') }}" class="block px-4 py-2 rounded-md {{ request()->routeIs('profile.addresses*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                    <i class="fas fa-map-marker-alt mr-3 w-5 text-center"></i> Addresses
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile.wishlist') }}" class="block px-4 py-2 rounded-md {{ request()->routeIs('profile.wishlist') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                    <i class="fas fa-heart mr-3 w-5 text-center"></i> Wishlist
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile.password.edit') }}" class="block px-4 py-2 rounded-md {{ request()->routeIs('profile.password.edit') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                    <i class="fas fa-lock mr-3 w-5 text-center"></i> Change Password
                                </a>
                            </li>
                            <li class="border-t border-gray-200 mt-4 pt-4">
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 rounded-md text-gray-700 hover:bg-red-50 hover:text-red-700">
                                        <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i> Logout
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="w-full lg:w-3/4 px-4">
                <div class="bg-white rounded-lg shadow p-6">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @yield('profile-content')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('profile.layout')

@section('title', 'My Profile')

@section('breadcrumb')
<li aria-current="page">
    <div class="flex items-center">
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
        <span class="ml-1 text-gray-500 md:ml-2">Profile</span>
    </div>
</li>
@endsection

@section('profile-content')
<div class="flex flex-col md:flex-row">
    <div class="md:w-1/3 flex flex-col items-center">
        <div class="w-32 h-32 bg-gray-200 rounded-full overflow-hidden mb-4">
            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-blue-500 text-white text-3xl font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline text-sm flex items-center">
            <i class="fas fa-pencil-alt mr-1"></i> Edit Profile
        </a>
    </div>
    
    <div class="md:w-2/3 mt-6 md:mt-0">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Full Name</h3>
                <p class="text-base text-gray-900">{{ Auth::user()->name }}</p>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Email Address</h3>
                <p class="text-base text-gray-900">{{ Auth::user()->email }}</p>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Phone Number</h3>
                <p class="text-base text-gray-900">{{ Auth::user()->phone ?? 'Not provided' }}</p>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Member Since</h3>
                <p class="text-base text-gray-900">{{ Auth::user()->created_at->format('F d, Y') }}</p>
            </div>
        </div>
        
        <div class="mt-8">
            <h3 class="font-medium text-lg mb-4">Your Activity</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ Auth::user()->orders()->count() }}</div>
                    <div class="text-sm text-gray-600">Orders</div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ Auth::user()->productReviews()->count() }}</div>
                    <div class="text-sm text-gray-600">Reviews</div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ Auth::user()->wishlist()->count() }}</div>
                    <div class="text-sm text-gray-600">Wishlist</div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ Auth::user()->addresses()->count() }}</div>
                    <div class="text-sm text-gray-600">Addresses</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
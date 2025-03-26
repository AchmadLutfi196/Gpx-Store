@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Account Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your orders, addresses, and account settings</p>
        </div>

        <!-- Dashboard Overview -->
        <div class="mb-10 bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 mr-4">
                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-medium text-gray-900">Welcome, {{ Auth::user()->name }}</h2>
                        <p class="text-gray-600">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600">Account created at: {{ Auth::user()->created_at->format('F d, Y') }}</p>
            </div>
        </div>

        <!-- Dashboard Sections -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Orders Section -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">My Orders</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">0 Orders</span>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">View and track your recent orders</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm inline-flex items-center">
                        View All Orders
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Addresses Section -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">My Addresses</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">0 Addresses</span>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Manage your shipping addresses</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm inline-flex items-center">
                        Manage Addresses
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Wishlist Section -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">My Wishlist</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">0 Items</span>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Products you've saved for later</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm inline-flex items-center">
                        View Wishlist
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Account Management -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Account Management</h2>
            
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Update Profile Information</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Update your account's profile information and email address.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm inline-flex items-center">
                        Edit Profile
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Ensure your account is using a long, random password to stay secure.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm inline-flex items-center">
                        Change Password
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Logout Form -->
            <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Logout</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Sign out of your account.</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
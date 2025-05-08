@extends('layouts.app')

@section('styles')
<style>
    .form-input {
        @apply w-full px-4 py-3 rounded-lg bg-gray-100 border-gray-200 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200;
    }
    
    .auth-container {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='36' height='72' viewBox='0 0 36 72'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23b2c5f8' fill-opacity='0.15'%3E%3Cpath d='M2 6h12L8 18 2 6zm18 36h12l-6 12-6-12z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    
    .animated-bg {
        background: linear-gradient(-45deg, #C7D2FE, #A5B4FC, #818CF8, #6366F1);
        background-size: 400% 400%;
        animation: gradient 15s ease infinite;
    }

    @keyframes gradient {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }
</style>
@endsection

@section('content')
<div class="flex min-h-screen bg-gray-50 auth-container">
    <!-- Left Side with Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Welcome Back</h1>
                <p class="text-gray-600 mt-2">Sign in to your GPX Store account</p>
            </div>
            
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6">
                    <div class="font-medium">Whoops! There was an error:</div>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Social Login -->
            <div class="space-y-4 mb-8">
                <a href="{{ url('auth/google') }}" class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white hover:bg-gray-50 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                    </svg>
                    Sign in with Google
                </a>
                
                <a href="{{ url('auth/github') }}" class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-800 hover:bg-gray-700 font-medium text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.87 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z"/>
                    </svg>
                    Sign in with GitHub
                </a>
            </div>
            
            <!-- Divider -->
            <div class="flex items-center justify-center my-8">
                <div class="border-t border-gray-300 flex-grow"></div>
                <div class="mx-4 text-sm text-gray-500 uppercase">or sign in with email</div>
                <div class="border-t border-gray-300 flex-grow"></div>
            </div>
            
            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="form-input @error('email') border-red-500 @enderror w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           value="{{ old('email') }}" 
                           placeholder="name@example.com">
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-500 hover:underline">
                            Forgot password?
                        </a>
                    </div>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="form-input @error('password') border-red-500 @enderror w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Your password">
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-600">
                            <svg class="w-5 h-5" id="eye-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                            </svg>
                            <svg class="w-5 h-5 hidden" id="eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="w-full py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        Sign In
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 hover:underline">
                        Register now
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Right Side with Illustration -->
    <div class="hidden lg:flex lg:w-1/2 animated-bg items-center justify-center p-12">
        <div class="max-w-lg text-center">
            <svg class="h-64 w-auto text-white mx-auto opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            
            <h1 class="text-4xl font-bold text-white mb-6 mt-8">Welcome Back!</h1>
            <p class="text-lg text-indigo-100">Sign in to access your account dashboard, track your orders, and manage your profile.</p>
            
            <div class="grid grid-cols-2 gap-6 mt-10">
                <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm rounded-xl p-6 text-left">
                    <div class="rounded-full bg-indigo-600 w-12 h-12 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-white text-lg mb-1">Track Orders</h3>
                    <p class="text-indigo-100">Check the status and details of your orders</p>
                </div>
                
                <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm rounded-xl p-6 text-left">
                    <div class="rounded-full bg-indigo-600 w-12 h-12 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-white text-lg mb-1">Member Benefits</h3>
                    <p class="text-indigo-100">Exclusive discounts and rewards for members</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('toggle-password');
    // if we're on a page without that button, bail out
    if (!togglePassword) return;

    const passwordInput = document.getElementById('password');
    const eyeClosed     = document.getElementById('eye-closed');
    const eyeOpen       = document.getElementById('eye-open');

    togglePassword.addEventListener('click', function() {
        const isPwd = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPwd ? 'text' : 'password');
        eyeClosed.classList.toggle('hidden');
        eyeOpen.classList.toggle('hidden');
    });
    });
</script>
@endsection
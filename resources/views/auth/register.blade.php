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
    
    .step-indicator {
        @apply w-8 h-8 rounded-full flex items-center justify-center;
    }
    
    .step-active {
        @apply bg-indigo-600 text-white;
    }
    
    .step-inactive {
        @apply bg-gray-200 text-gray-500;
    }
    
    .password-strength-meter {
        height: 4px;
        background-color: #ddd;
        border-radius: 2px;
        position: relative;
        overflow: hidden;
    }

    .password-strength-meter::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 0;
        transition: width 0.3s ease;
    }

    .password-strength-meter.weak::before {
        background-color: #f87171;
        width: 25%;
    }

    .password-strength-meter.medium::before {
        background-color: #fbbf24;
        width: 50%;
    }

    .password-strength-meter.good::before {
        background-color: #60a5fa;
        width: 75%;
    }

    .password-strength-meter.strong::before {
        background-color: #34d399;
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="flex min-h-screen bg-gray-50 auth-container">
    <!-- Left Side with Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="text-center mb-8" data-aos="fade-up">
                <h1 class="text-3xl font-bold text-gray-900">Create an Account</h1>
                <p class="text-gray-600 mt-2">Join GPX Store for a better shopping experience</p>
            </div>
            
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6" data-aos="fade-up">
                    <div class="font-medium">Oops! There are some issues:</div>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Registration Progress Steps -->
            <div class="flex justify-between items-center mb-8" data-aos="fade-up" data-aos-delay="150">
                <div class="flex flex-col items-center">
                    <div class="step-indicator step-active">
                        <span>1</span>
                    </div>
                    <span class="text-xs mt-1 text-gray-700">Account</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-indicator step-inactive">
                        <span>2</span>
                    </div>
                    <span class="text-xs mt-1 text-gray-500">Profile</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-indicator step-inactive">
                        <span>3</span>
                    </div>
                    <span class="text-xs mt-1 text-gray-500">Complete</span>
                </div>
            </div>
            
            <!-- Social Registration -->
            <div class="space-y-4 mb-8" data-aos="fade-up" data-aos-delay="300">
                <a href="{{ url('auth/google') }}" class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white hover:bg-gray-50 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                    </svg>
                    Register with Google
                </a>
                
                <button type="button" class="flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-lg shadow-sm bg-blue-600 hover:bg-blue-700 font-medium text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 2.04C6.5 2.04 2 6.53 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.85C10.44 7.34 11.93 5.96 14.22 5.96C15.31 5.96 16.45 6.15 16.45 6.15V8.62H15.19C13.95 8.62 13.56 9.39 13.56 10.18V12.06H16.34L15.89 14.96H13.56V21.96A10 10 0 0 0 22 12.06C22 6.53 17.5 2.04 12 2.04Z"/>
                    </svg>
                    Register with Facebook
                </button>
            </div>
            
            <!-- Divider -->
            <div class="flex items-center justify-center my-8" data-aos="fade-up" data-aos-delay="450">
                <div class="border-t border-gray-300 flex-grow"></div>
                <div class="mx-4 text-sm text-gray-500 uppercase">or register with email</div>
                <div class="border-t border-gray-300 flex-grow"></div>
            </div>
            
            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-6" data-aos="fade-up" data-aos-delay="600">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input id="name" name="name" type="text" autocomplete="name" required 
                           class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name') }}" 
                           placeholder="Enter your full name">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="form-input @error('email') border-red-500 @enderror" 
                           value="{{ old('email') }}" 
                           placeholder="name@example.com">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500">+62</span>
                        </div>
                        <input id="phone" name="phone" type="text" autocomplete="tel" required 
                               class="form-input pl-12 @error('phone') border-red-500 @enderror" 
                               value="{{ old('phone') }}" 
                               placeholder="812XXXXXXXX">
                    </div>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="new-password" required 
                               class="form-input @error('password') border-red-500 @enderror" 
                               placeholder="Minimum 8 characters">
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
                    <div class="mt-2">
                        <div class="password-strength-meter" id="password-strength-meter"></div>
                        <p class="text-xs text-gray-500 mt-1" id="password-strength-text">Password must be at least 8 characters</p>
                    </div>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                               class="form-input @error('password_confirmation') border-red-500 @enderror" 
                               placeholder="Repeat password">
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-gray-700">
                            I agree to the 
                            <a href="#" class="text-blue-600 hover:text-blue-500 hover:underline">Terms & Conditions</a> 
                            and 
                            <a href="#" class="text-blue-600 hover:text-blue-500 hover:underline">Privacy Policy</a>
                        </label>
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="w-full py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        Register Now
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center" data-aos="fade-up" data-aos-delay="750">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 hover:underline">
                        Login now
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Right Side with Illustration -->
    <div class="hidden lg:flex lg:w-1/2 animated-bg items-center justify-center p-12">
        <div class="max-w-lg text-center">
            <div data-aos="fade-left" class="mb-8">
                <svg class="h-64 w-auto text-white mx-auto opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            
            <div data-aos="fade-up" data-aos-delay="300">
                <h1 class="text-4xl font-bold text-white mb-6">Join Us Today!</h1>
                <p class="text-lg text-indigo-100">Register as a GPX Store member and enjoy exclusive benefits.</p>
                
                <div class="grid grid-cols-2 gap-6 mt-10">
                    <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm rounded-xl p-6 text-left">
                        <div class="rounded-full bg-indigo-600 w-12 h-12 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-white text-lg mb-1">Reward Points</h3>
                        <p class="text-indigo-100">Earn points with every purchase that can be redeemed for discounts</p>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm rounded-xl p-6 text-left">
                        <div class="rounded-full bg-indigo-600 w-12 h-12 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-white text-lg mb-1">Exclusive Discounts</h3>
                        <p class="text-indigo-100">Special prices and promotions only for members</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeClosed = document.getElementById('eye-closed');
        const eyeOpen = document.getElementById('eye-open');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle eye icons
            eyeClosed.classList.toggle('hidden');
            eyeOpen.classList.toggle('hidden');
        });
        
        // Password strength meter
        const passwordStrengthMeter = document.getElementById('password-strength-meter');
        const passwordStrengthText = document.getElementById('password-strength-text');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = measurePasswordStrength(password);
            
            // Update password strength meter
            passwordStrengthMeter.className = 'password-strength-meter';
            
            if (password.length === 0) {
                passwordStrengthText.textContent = 'Password must be at least 8 characters';
                return;
            }
            
            if (strength < 30) {
                passwordStrengthMeter.classList.add('weak');
                passwordStrengthText.textContent = 'Weak: Use a combination of letters, numbers, and symbols';
                passwordStrengthText.className = 'text-xs text-red-600 mt-1';
            } else if (strength < 60) {
                passwordStrengthMeter.classList.add('medium');
                passwordStrengthText.textContent = 'Medium: Add more characters';
                passwordStrengthText.className = 'text-xs text-yellow-600 mt-1';
            } else if (strength < 80) {
                passwordStrengthMeter.classList.add('good');
                passwordStrengthText.textContent = 'Good: Password is sufficient';
                passwordStrengthText.className = 'text-xs text-blue-600 mt-1';
            } else {
                passwordStrengthMeter.classList.add('strong');
                passwordStrengthText.textContent = 'Strong: Excellent password';
                passwordStrengthText.className = 'text-xs text-green-600 mt-1';
            }
        });
        
        // Simple password strength measurement
        function measurePasswordStrength(password) {
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 20;
            if (password.length >= 12) strength += 20;
            
            // Character type check
            if (/[a-z]/.test(password)) strength += 10;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^a-zA-Z0-9]/.test(password)) strength += 30;
            
            // Return a number from 0 to 100
            return Math.min(100, strength);
        }
        
        // Phone number formatter
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function(e) {
            // Remove non-numeric characters
            let value = this.value.replace(/\D/g, '');
            
            // Remove leading zero if exists (since we're adding the +62 prefix)
            if (value.startsWith('0')) {
                value = value.substring(1);
            }
            
            // Format the number
            this.value = value;
        });
    });
</script>
@endsection
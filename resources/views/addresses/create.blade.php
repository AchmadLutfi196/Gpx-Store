@extends('profile.layout')

@section('breadcrumb')
<li>
    <div class="flex items-center">
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
        <a href="{{ route('profile.addresses') }}" class="ml-1 text-gray-500 md:ml-2 hover:text-blue-600">Addresses</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
        <span class="ml-1 text-gray-500 md:ml-2">Add New Address</span>
    </div>
</li>
@endsection

@section('title', 'Add New Address')

@section('profile-content')
<form action="{{ route('addresses.store') }}" method="POST">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Full Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                   required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone Number -->
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                   required>
            @error('phone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address Line 1 -->
        <div class="md:col-span-2">
            <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
            <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1') }}" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                   required>
            @error('address_line1')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address Line 2 -->
        <div class="md:col-span-2">
            <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
            <input type="text" name="address_line2" id="address_line2" value="{{ old('address_line2') }}" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            @error('address_line2')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- City -->
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
            <input type="text" name="city" id="city" value="{{ old('city') }}" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                   required>
            @error('city')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Postal Code -->
        <div>
            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                   required>
            @error('postal_code')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Province -->
        <div>
            <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Province</label>
            <select name="province" id="province" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required>
                <option value="">Select Province</option>
                @foreach($provinces as $province)
                    <option value="{{ $province }}" {{ old('province') == $province ? 'selected' : '' }}>
                        {{ $province }}
                    </option>
                @endforeach
            </select>
            @error('province')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Set as Default -->
        <div class="md:col-span-2 mt-2">
            <div class="flex items-center">
                <input type="checkbox" name="set_as_default" id="set_as_default" 
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                       {{ old('set_as_default') ? 'checked' : '' }}>
                <label for="set_as_default" class="ml-2 block text-sm text-gray-700">
                    Set as default address
                </label>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('profile.addresses') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
            Cancel
        </a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Save Address
        </button>
    </div>
</form>
@endsection

@section('scripts')
@if(session('sweetAlert'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: "{{ session('sweetAlert.title') }}",
            text: "{{ session('sweetAlert.text') }}",
            icon: "{{ session('sweetAlert.icon') }}",
            confirmButtonText: "{{ session('sweetAlert.confirmButtonText') }}",
            confirmButtonColor: '#3085d6'
        });
    });
</script>
@endif
@endsection
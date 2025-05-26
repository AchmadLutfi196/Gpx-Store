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
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a 1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
        <span class="ml-1 text-gray-500 md:ml-2">Add New Address</span>
    </div>
</li>
@endsection

@section('title', 'Add New Address')

@section('profile-content')
<form action="{{ route('addresses.store') }}" method="POST">
    @csrf

    <!-- Hidden fields for form submission -->
    <input type="hidden" name="name" id="hidden_name" value="{{ old('name') }}">
    <input type="hidden" name="phone" id="hidden_phone" value="{{ old('phone') }}">
    <input type="hidden" name="address_line1" id="hidden_address_line1" value="{{ old('address_line1') }}">
    
    <!-- Hidden fields for RajaOngkir integration -->
    <input type="hidden" name="city_id" id="city_id" value="{{ old('city_id') }}">
    <input type="hidden" name="province_id" id="province_id" value="{{ old('province_id') }}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Full Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-600">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required onchange="document.getElementById('hidden_name').value = this.value">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone Number -->
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-600">*</span></label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required onchange="document.getElementById('hidden_phone').value = this.value">
            @error('phone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address Line 1 -->
        <div class="md:col-span-2">
            <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 <span class="text-red-600">*</span></label>
            <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required onchange="document.getElementById('hidden_address_line1').value = this.value">
            @error('address_line1')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address Line 2 -->
        <div class="md:col-span-2">
            <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
            <input type="text" name="address_line2" id="address_line2" value="{{ old('address_line2') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('address_line2')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Province Selection with RajaOngkir -->
        <div>
            <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Province <span class="text-red-600">*</span></label>
            <select name="province" id="province" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                <option value="">Select Province</option>
                <!-- Will be populated via API -->
            </select>
            @error('province')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- City Selection with RajaOngkir -->
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-600">*</span></label>
            <select name="city" id="city" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required disabled>
                <option value="">Select Province First</option>
            </select>
            @error('city')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Postal Code -->
        <div>
            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code <span class="text-red-600">*</span></label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
            @error('postal_code')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Country (Default to Indonesia) -->
        <div>
            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country <span class="text-red-600">*</span></label>
            <input type="text" name="country" id="country" value="{{ old('country', 'Indonesia') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
            @error('country')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Set as Default -->
        <div class="md:col-span-2 mt-2">
            <div class="flex items-center">
                <input type="checkbox" name="is_default" id="is_default" value="1"
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                       {{ old('is_default') ? 'checked' : '' }}>
                <label for="is_default" class="ml-2 block text-sm text-gray-700">
                    Set as default address
                </label>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('addresses.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
            Cancel
        </a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Save Address
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const provinceIdInput = document.getElementById('province_id');
    const cityIdInput = document.getElementById('city_id');

    // Load provinces from RajaOngkir API
    loadProvinces();

    // Event listener for province selection
    provinceSelect.addEventListener('change', function() {
        const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
        const provinceId = selectedOption.dataset.id;
        const provinceName = selectedOption.value;

        if (provinceId && provinceName) {
            provinceIdInput.value = provinceId;
            loadCities(provinceId);
        } else {
            citySelect.innerHTML = '<option value="">Select Province First</option>';
            citySelect.disabled = true;
            cityIdInput.value = '';
        }
    });

    // Event listener for city selection
    citySelect.addEventListener('change', function() {
        const selectedOption = citySelect.options[citySelect.selectedIndex];
        const cityId = selectedOption.dataset.id;
        
        if (cityId) {
            cityIdInput.value = cityId;
        }
    });

    // Form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Update hidden fields before submission
        document.getElementById('hidden_name').value = document.getElementById('name').value;
        document.getElementById('hidden_phone').value = document.getElementById('phone').value;
        document.getElementById('hidden_address_line1').value = document.getElementById('address_line1').value;

        // Validate that province and city have been selected
        if (!provinceIdInput.value || !cityIdInput.value) {
            e.preventDefault();
            alert('Please select both province and city');
            return false;
        }
    });

    // Function to load provinces
    function loadProvinces() {
        fetch('/api/rajaongkir/provinces')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    provinceSelect.innerHTML = '<option value="">Select Province</option>';
                    
                    data.data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.province;
                        option.textContent = province.province;
                        option.dataset.id = province.province_id;
                        provinceSelect.appendChild(option);
                    });
                } else {
                    console.error('Failed to load provinces:', data.message || 'Unknown error');
                    provinceSelect.innerHTML = '<option value="">Error loading provinces</option>';
                }
            })
            .catch(error => {
                console.error('Error loading provinces:', error);
                provinceSelect.innerHTML = '<option value="">Error loading provinces</option>';
            });
    }

    // Function to load cities for selected province
    function loadCities(provinceId) {
        citySelect.innerHTML = '<option value="">Loading cities...</option>';
        citySelect.disabled = true;

        fetch(`/api/rajaongkir/cities/${provinceId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    citySelect.innerHTML = '<option value="">Select City</option>';
                    
                    data.data.forEach(city => {
                        const option = document.createElement('option');
                        // Format city name as "Type CityName" e.g. "Kota Jakarta Pusat"
                        option.value = `${city.type} ${city.city_name}`;
                        option.textContent = `${city.type} ${city.city_name}`;
                        option.dataset.id = city.city_id;
                        citySelect.appendChild(option);
                    });
                    
                    citySelect.disabled = false;
                } else {
                    console.error('Failed to load cities:', data.message || 'Unknown error');
                    citySelect.innerHTML = '<option value="">Error loading cities</option>';
                }
            })
            .catch(error => {
                console.error('Error loading cities:', error);
                citySelect.innerHTML = '<option value="">Error loading cities</option>';
            });
    }
});
</script>

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
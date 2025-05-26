<div class="bg-white shadow rounded-lg p-6">
    <form method="POST" action="{{ isset($address) ? route('addresses.update', $address) : route('addresses.store') }}" class="space-y-6">
        @csrf
        @if(isset($address))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $address->name ?? '') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Field -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $address->phone ?? '') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address Line 1 Field -->
            <div class="md:col-span-2">
                <label for="address_line1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1', $address->address_line1 ?? '') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                @error('address_line1')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address Line 2 Field -->
            <div class="md:col-span-2">
                <label for="address_line2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                <input type="text" name="address_line2" id="address_line2" value="{{ old('address_line2', $address->address_line2 ?? '') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('address_line2')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Province Selection -->
            <div>
                <label for="province" class="block text-sm font-medium text-gray-700">Province</label>
                <select id="province" name="province" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    <option value="">Select Province</option>
                </select>
                <input type="hidden" name="province_id" id="province_id" value="{{ old('province_id', $address->province_id ?? '') }}">
                @error('province')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- City Selection -->
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                <select id="city" name="city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required disabled>
                    <option value="">Select City</option>
                </select>
                <input type="hidden" name="city_id" id="city_id" value="{{ old('city_id', $address->city_id ?? '') }}">
                @error('city')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Postal Code Field -->
            <div>
                <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                @error('postal_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Country Field -->
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                <input type="text" name="country" id="country" value="{{ old('country', $address->country ?? 'Indonesia') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                @error('country')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Default Address Checkbox -->
            <div class="md:col-span-2">
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1" 
                           {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_default" class="ml-2 block text-sm text-gray-900">Set as default address</label>
                </div>
                @error('is_default')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('profile.addresses') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                {{ isset($address) ? 'Update Address' : 'Save Address' }}
            </button>
        </div>
    </form>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const provinceIdInput = document.getElementById('province_id');
        const cityIdInput = document.getElementById('city_id');
        
        // Current values (for editing)
        const currentProvinceId = '{{ old('province_id', $address->province_id ?? '') }}';
        const currentProvince = '{{ old('province', $address->province ?? '') }}';
        const currentCityId = '{{ old('city_id', $address->city_id ?? '') }}';
        const currentCity = '{{ old('city', $address->city ?? '') }}';
        
        // Load provinces
        fetch('/api/rajaongkir/provinces')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    provinceSelect.innerHTML = '<option value="">Select Province</option>';
                    
                    data.data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.province;
                        option.dataset.id = province.province_id;
                        option.textContent = province.province;
                        option.selected = province.province === currentProvince || province.province_id === currentProvinceId;
                        provinceSelect.appendChild(option);
                    });
                    
                    // If we have a province selected, load its cities
                    if (provinceSelect.value) {
                        const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
                        const provinceId = selectedOption.dataset.id;
                        provinceIdInput.value = provinceId;
                        loadCities(provinceId);
                    }
                } else {
                    console.error('Failed to load provinces:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading provinces:', error);
            });
        
        // Province change event
        provinceSelect.addEventListener('change', function() {
            const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
            if (selectedOption.value) {
                const provinceId = selectedOption.dataset.id;
                provinceIdInput.value = provinceId;
                loadCities(provinceId);
            } else {
                citySelect.innerHTML = '<option value="">Select City</option>';
                citySelect.disabled = true;
                cityIdInput.value = '';
            }
        });
        
        // City change event
        citySelect.addEventListener('change', function() {
            const selectedOption = citySelect.options[citySelect.selectedIndex];
            if (selectedOption.value) {
                cityIdInput.value = selectedOption.dataset.id;
            } else {
                cityIdInput.value = '';
            }
        });
        
        // Function to load cities based on province
        function loadCities(provinceId) {
            citySelect.disabled = true;
            citySelect.innerHTML = '<option value="">Loading cities...</option>';
            
            fetch(`/api/rajaongkir/cities/${provinceId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        
                        data.data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = `${city.type} ${city.city_name}`;
                            option.dataset.id = city.city_id;
                            option.textContent = `${city.type} ${city.city_name}`;
                            option.selected = option.value === currentCity || city.city_id === currentCityId;
                            citySelect.appendChild(option);
                        });
                        
                        citySelect.disabled = false;
                        
                        // If we have a city selected, set the city_id
                        if (citySelect.value) {
                            const selectedOption = citySelect.options[citySelect.selectedIndex];
                            cityIdInput.value = selectedOption.dataset.id;
                        }
                    } else {
                        citySelect.innerHTML = '<option value="">Error loading cities</option>';
                        console.error('Failed to load cities:', data.message);
                    }
                })
                .catch(error => {
                    citySelect.innerHTML = '<option value="">Error loading cities</option>';
                    console.error('Error loading cities:', error);
                });
        }
    });
</script>
@endsection

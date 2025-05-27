<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;
    protected $isSandbox;
    protected $useStaticData = true; // Force to use static data by default

    public function __construct()
    {
        // Use the KOMERCE API key if configured
        $this->apiKey = config('services.rajaongkir.key', env('KOMERCE_API_KEY', 'AqYBdJ5945321a6d0fc32a2dZMkqredM'));
        $this->isSandbox = config('services.rajaongkir.sandbox', true);
        
        // Always use static data regardless of config settings
        // This ensures the application works even without API access
        $this->useStaticData = true; 
        
        // Use config service values with fallback to env
        if ($this->isSandbox) {
            $this->baseUrl = config('services.rajaongkir.sandbox_url', 'https://api.sandbox.rajaongkir.com/starter');
        } else {
            $this->baseUrl = config('services.rajaongkir.url', 'https://api.rajaongkir.com/starter');
        }
        
        Log::debug('RajaOngkirService initialized', [
            'baseUrl' => $this->baseUrl,
            'isSandbox' => $this->isSandbox,
            'keyExists' => !empty($this->apiKey),
            'useStaticData' => $this->useStaticData
        ]);
    }

    public function getProvinces()
    {
        // If static data is enabled, return hardcoded data
        if ($this->useStaticData) {
            Log::info('Using static province data');
            return $this->getStaticProvinces();
        }
        
        try {
            Log::debug('Fetching provinces from RajaOngkir', ['baseUrl' => $this->baseUrl]);
            
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($this->baseUrl . '/province');

            if ($response->successful()) {
                $data = $response->json();
                Log::debug('RajaOngkir provinces response', ['status' => $data['rajaongkir']['status'] ?? 'unknown']);
                return $data['rajaongkir']['results'] ?? [];
            } else {
                Log::error('RajaOngkir provinces error', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Exception in getProvinces', ['message' => $e->getMessage()]);
            
            // Fallback to static data if API call fails
            Log::info('Falling back to static province data');
            return $this->getStaticProvinces();
        }

        return [];
    }

    public function getCities($provinceId = null)
    {
        // If static data is enabled, return hardcoded data
        if ($this->useStaticData) {
            Log::info('Using static city data for province: ' . $provinceId);
            return $this->getStaticCities($provinceId);
        }
        
        try {
            $url = $this->baseUrl . '/city';
            $params = [];
            
            if ($provinceId) {
                $params['province'] = $provinceId;
            }

            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                return $data['rajaongkir']['results'] ?? [];
            } else {
                Log::error('RajaOngkir cities error', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Exception in getCities', ['message' => $e->getMessage()]);
            
            // Fallback to static data if API call fails
            Log::info('Falling back to static city data');
            return $this->getStaticCities($provinceId);
        }

        return [];
    }

    public function getShippingCost($origin, $destination, $weight, $courier)
    {
        // If static data is enabled, return hardcoded shipping cost data
        if ($this->useStaticData) {
            Log::info('Using static shipping cost data');
            return $this->getStaticShippingCost($origin, $destination, $weight, $courier);
        }
        
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->post($this->baseUrl . '/cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['rajaongkir']['results'] ?? [];
            } else {
                Log::error('RajaOngkir shipping cost error', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Exception in getShippingCost', ['message' => $e->getMessage()]);
            
            // Fallback to static data if API call fails
            Log::info('Falling back to static shipping cost data');
            return $this->getStaticShippingCost($origin, $destination, $weight, $courier);
        }

        return [];
    }

    // Helper method to check if the API is accessible
    public function checkConnection()
    {
        if ($this->useStaticData) {
            return [
                'success' => true,
                'status' => 200,
                'message' => 'Using static data (offline mode)',
            ];
        }
        
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($this->baseUrl . '/province');
            
            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'message' => $response->successful() ? 'Connection successful' : 'Connection failed: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 0,
                'message' => 'Exception: ' . $e->getMessage(),
                'data' => 'Fallback to static data available',
            ];
        }
    }

    // Provide static data for offline/testing use
    protected function getStaticProvinces()
    {
        return [
            ['province_id' => '1', 'province' => 'Bali'],
            ['province_id' => '2', 'province' => 'Bangka Belitung'],
            ['province_id' => '3', 'province' => 'Banten'],
            ['province_id' => '4', 'province' => 'Bengkulu'],
            ['province_id' => '5', 'province' => 'DI Yogyakarta'],
            ['province_id' => '6', 'province' => 'DKI Jakarta'],
            ['province_id' => '7', 'province' => 'Gorontalo'],
            ['province_id' => '8', 'province' => 'Jambi'],
            ['province_id' => '9', 'province' => 'Jawa Barat'],
            ['province_id' => '10', 'province' => 'Jawa Tengah'],
            ['province_id' => '11', 'province' => 'Jawa Timur'],
            ['province_id' => '12', 'province' => 'Kalimantan Barat'],
            ['province_id' => '13', 'province' => 'Kalimantan Selatan'],
            ['province_id' => '14', 'province' => 'Kalimantan Tengah'],
            ['province_id' => '15', 'province' => 'Kalimantan Timur'],
            ['province_id' => '16', 'province' => 'Kalimantan Utara'],
            ['province_id' => '17', 'province' => 'Kepulauan Riau'],
            ['province_id' => '18', 'province' => 'Lampung'],
            ['province_id' => '19', 'province' => 'Maluku'],
            ['province_id' => '20', 'province' => 'Maluku Utara'],
            ['province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)'],
            ['province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)'],
            ['province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)'],
            ['province_id' => '24', 'province' => 'Papua'],
            ['province_id' => '25', 'province' => 'Papua Barat'],
            ['province_id' => '26', 'province' => 'Riau'],
            ['province_id' => '27', 'province' => 'Sulawesi Barat'],
            ['province_id' => '28', 'province' => 'Sulawesi Selatan'],
            ['province_id' => '29', 'province' => 'Sulawesi Tengah'],
            ['province_id' => '30', 'province' => 'Sulawesi Tenggara'],
            ['province_id' => '31', 'province' => 'Sulawesi Utara'],
            ['province_id' => '32', 'province' => 'Sumatera Barat'],
            ['province_id' => '33', 'province' => 'Sumatera Selatan'],
            ['province_id' => '34', 'province' => 'Sumatera Utara'],
        ];
    }

    protected function getStaticCities($provinceId = null)
    {
        $cities = [
        // Bali
        '1' => [
            ['city_id' => '17', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kabupaten', 'city_name' => 'Badung', 'postal_code' => '80351'],
            ['city_id' => '32', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kabupaten', 'city_name' => 'Bangli', 'postal_code' => '80619'],
            ['city_id' => '94', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kabupaten', 'city_name' => 'Buleleng', 'postal_code' => '81111'],
            ['city_id' => '114', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kota', 'city_name' => 'Denpasar', 'postal_code' => '80227'],
            ['city_id' => '128', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kabupaten', 'city_name' => 'Gianyar', 'postal_code' => '80519'],
            ['city_id' => '161', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kabupaten', 'city_name' => 'Jembrana', 'postal_code' => '82251'],
            ['city_id' => '170', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kabupaten', 'city_name' => 'Karangasem', 'postal_code' => '80819'],
            ['city_id' => '197', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kabupaten', 'city_name' => 'Klungkung', 'postal_code' => '80719'],
            ['city_id' => '447', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kabupaten', 'city_name' => 'Tabanan', 'postal_code' => '82119']
        ],
        // Bangka Belitung
        '2' => [
            ['city_id' => '29', 'province_id' => '2', 'province' => 'Bangka Belitung', 'type' => 'Kabupaten', 'city_name' => 'Bangka', 'postal_code' => '33212'],
            ['city_id' => '30', 'province_id' => '2', 'province' => 'Bangka Belitung', 'type' => 'Kabupaten', 'city_name' => 'Bangka Barat', 'postal_code' => '33315'],
            ['city_id' => '31', 'province_id' => '2', 'province' => 'Bangka Belitung', 'type' => 'Kabupaten', 'city_name' => 'Bangka Selatan', 'postal_code' => '33719'],
            ['city_id' => '32', 'province_id' => '2', 'province' => 'Bangka Belitung', 'type' => 'Kabupaten', 'city_name' => 'Bangka Tengah', 'postal_code' => '33613'],
            ['city_id' => '50', 'province_id' => '2', 'province' => 'Bangka Belitung', 'type' => 'Kabupaten', 'city_name' => 'Belitung', 'postal_code' => '33419'],
            ['city_id' => '51', 'province_id' => '2', 'province' => 'Bangka Belitung', 'type' => 'Kabupaten', 'city_name' => 'Belitung Timur', 'postal_code' => '33519'],
            ['city_id' => '334', 'province_id' => '2', 'province' => 'Bangka Belitung', 'type' => 'Kota', 'city_name' => 'Pangkal Pinang', 'postal_code' => '33115']
        ],
        // Banten
        '3' => [
            ['city_id' => '106', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kota', 'city_name' => 'Cilegon', 'postal_code' => '42417'],
            ['city_id' => '232', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kabupaten', 'city_name' => 'Lebak', 'postal_code' => '42319'],
            ['city_id' => '331', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kabupaten', 'city_name' => 'Pandeglang', 'postal_code' => '42212'],
            ['city_id' => '402', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kabupaten', 'city_name' => 'Serang', 'postal_code' => '42182'],
            ['city_id' => '403', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kota', 'city_name' => 'Serang', 'postal_code' => '42111'],
            ['city_id' => '455', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kabupaten', 'city_name' => 'Tangerang', 'postal_code' => '15914'],
            ['city_id' => '456', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kota', 'city_name' => 'Tangerang', 'postal_code' => '15111'],
            ['city_id' => '457', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kota', 'city_name' => 'Tangerang Selatan', 'postal_code' => '15332']
        ],
        // Bengkulu
        '4' => [
            ['city_id' => '42', 'province_id' => '4', 'province' => 'Bengkulu', 'type' => 'Kabupaten', 'city_name' => 'Bengkulu Selatan', 'postal_code' => '38519'],
            ['city_id' => '43', 'province_id' => '4', 'province' => 'Bengkulu', 'type' => 'Kabupaten', 'city_name' => 'Bengkulu Tengah', 'postal_code' => '38319'],
            ['city_id' => '44', 'province_id' => '4', 'province' => 'Bengkulu', 'type' => 'Kabupaten', 'city_name' => 'Bengkulu Utara', 'postal_code' => '38619'],
            ['city_id' => '46', 'province_id' => '4', 'province' => 'Bengkulu', 'type' => 'Kota', 'city_name' => 'Bengkulu', 'postal_code' => '38229'],
            ['city_id' => '175', 'province_id' => '4', 'province' => 'Bengkulu', 'type' => 'Kabupaten', 'city_name' => 'Kaur', 'postal_code' => '38911'],
            ['city_id' => '183', 'province_id' => '4', 'province' => 'Bengkulu', 'type' => 'Kabupaten', 'city_name' => 'Kepahiang', 'postal_code' => '39319'],
            ['city_id' => '233', 'province_id' => '4', 'province' => 'Bengkulu', 'type' => 'Kabupaten', 'city_name' => 'Lebong', 'postal_code' => '39264'],
            ['city_id' => '294', 'province_id' => '4', 'province' => 'Bengkulu', 'type' => 'Kabupaten', 'city_name' => 'Mukomuko', 'postal_code' => '38365'],
            ['city_id' => '379', 'province_id' => '4', 'province' => 'Bengkulu', 'type' => 'Kabupaten', 'city_name' => 'Rejang Lebong', 'postal_code' => '39112'],
            ['city_id' => '397', 'province_id' => '4', 'province' => 'Bengkulu', 'type' => 'Kabupaten', 'city_name' => 'Seluma', 'postal_code' => '38363']
        ],
        // DI Yogyakarta
        '5' => [
            ['city_id' => '39', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten', 'city_name' => 'Bantul', 'postal_code' => '55715'],
            ['city_id' => '135', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten', 'city_name' => 'Gunung Kidul', 'postal_code' => '55812'],
            ['city_id' => '210', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten', 'city_name' => 'Kulon Progo', 'postal_code' => '55611'],
            ['city_id' => '419', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten', 'city_name' => 'Sleman', 'postal_code' => '55513'],
            ['city_id' => '501', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kota', 'city_name' => 'Yogyakarta', 'postal_code' => '55111']
        ],    
        // DKI Jakarta
        '6' => [
            ['city_id' => '152', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Pusat', 'postal_code' => '10540'],
            ['city_id' => '153', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Barat', 'postal_code' => '11220'],
            ['city_id' => '154', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Selatan', 'postal_code' => '12230'],
            ['city_id' => '155', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Timur', 'postal_code' => '13330'],
            ['city_id' => '156', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Utara', 'postal_code' => '14140'],
            ['city_id' => '189', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Seribu', 'postal_code' => '14550']
        ],
        // Gorontalo
        '7' => [
            ['city_id' => '129', 'province_id' => '7', 'province' => 'Gorontalo', 'type' => 'Kabupaten', 'city_name' => 'Boalemo', 'postal_code' => '96319'],
            ['city_id' => '130', 'province_id' => '7', 'province' => 'Gorontalo', 'type' => 'Kabupaten', 'city_name' => 'Bone Bolango', 'postal_code' => '96511'],
            ['city_id' => '131', 'province_id' => '7', 'province' => 'Gorontalo', 'type' => 'Kabupaten', 'city_name' => 'Gorontalo', 'postal_code' => '96218'],
            ['city_id' => '132', 'province_id' => '7', 'province' => 'Gorontalo', 'type' => 'Kota', 'city_name' => 'Gorontalo', 'postal_code' => '96115'],
            ['city_id' => '133', 'province_id' => '7', 'province' => 'Gorontalo', 'type' => 'Kabupaten', 'city_name' => 'Gorontalo Utara', 'postal_code' => '96611'],
            ['city_id' => '361', 'province_id' => '7', 'province' => 'Gorontalo', 'type' => 'Kabupaten', 'city_name' => 'Pohuwato', 'postal_code' => '96419']
        ],
        // Jambi
        '8' => [
            ['city_id' => '156', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kabupaten', 'city_name' => 'Batanghari', 'postal_code' => '36613'],
            ['city_id' => '157', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kabupaten', 'city_name' => 'Bungo', 'postal_code' => '37216'],
            ['city_id' => '158', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kota', 'city_name' => 'Jambi', 'postal_code' => '36111'],
            ['city_id' => '213', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kabupaten', 'city_name' => 'Kerinci', 'postal_code' => '37167'],
            ['city_id' => '280', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kabupaten', 'city_name' => 'Merangin', 'postal_code' => '37319'],
            ['city_id' => '293', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kabupaten', 'city_name' => 'Muaro Jambi', 'postal_code' => '36311'],
            ['city_id' => '393', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kabupaten', 'city_name' => 'Sarolangun', 'postal_code' => '37419'],
            ['city_id' => '442', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kota', 'city_name' => 'Sungai Penuh', 'postal_code' => '37113'],
            ['city_id' => '460', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kabupaten', 'city_name' => 'Tanjung Jabung Barat', 'postal_code' => '36513'],
            ['city_id' => '461', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kabupaten', 'city_name' => 'Tanjung Jabung Timur', 'postal_code' => '36719'],
            ['city_id' => '471', 'province_id' => '8', 'province' => 'Jambi', 'type' => 'Kabupaten', 'city_name' => 'Tebo', 'postal_code' => '37519']
        ],
        // Jawa Barat
        '9' => [
            ['city_id' => '22', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Bandung', 'postal_code' => '40311'],
            ['city_id' => '23', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bandung', 'postal_code' => '40111'],
            ['city_id' => '24', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Bandung Barat', 'postal_code' => '40721'],
            ['city_id' => '34', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bekasi', 'postal_code' => '17111'],
            ['city_id' => '35', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Bekasi', 'postal_code' => '17837'],
            ['city_id' => '53', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Bogor', 'postal_code' => '16911'],
            ['city_id' => '54', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bogor', 'postal_code' => '16111'],
            ['city_id' => '94', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Ciamis', 'postal_code' => '46211'],
            ['city_id' => '95', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Cianjur', 'postal_code' => '43217'],
            ['city_id' => '106', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Cimahi', 'postal_code' => '40512'],
            ['city_id' => '107', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Cirebon', 'postal_code' => '45611'],
            ['city_id' => '108', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Cirebon', 'postal_code' => '45116'],
            ['city_id' => '115', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Depok', 'postal_code' => '16416'],
            ['city_id' => '126', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Garut', 'postal_code' => '44126'],
            ['city_id' => '149', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Indramayu', 'postal_code' => '45214'],
            ['city_id' => '170', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Karawang', 'postal_code' => '41311'],
            ['city_id' => '178', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Kuningan', 'postal_code' => '45511'],
            ['city_id' => '261', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Majalengka', 'postal_code' => '45412'],
            ['city_id' => '340', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Pangandaran', 'postal_code' => '46511'],
            ['city_id' => '364', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Purwakarta', 'postal_code' => '41119'],
            ['city_id' => '390', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Subang', 'postal_code' => '41215'],
            ['city_id' => '391', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Sukabumi', 'postal_code' => '43311'],
            ['city_id' => '392', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Sukabumi', 'postal_code' => '43114'],
            ['city_id' => '393', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Sumedang', 'postal_code' => '45326'],
            ['city_id' => '430', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Tasikmalaya', 'postal_code' => '46411'],
            ['city_id' => '431', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Tasikmalaya', 'postal_code' => '46116']
        ],
        // Jawa Tengah
        '10' => [
            ['city_id' => '19', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Banjarnegara', 'postal_code' => '53419'],
            ['city_id' => '33', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Banyumas', 'postal_code' => '53114'],
            ['city_id' => '41', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Batang', 'postal_code' => '51211'],
            ['city_id' => '49', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Blora', 'postal_code' => '58219'],
            ['city_id' => '55', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Boyolali', 'postal_code' => '57312'],
            ['city_id' => '76', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Brebes', 'postal_code' => '52212'],
            ['city_id' => '91', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Cilacap', 'postal_code' => '53211'],
            ['city_id' => '113', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Demak', 'postal_code' => '59519'],
            ['city_id' => '134', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Grobogan', 'postal_code' => '58111'],
            ['city_id' => '163', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Jepara', 'postal_code' => '59419'],
            ['city_id' => '169', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Karanganyar', 'postal_code' => '57718'],
            ['city_id' => '177', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Kebumen', 'postal_code' => '54319'],
            ['city_id' => '181', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Kendal', 'postal_code' => '51314'],
            ['city_id' => '196', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Klaten', 'postal_code' => '57411'],
            ['city_id' => '209', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Kudus', 'postal_code' => '59311'],
            ['city_id' => '249', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Magelang', 'postal_code' => '56519'],
            ['city_id' => '250', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Magelang', 'postal_code' => '56133'],
            ['city_id' => '344', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Pati', 'postal_code' => '59114'],
            ['city_id' => '348', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Pekalongan', 'postal_code' => '51161'],
            ['city_id' => '349', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Pekalongan', 'postal_code' => '51122'],
            ['city_id' => '352', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Pemalang', 'postal_code' => '52319'],
            ['city_id' => '375', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Purbalingga', 'postal_code' => '53312'],
            ['city_id' => '377', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Purworejo', 'postal_code' => '54111'],
            ['city_id' => '380', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Rembang', 'postal_code' => '59219'],
            ['city_id' => '386', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Salatiga', 'postal_code' => '50711'],
            ['city_id' => '397', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Semarang', 'postal_code' => '50511'],
            ['city_id' => '398', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Semarang', 'postal_code' => '50111'],
            ['city_id' => '427', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Sragen', 'postal_code' => '57211'],
            ['city_id' => '433', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Sukoharjo', 'postal_code' => '57514'],
            ['city_id' => '445', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Tegal', 'postal_code' => '52419'],
            ['city_id' => '446', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Tegal', 'postal_code' => '52121'],
            ['city_id' => '457', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Temanggung', 'postal_code' => '56212'],
            ['city_id' => '472', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Surakarta (Solo)', 'postal_code' => '57113'],
            ['city_id' => '497', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Wonogiri', 'postal_code' => '57619'],
            ['city_id' => '498', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Wonosobo', 'postal_code' => '56311']
        ],
        // Jawa Timur
        '11' => [
            ['city_id' => '15', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Bangkalan', 'postal_code' => '69118'],
            ['city_id' => '36', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Banyuwangi', 'postal_code' => '68416'],
            ['city_id' => '44', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Blitar', 'postal_code' => '66171'],
            ['city_id' => '45', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Blitar', 'postal_code' => '66124'],
            ['city_id' => '47', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Bojonegoro', 'postal_code' => '62119'],
            ['city_id' => '51', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Bondowoso', 'postal_code' => '68219'],
            ['city_id' => '133', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Gresik', 'postal_code' => '61115'],
            ['city_id' => '160', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Jember', 'postal_code' => '68113'],
            ['city_id' => '164', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Jombang', 'postal_code' => '61415'],
            ['city_id' => '178', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Kediri', 'postal_code' => '64184'],
            ['city_id' => '179', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Kediri', 'postal_code' => '64125'],
            ['city_id' => '222', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Lamongan', 'postal_code' => '64125'],
            ['city_id' => '243', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Lumajang', 'postal_code' => '67319'],
            ['city_id' => '247', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Madiun', 'postal_code' => '63153'],
            ['city_id' => '248', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Madiun', 'postal_code' => '63122'],
            ['city_id' => '251', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Magetan', 'postal_code' => '63314'],
            ['city_id' => '256', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Malang', 'postal_code' => '65163'],
            ['city_id' => '257', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Malang', 'postal_code' => '65112'],
            ['city_id' => '262', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Mojokerto', 'postal_code' => '61382'],
            ['city_id' => '263', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Mojokerto', 'postal_code' => '61316'],
            ['city_id' => '335', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Nganjuk', 'postal_code' => '64414'],
            ['city_id' => '336', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Ngawi', 'postal_code' => '63219'],
            ['city_id' => '338', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Pacitan', 'postal_code' => '63512'],
            ['city_id' => '343', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Pamekasan', 'postal_code' => '69319'],
            ['city_id' => '363', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Pasuruan', 'postal_code' => '67153'],
            ['city_id' => '364', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Pasuruan', 'postal_code' => '67118'],
            ['city_id' => '370', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Ponorogo', 'postal_code' => '63411'],
            ['city_id' => '373', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Probolinggo', 'postal_code' => '67282'],
            ['city_id' => '374', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Probolinggo', 'postal_code' => '67215'],
            ['city_id' => '385', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Sampang', 'postal_code' => '69219'],
            ['city_id' => '409', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Sidoarjo', 'postal_code' => '61219'],
            ['city_id' => '418', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Situbondo', 'postal_code' => '68316'],
            ['city_id' => '441', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Sumenep', 'postal_code' => '69413'],
            ['city_id' => '444', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Surabaya', 'postal_code' => '60119'],
            ['city_id' => '487', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Trenggalek', 'postal_code' => '66312'],
            ['city_id' => '489', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Tuban', 'postal_code' => '62319'],
            ['city_id' => '492', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Tulungagung', 'postal_code' => '66212'],
            ['city_id' => '493', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Batu', 'postal_code' => '65311']
        ],
        // Kalimantan Barat
        '12' => [
            ['city_id' => '63', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Bengkayang', 'postal_code' => '79213'],
            ['city_id' => '168', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Kapuas Hulu', 'postal_code' => '78719'],
            ['city_id' => '176', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Kayong Utara', 'postal_code' => '78852'],
            ['city_id' => '195', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Ketapang', 'postal_code' => '78874'],
            ['city_id' => '208', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Kubu Raya', 'postal_code' => '78311'],
            ['city_id' => '228', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Landak', 'postal_code' => '78319'],
            ['city_id' => '279', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Melawi', 'postal_code' => '78619'],
            ['city_id' => '364', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Mempawah', 'postal_code' => '78911'],
            ['city_id' => '395', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Sambas', 'postal_code' => '79453'],
            ['city_id' => '415', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Sanggau', 'postal_code' => '78557'],
            ['city_id' => '417', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Sekadau', 'postal_code' => '79583'],
            ['city_id' => '423', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Sintang', 'postal_code' => '78619'],
            ['city_id' => '351', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kota', 'city_name' => 'Pontianak', 'postal_code' => '78112'],
            ['city_id' => '405', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kota', 'city_name' => 'Singkawang', 'postal_code' => '79117']
        ],

        // Kalimantan Selatan
        '13' => [
            ['city_id' => '19', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Balangan', 'postal_code' => '71611'],
            ['city_id' => '35', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Banjar', 'postal_code' => '70619'],
            ['city_id' => '37', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kota', 'city_name' => 'Banjarbaru', 'postal_code' => '70712'],
            ['city_id' => '38', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kota', 'city_name' => 'Banjarmasin', 'postal_code' => '70117'],
            ['city_id' => '86', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Barito Kuala', 'postal_code' => '70511'],
            ['city_id' => '149', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Hulu Sungai Selatan', 'postal_code' => '71212'],
            ['city_id' => '150', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Hulu Sungai Tengah', 'postal_code' => '71313'],
            ['city_id' => '151', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Hulu Sungai Utara', 'postal_code' => '71419'],
            ['city_id' => '215', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Kotabaru', 'postal_code' => '72119'],
            ['city_id' => '438', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Tabalong', 'postal_code' => '71513'],
            ['city_id' => '439', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Tanah Bumbu', 'postal_code' => '72211'],
            ['city_id' => '440', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Tanah Laut', 'postal_code' => '70811'],
            ['city_id' => '450', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Tapin', 'postal_code' => '71119']
        ],

        // Kalimantan Tengah
        '14' => [
            ['city_id' => '82', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Barito Selatan', 'postal_code' => '73711'],
            ['city_id' => '83', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Barito Timur', 'postal_code' => '73671'],
            ['city_id' => '84', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Barito Utara', 'postal_code' => '73881'],
            ['city_id' => '140', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Gunung Mas', 'postal_code' => '74511'],
            ['city_id' => '171', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Kapuas', 'postal_code' => '73583'],
            ['city_id' => '173', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Katingan', 'postal_code' => '74411'],
            ['city_id' => '217', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Kotawaringin Barat', 'postal_code' => '74119'],
            ['city_id' => '218', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Kotawaringin Timur', 'postal_code' => '74364'],
            ['city_id' => '220', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Lamandau', 'postal_code' => '74611'],
            ['city_id' => '312', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Murung Raya', 'postal_code' => '73911'],
            ['city_id' => '345', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Pulang Pisau', 'postal_code' => '74811'],
            ['city_id' => '387', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Seruyan', 'postal_code' => '74211'],
            ['city_id' => '428', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Sukamara', 'postal_code' => '74712'],
            ['city_id' => '330', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kota', 'city_name' => 'Palangkaraya', 'postal_code' => '73112']
        ],

        // Kalimantan Timur
        '15' => [
            ['city_id' => '66', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kabupaten', 'city_name' => 'Berau', 'postal_code' => '77311'],
            ['city_id' => '89', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kota', 'city_name' => 'Balikpapan', 'postal_code' => '76111'],
            ['city_id' => '99', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kota', 'city_name' => 'Bontang', 'postal_code' => '75313'],
            ['city_id' => '214', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kabupaten', 'city_name' => 'Kutai Barat', 'postal_code' => '75711'],
            ['city_id' => '215', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kabupaten', 'city_name' => 'Kutai Kartanegara', 'postal_code' => '75511'],
            ['city_id' => '216', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kabupaten', 'city_name' => 'Kutai Timur', 'postal_code' => '75611'],
            ['city_id' => '341', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kabupaten', 'city_name' => 'Paser', 'postal_code' => '76211'],
            ['city_id' => '354', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kabupaten', 'city_name' => 'Penajam Paser Utara', 'postal_code' => '76311'],
            ['city_id' => '387', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kota', 'city_name' => 'Samarinda', 'postal_code' => '75133'],
            ['city_id' => '415', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kabupaten', 'city_name' => 'Mahakam Ulu', 'postal_code' => '77571']
        ],

        // Kalimantan Utara
        '16' => [
            ['city_id' => '96', 'province_id' => '16', 'province' => 'Kalimantan Utara', 'type' => 'Kabupaten', 'city_name' => 'Bulungan', 'postal_code' => '77211'],
            ['city_id' => '257', 'province_id' => '16', 'province' => 'Kalimantan Utara', 'type' => 'Kota', 'city_name' => 'Tarakan', 'postal_code' => '77114'],
            ['city_id' => '268', 'province_id' => '16', 'province' => 'Kalimantan Utara', 'type' => 'Kabupaten', 'city_name' => 'Malinau', 'postal_code' => '77511'],
            ['city_id' => '324', 'province_id' => '16', 'province' => 'Kalimantan Utara', 'type' => 'Kabupaten', 'city_name' => 'Nunukan', 'postal_code' => '77421'],
            ['city_id' => '467', 'province_id' => '16', 'province' => 'Kalimantan Utara', 'type' => 'Kabupaten', 'city_name' => 'Tana Tidung', 'postal_code' => '77611']
        ],
        // Kepulauan Riau
        '17' => [
            ['city_id' => '48', 'province_id' => '17', 'province' => 'Kepulauan Riau', 'type' => 'Kabupaten', 'city_name' => 'Bintan', 'postal_code' => '29135'],
            ['city_id' => '71', 'province_id' => '17', 'province' => 'Kepulauan Riau', 'type' => 'Kota', 'city_name' => 'Batam', 'postal_code' => '29413'],
            ['city_id' => '172', 'province_id' => '17', 'province' => 'Kepulauan Riau', 'type' => 'Kabupaten', 'city_name' => 'Karimun', 'postal_code' => '29611'],
            ['city_id' => '184', 'province_id' => '17', 'province' => 'Kepulauan Riau', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Anambas', 'postal_code' => '29991'],
            ['city_id' => '237', 'province_id' => '17', 'province' => 'Kepulauan Riau', 'type' => 'Kabupaten', 'city_name' => 'Lingga', 'postal_code' => '29811'],
            ['city_id' => '302', 'province_id' => '17', 'province' => 'Kepulauan Riau', 'type' => 'Kabupaten', 'city_name' => 'Natuna', 'postal_code' => '29711'],
            ['city_id' => '462', 'province_id' => '17', 'province' => 'Kepulauan Riau', 'type' => 'Kota', 'city_name' => 'Tanjung Pinang', 'postal_code' => '29111']
        ],

        // Lampung
        '18' => [
            ['city_id' => '21', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kota', 'city_name' => 'Bandar Lampung', 'postal_code' => '35139'],
            ['city_id' => '223', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Lampung Barat', 'postal_code' => '34814'],
            ['city_id' => '224', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Lampung Selatan', 'postal_code' => '35511'],
            ['city_id' => '225', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Lampung Tengah', 'postal_code' => '34212'],
            ['city_id' => '226', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Lampung Timur', 'postal_code' => '34319'],
            ['city_id' => '227', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Lampung Utara', 'postal_code' => '34516'],
            ['city_id' => '282', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Mesuji', 'postal_code' => '34911'],
            ['city_id' => '283', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kota', 'city_name' => 'Metro', 'postal_code' => '34111'],
            ['city_id' => '355', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Pesawaran', 'postal_code' => '35312'],
            ['city_id' => '356', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Pesisir Barat', 'postal_code' => '35974'],
            ['city_id' => '368', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Pringsewu', 'postal_code' => '35719'],
            ['city_id' => '458', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Tanggamus', 'postal_code' => '35619'],
            ['city_id' => '490', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Tulang Bawang', 'postal_code' => '34613'],
            ['city_id' => '491', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Tulang Bawang Barat', 'postal_code' => '34419'],
            ['city_id' => '496', 'province_id' => '18', 'province' => 'Lampung', 'type' => 'Kabupaten', 'city_name' => 'Way Kanan', 'postal_code' => '34711']
        ],

        // Maluku
        '19' => [
            ['city_id' => '14', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kota', 'city_name' => 'Ambon', 'postal_code' => '97222'],
            ['city_id' => '99', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kabupaten', 'city_name' => 'Buru', 'postal_code' => '97371'],
            ['city_id' => '100', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kabupaten', 'city_name' => 'Buru Selatan', 'postal_code' => '97351'],
            ['city_id' => '185', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Aru', 'postal_code' => '97681'],
            ['city_id' => '258', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kabupaten', 'city_name' => 'Maluku Barat Daya', 'postal_code' => '97451'],
            ['city_id' => '259', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kabupaten', 'city_name' => 'Maluku Tengah', 'postal_code' => '97513'],
            ['city_id' => '260', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kabupaten', 'city_name' => 'Maluku Tenggara', 'postal_code' => '97651'],
            ['city_id' => '261', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kabupaten', 'city_name' => 'Maluku Tenggara Barat', 'postal_code' => '97465'],
            ['city_id' => '400', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kabupaten', 'city_name' => 'Seram Bagian Barat', 'postal_code' => '97561'],
            ['city_id' => '401', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kabupaten', 'city_name' => 'Seram Bagian Timur', 'postal_code' => '97581'],
            ['city_id' => '488', 'province_id' => '19', 'province' => 'Maluku', 'type' => 'Kota', 'city_name' => 'Tual', 'postal_code' => '97612']
        ],

        // Maluku Utara
        '20' => [
            ['city_id' => '138', 'province_id' => '20', 'province' => 'Maluku Utara', 'type' => 'Kabupaten', 'city_name' => 'Halmahera Barat', 'postal_code' => '97757'],
            ['city_id' => '139', 'province_id' => '20', 'province' => 'Maluku Utara', 'type' => 'Kabupaten', 'city_name' => 'Halmahera Selatan', 'postal_code' => '97911'],
            ['city_id' => '140', 'province_id' => '20', 'province' => 'Maluku Utara', 'type' => 'Kabupaten', 'city_name' => 'Halmahera Tengah', 'postal_code' => '97853'],
            ['city_id' => '141', 'province_id' => '20', 'province' => 'Maluku Utara', 'type' => 'Kabupaten', 'city_name' => 'Halmahera Timur', 'postal_code' => '97862'],
            ['city_id' => '142', 'province_id' => '20', 'province' => 'Maluku Utara', 'type' => 'Kabupaten', 'city_name' => 'Halmahera Utara', 'postal_code' => '97762'],
            ['city_id' => '191', 'province_id' => '20', 'province' => 'Maluku Utara', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Sula', 'postal_code' => '97995'],
            ['city_id' => '372', 'province_id' => '20', 'province' => 'Maluku Utara', 'type' => 'Kabupaten', 'city_name' => 'Pulau Morotai', 'postal_code' => '97771'],
            ['city_id' => '477', 'province_id' => '20', 'province' => 'Maluku Utara', 'type' => 'Kabupaten', 'city_name' => 'Pulau Taliabu', 'postal_code' => '97885'],
            ['city_id' => '465', 'province_id' => '20', 'province' => 'Maluku Utara', 'type' => 'Kota', 'city_name' => 'Ternate', 'postal_code' => '97714'],
            ['city_id' => '466', 'province_id' => '20', 'province' => 'Maluku Utara', 'type' => 'Kota', 'city_name' => 'Tidore Kepulauan', 'postal_code' => '97815']
        ],

        // Nanggroe Aceh Darussalam (NAD)
        '21' => [
            ['city_id' => '1', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Barat', 'postal_code' => '23681'],
            ['city_id' => '2', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Barat Daya', 'postal_code' => '23764'],
            ['city_id' => '3', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Besar', 'postal_code' => '23951'],
            ['city_id' => '4', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Jaya', 'postal_code' => '23654'],
            ['city_id' => '5', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Selatan', 'postal_code' => '23719'],
            ['city_id' => '6', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Singkil', 'postal_code' => '24785'],
            ['city_id' => '7', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Tamiang', 'postal_code' => '24476'],
            ['city_id' => '8', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Tengah', 'postal_code' => '24511'],
            ['city_id' => '9', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Tenggara', 'postal_code' => '24611'],
            ['city_id' => '10', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Timur', 'postal_code' => '24454'],
            ['city_id' => '11', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Aceh Utara', 'postal_code' => '24382'],
            ['city_id' => '20', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kota', 'city_name' => 'Banda Aceh', 'postal_code' => '23238'],
            ['city_id' => '59', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Bener Meriah', 'postal_code' => '24581'],
            ['city_id' => '72', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Bireuen', 'postal_code' => '24219'],
            ['city_id' => '127', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Gayo Lues', 'postal_code' => '24653'],
            ['city_id' => '230', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kota', 'city_name' => 'Langsa', 'postal_code' => '24412'],
            ['city_id' => '235', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kota', 'city_name' => 'Lhokseumawe', 'postal_code' => '24352'],
            ['city_id' => '300', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Nagan Raya', 'postal_code' => '23674'],
            ['city_id' => '358', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Pidie', 'postal_code' => '24186'],
            ['city_id' => '359', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Pidie Jaya', 'postal_code' => '24186'],
            ['city_id' => '384', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kota', 'city_name' => 'Sabang', 'postal_code' => '23512'],
            ['city_id' => '414', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kabupaten', 'city_name' => 'Simeulue', 'postal_code' => '23891'],
            ['city_id' => '429', 'province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)', 'type' => 'Kota', 'city_name' => 'Subulussalam', 'postal_code' => '24882']
        ],

        // Nusa Tenggara Barat (NTB)
        '22' => [
            ['city_id' => '61', 'province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)', 'type' => 'Kabupaten', 'city_name' => 'Bima', 'postal_code' => '84171'],
            ['city_id' => '62', 'province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)', 'type' => 'Kota', 'city_name' => 'Bima', 'postal_code' => '84139'],
            ['city_id' => '95', 'province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)', 'type' => 'Kabupaten', 'city_name' => 'Dompu', 'postal_code' => '84217'],
            ['city_id' => '246', 'province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)', 'type' => 'Kabupaten', 'city_name' => 'Lombok Barat', 'postal_code' => '83311'],
            ['city_id' => '247', 'province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)', 'type' => 'Kabupaten', 'city_name' => 'Lombok Tengah', 'postal_code' => '83511'],
            ['city_id' => '248', 'province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)', 'type' => 'Kabupaten', 'city_name' => 'Lombok Timur', 'postal_code' => '83612'],
            ['city_id' => '249', 'province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)', 'type' => 'Kabupaten', 'city_name' => 'Lombok Utara', 'postal_code' => '83711'],
            ['city_id' => '250', 'province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)', 'type' => 'Kota', 'city_name' => 'Mataram', 'postal_code' => '83131'],
            ['city_id' => '305', 'province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)', 'type' => 'Kabupaten', 'city_name' => 'Sumbawa', 'postal_code' => '84315'],
            ['city_id' => '306', 'province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)', 'type' => 'Kabupaten', 'city_name' => 'Sumbawa Barat', 'postal_code' => '84419']
        ],

        // Nusa Tenggara Timur (NTT)
        '23' => [
            ['city_id' => '13', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Alor', 'postal_code' => '85811'],
            ['city_id' => '58', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Belu', 'postal_code' => '85711'],
            ['city_id' => '122', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Ende', 'postal_code' => '86351'],
            ['city_id' => '125', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Flores Timur', 'postal_code' => '86213'],
            ['city_id' => '212', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Kupang', 'postal_code' => '85362'],
            ['city_id' => '213', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kota', 'city_name' => 'Kupang', 'postal_code' => '85119'],
            ['city_id' => '234', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Lembata', 'postal_code' => '86611'],
            ['city_id' => '269', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Malaka', 'postal_code' => '85762'],
            ['city_id' => '275', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Manggarai', 'postal_code' => '86551'],
            ['city_id' => '276', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Manggarai Barat', 'postal_code' => '86753'],
            ['city_id' => '277', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Manggarai Timur', 'postal_code' => '86654'],
            ['city_id' => '301', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Nagekeo', 'postal_code' => '86911'],
            ['city_id' => '339', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Ngada', 'postal_code' => '86413'],
            ['city_id' => '384', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Rote Ndao', 'postal_code' => '85982'],
            ['city_id' => '385', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Sabu Raijua', 'postal_code' => '85391'],
            ['city_id' => '412', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Sikka', 'postal_code' => '86121'],
            ['city_id' => '434', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Sumba Barat', 'postal_code' => '87219'],
            ['city_id' => '435', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Sumba Barat Daya', 'postal_code' => '87453'],
            ['city_id' => '436', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Sumba Tengah', 'postal_code' => '87358'],
            ['city_id' => '437', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Sumba Timur', 'postal_code' => '87112'],
            ['city_id' => '479', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Timor Tengah Selatan', 'postal_code' => '85562'],
            ['city_id' => '480', 'province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)', 'type' => 'Kabupaten', 'city_name' => 'Timor Tengah Utara', 'postal_code' => '85612']
        ],

        // Papua
        '24' => [
            ['city_id' => '26', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Asmat', 'postal_code' => '99777'],
            ['city_id' => '67', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Biak Numfor', 'postal_code' => '98119'],
            ['city_id' => '90', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Boven Digoel', 'postal_code' => '99662'],
            ['city_id' => '111', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Deiyai', 'postal_code' => '98784'],
            ['city_id' => '117', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Dogiyai', 'postal_code' => '98866'],
            ['city_id' => '150', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Intan Jaya', 'postal_code' => '98771'],
            ['city_id' => '157', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Jayapura', 'postal_code' => '99352'],
            ['city_id' => '158', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kota', 'city_name' => 'Jayapura', 'postal_code' => '99114'],
            ['city_id' => '159', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Jayawijaya', 'postal_code' => '99511'],
            ['city_id' => '180', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Keerom', 'postal_code' => '99461'],
            ['city_id' => '193', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Yapen', 'postal_code' => '98211'],
            ['city_id' => '231', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Lanny Jaya', 'postal_code' => '99531'],
            ['city_id' => '263', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Mamberamo Raya', 'postal_code' => '99381'],
            ['city_id' => '264', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Mamberamo Tengah', 'postal_code' => '99553'],
            ['city_id' => '274', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Mappi', 'postal_code' => '99853'],
            ['city_id' => '281', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Merauke', 'postal_code' => '99613'],
            ['city_id' => '284', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Mimika', 'postal_code' => '99962'],
            ['city_id' => '299', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Nabire', 'postal_code' => '98816'],
            ['city_id' => '303', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Nduga', 'postal_code' => '99541'],
            ['city_id' => '335', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Paniai', 'postal_code' => '98765'],
            ['city_id' => '347', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Pegunungan Bintang', 'postal_code' => '99573'],
            ['city_id' => '373', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Puncak', 'postal_code' => '98981'],
            ['city_id' => '374', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Puncak Jaya', 'postal_code' => '98979'],
            ['city_id' => '392', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Sarmi', 'postal_code' => '99373'],
            ['city_id' => '443', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Supiori', 'postal_code' => '98164'],
            ['city_id' => '484', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Tolikara', 'postal_code' => '99411'],
            ['city_id' => '495', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Waropen', 'postal_code' => '98269'],
            ['city_id' => '499', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Yahukimo', 'postal_code' => '99041'],
            ['city_id' => '500', 'province_id' => '24', 'province' => 'Papua', 'type' => 'Kabupaten', 'city_name' => 'Yalimo', 'postal_code' => '99481']
        ],

        // Papua Barat
        '25' => [
            ['city_id' => '70', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Fakfak', 'postal_code' => '98651'],
            ['city_id' => '179', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Kaimana', 'postal_code' => '98671'],
            ['city_id' => '272', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Manokwari', 'postal_code' => '98311'],
            ['city_id' => '273', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Manokwari Selatan', 'postal_code' => '98355'],
            ['city_id' => '288', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Maybrat', 'postal_code' => '98051'],
            ['city_id' => '346', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Pegunungan Arfak', 'postal_code' => '98354'],
            ['city_id' => '378', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Raja Ampat', 'postal_code' => '98489'],
            ['city_id' => '424', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Sorong', 'postal_code' => '98431'],
            ['city_id' => '425', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kota', 'city_name' => 'Sorong', 'postal_code' => '98411'],
            ['city_id' => '426', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Sorong Selatan', 'postal_code' => '98454'],
            ['city_id' => '449', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Tambrauw', 'postal_code' => '98475'],
            ['city_id' => '474', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Teluk Bintuni', 'postal_code' => '98551'],
            ['city_id' => '475', 'province_id' => '25', 'province' => 'Papua Barat', 'type' => 'Kabupaten', 'city_name' => 'Teluk Wondama', 'postal_code' => '98591']
        ],

        // Riau
        '26' => [
            ['city_id' => '52', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kabupaten', 'city_name' => 'Bengkalis', 'postal_code' => '28719'],
            ['city_id' => '112', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kota', 'city_name' => 'Dumai', 'postal_code' => '28811'],
            ['city_id' => '147', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kabupaten', 'city_name' => 'Indragiri Hilir', 'postal_code' => '29212'],
            ['city_id' => '148', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kabupaten', 'city_name' => 'Indragiri Hulu', 'postal_code' => '29319'],
            ['city_id' => '166', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kabupaten', 'city_name' => 'Kampar', 'postal_code' => '28411'],
            ['city_id' => '187', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Meranti', 'postal_code' => '28753'],
            ['city_id' => '207', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kabupaten', 'city_name' => 'Kuantan Singingi', 'postal_code' => '29519'],
            ['city_id' => '350', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kota', 'city_name' => 'Pekanbaru', 'postal_code' => '28112'],
            ['city_id' => '351', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kabupaten', 'city_name' => 'Pelalawan', 'postal_code' => '28311'],
            ['city_id' => '381', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kabupaten', 'city_name' => 'Rokan Hilir', 'postal_code' => '28992'],
            ['city_id' => '382', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kabupaten', 'city_name' => 'Rokan Hulu', 'postal_code' => '28511'],
            ['city_id' => '406', 'province_id' => '26', 'province' => 'Riau', 'type' => 'Kabupaten', 'city_name' => 'Siak', 'postal_code' => '28623']
        ],
        // Sulawesi Barat
        '27' => [
            ['city_id' => '270', 'province_id' => '27', 'province' => 'Sulawesi Barat', 'type' => 'Kabupaten', 'city_name' => 'Majene', 'postal_code' => '91411'],
            ['city_id' => '271', 'province_id' => '27', 'province' => 'Sulawesi Barat', 'type' => 'Kabupaten', 'city_name' => 'Mamasa', 'postal_code' => '91362'],
            ['city_id' => '272', 'province_id' => '27', 'province' => 'Sulawesi Barat', 'type' => 'Kabupaten', 'city_name' => 'Mamuju', 'postal_code' => '91512'],
            ['city_id' => '273', 'province_id' => '27', 'province' => 'Sulawesi Barat', 'type' => 'Kabupaten', 'city_name' => 'Mamuju Tengah', 'postal_code' => '91562'],
            ['city_id' => '274', 'province_id' => '27', 'province' => 'Sulawesi Barat', 'type' => 'Kabupaten', 'city_name' => 'Pasangkayu', 'postal_code' => '91765'],
            ['city_id' => '275', 'province_id' => '27', 'province' => 'Sulawesi Barat', 'type' => 'Kabupaten', 'city_name' => 'Polewali Mandar', 'postal_code' => '91311']
        ],

        // Sulawesi Selatan
        '28' => [
            ['city_id' => '53', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Bantaeng', 'postal_code' => '92411'],
            ['city_id' => '67', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Barru', 'postal_code' => '90719'],
            ['city_id' => '73', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Bone', 'postal_code' => '92713'],
            ['city_id' => '78', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Bulukumba', 'postal_code' => '92511'],
            ['city_id' => '118', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Enrekang', 'postal_code' => '91713'],
            ['city_id' => '137', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Gowa', 'postal_code' => '92111'],
            ['city_id' => '162', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Jeneponto', 'postal_code' => '92315'],
            ['city_id' => '244', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Selayar', 'postal_code' => '92812'],
            ['city_id' => '235', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Luwu', 'postal_code' => '91994'],
            ['city_id' => '236', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Luwu Timur', 'postal_code' => '92981'],
            ['city_id' => '237', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Luwu Utara', 'postal_code' => '92911'],
            ['city_id' => '254', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kota', 'city_name' => 'Makassar', 'postal_code' => '90111'],
            ['city_id' => '275', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Maros', 'postal_code' => '90511'],
            ['city_id' => '306', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kota', 'city_name' => 'Palopo', 'postal_code' => '91911'],
            ['city_id' => '317', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Pangkajene Kepulauan', 'postal_code' => '90611'],
            ['city_id' => '410', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kota', 'city_name' => 'Parepare', 'postal_code' => '91123'],
            ['city_id' => '321', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Pinrang', 'postal_code' => '91251'],
            ['city_id' => '366', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Sidenreng Rappang', 'postal_code' => '91613'],
            ['city_id' => '408', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Sinjai', 'postal_code' => '92615'],
            ['city_id' => '416', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Soppeng', 'postal_code' => '90812'],
            ['city_id' => '420', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Takalar', 'postal_code' => '92211'],
            ['city_id' => '427', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Tana Toraja', 'postal_code' => '91819'],
            ['city_id' => '468', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Toraja Utara', 'postal_code' => '91831'],
            ['city_id' => '493', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Wajo', 'postal_code' => '90911']
        ],

        // Sulawesi Tengah
        '29' => [
            ['city_id' => '25', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Banggai', 'postal_code' => '94711'],
            ['city_id' => '26', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Banggai Kepulauan', 'postal_code' => '94881'],
            ['city_id' => '27', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Banggai Laut', 'postal_code' => '94891'],
            ['city_id' => '75', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Buol', 'postal_code' => '94565'],
            ['city_id' => '111', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Donggala', 'postal_code' => '94341'],
            ['city_id' => '267', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Morowali', 'postal_code' => '94911'],
            ['city_id' => '268', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Morowali Utara', 'postal_code' => '94961'],
            ['city_id' => '290', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Parigi Moutong', 'postal_code' => '94411'],
            ['city_id' => '329', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kota', 'city_name' => 'Palu', 'postal_code' => '94111'],
            ['city_id' => '338', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Poso', 'postal_code' => '94615'],
            ['city_id' => '412', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Sigi', 'postal_code' => '94364'],
            ['city_id' => '483', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Tojo Una-Una', 'postal_code' => '94683'],
            ['city_id' => '494', 'province_id' => '29', 'province' => 'Sulawesi Tengah', 'type' => 'Kabupaten', 'city_name' => 'Tolitoli', 'postal_code' => '94542']
        ],

        // Sulawesi Tenggara
        '30' => [
            ['city_id' => '74', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Bombana', 'postal_code' => '93771'],
            ['city_id' => '77', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Buton', 'postal_code' => '93754'],
            ['city_id' => '78', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Buton Selatan', 'postal_code' => '93773'],
            ['city_id' => '79', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Buton Tengah', 'postal_code' => '93763'],
            ['city_id' => '80', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Buton Utara', 'postal_code' => '93745'],
            ['city_id' => '188', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Kolaka', 'postal_code' => '93511'],
            ['city_id' => '189', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Kolaka Timur', 'postal_code' => '93561'],
            ['city_id' => '190', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Kolaka Utara', 'postal_code' => '93911'],
            ['city_id' => '198', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Konawe', 'postal_code' => '93411'],
            ['city_id' => '199', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Konawe Kepulauan', 'postal_code' => '93393'],
            ['city_id' => '200', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Konawe Selatan', 'postal_code' => '93311'],
            ['city_id' => '201', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Konawe Utara', 'postal_code' => '93311'],
            ['city_id' => '271', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Muna', 'postal_code' => '93611'],
            ['city_id' => '272', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Muna Barat', 'postal_code' => '93662'],
            ['city_id' => '295', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kabupaten', 'city_name' => 'Wakatobi', 'postal_code' => '93791'],
            ['city_id' => '204', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kota', 'city_name' => 'Kendari', 'postal_code' => '93126'],
            ['city_id' => '72', 'province_id' => '30', 'province' => 'Sulawesi Tenggara', 'type' => 'Kota', 'city_name' => 'Baubau', 'postal_code' => '93719']
        ],

        // Sulawesi Utara
        '31' => [
            ['city_id' => '69', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Bolaang Mongondow', 'postal_code' => '95755'],
            ['city_id' => '70', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Bolaang Mongondow Selatan', 'postal_code' => '95774'],
            ['city_id' => '71', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Bolaang Mongondow Timur', 'postal_code' => '95783'],
            ['city_id' => '72', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Bolaang Mongondow Utara', 'postal_code' => '95765'],
            ['city_id' => '192', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Sangihe', 'postal_code' => '95819'],
            ['city_id' => '204', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Siau Tagulandang Biaro', 'postal_code' => '95862'],
            ['city_id' => '205', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Talaud', 'postal_code' => '95885'],
            ['city_id' => '267', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Minahasa', 'postal_code' => '95614'],
            ['city_id' => '268', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Minahasa Selatan', 'postal_code' => '95914'],
            ['city_id' => '269', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Minahasa Tenggara', 'postal_code' => '95995'],
            ['city_id' => '270', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kabupaten', 'city_name' => 'Minahasa Utara', 'postal_code' => '95316'],
            ['city_id' => '59', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kota', 'city_name' => 'Bitung', 'postal_code' => '95512'],
            ['city_id' => '202', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kota', 'city_name' => 'Kotamobagu', 'postal_code' => '95711'],
            ['city_id' => '264', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kota', 'city_name' => 'Manado', 'postal_code' => '95123'],
            ['city_id' => '485', 'province_id' => '31', 'province' => 'Sulawesi Utara', 'type' => 'Kota', 'city_name' => 'Tomohon', 'postal_code' => '95416']
        ],
        // Sumatera Barat
        '32' => [
            ['city_id' => '56', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Agam', 'postal_code' => '26411'],
            ['city_id' => '98', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Dharmasraya', 'postal_code' => '27612'],
            ['city_id' => '186', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Kepulauan Mentawai', 'postal_code' => '25771'],
            ['city_id' => '236', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Lima Puluh Kota', 'postal_code' => '26671'],
            ['city_id' => '318', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kota', 'city_name' => 'Padang', 'postal_code' => '25111'],
            ['city_id' => '321', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kota', 'city_name' => 'Padang Panjang', 'postal_code' => '27122'],
            ['city_id' => '322', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Padang Pariaman', 'postal_code' => '25583'],
            ['city_id' => '337', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kota', 'city_name' => 'Pariaman', 'postal_code' => '25511'],
            ['city_id' => '339', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Pasaman', 'postal_code' => '26318'],
            ['city_id' => '340', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Pasaman Barat', 'postal_code' => '26511'],
            ['city_id' => '345', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kota', 'city_name' => 'Payakumbuh', 'postal_code' => '26213'],
            ['city_id' => '357', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Pesisir Selatan', 'postal_code' => '25611'],
            ['city_id' => '394', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kota', 'city_name' => 'Sawahlunto', 'postal_code' => '27416'],
            ['city_id' => '411', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Sijunjung', 'postal_code' => '27511'],
            ['city_id' => '420', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Solok', 'postal_code' => '27365'],
            ['city_id' => '421', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kota', 'city_name' => 'Solok', 'postal_code' => '27315'],
            ['city_id' => '422', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Solok Selatan', 'postal_code' => '27779'],
            ['city_id' => '453', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kabupaten', 'city_name' => 'Tanah Datar', 'postal_code' => '27211'],
            ['city_id' => '173', 'province_id' => '32', 'province' => 'Sumatera Barat', 'type' => 'Kota', 'city_name' => 'Bukittinggi', 'postal_code' => '26115']
        ],

        // Sumatera Selatan
        '33' => [
            ['city_id' => '40', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Banyuasin', 'postal_code' => '30911'],
            ['city_id' => '120', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Empat Lawang', 'postal_code' => '31811'],
            ['city_id' => '220', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Lahat', 'postal_code' => '31419'],
            ['city_id' => '242', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kota', 'city_name' => 'Lubuk Linggau', 'postal_code' => '31614'],
            ['city_id' => '292', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Muara Enim', 'postal_code' => '31315'],
            ['city_id' => '297', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Musi Banyuasin', 'postal_code' => '30719'],
            ['city_id' => '298', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Musi Rawas', 'postal_code' => '31661'],
            ['city_id' => '312', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Musi Rawas Utara', 'postal_code' => '31654'],
            ['city_id' => '324', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Ogan Ilir', 'postal_code' => '30811'],
            ['city_id' => '325', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Ogan Komering Ilir', 'postal_code' => '30618'],
            ['city_id' => '326', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Ogan Komering Ulu', 'postal_code' => '32112'],
            ['city_id' => '327', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Ogan Komering Ulu Selatan', 'postal_code' => '32211'],
            ['city_id' => '328', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kabupaten', 'city_name' => 'Ogan Komering Ulu Timur', 'postal_code' => '32312'],
            ['city_id' => '354', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kota', 'city_name' => 'Palembang', 'postal_code' => '30111'],
            ['city_id' => '369', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kota', 'city_name' => 'Pagar Alam', 'postal_code' => '31512'],
            ['city_id' => '367', 'province_id' => '33', 'province' => 'Sumatera Selatan', 'type' => 'Kota', 'city_name' => 'Prabumulih', 'postal_code' => '31121']
        ],

        // Sumatera Utara
        '34' => [
            ['city_id' => '27', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Asahan', 'postal_code' => '21214'],
            ['city_id' => '52', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Batu Bara', 'postal_code' => '21655'],
            ['city_id' => '70', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Binjai', 'postal_code' => '20712'],
            ['city_id' => '110', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Dairi', 'postal_code' => '22211'],
            ['city_id' => '112', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Deli Serdang', 'postal_code' => '20511'],
            ['city_id' => '202', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Gunungsitoli', 'postal_code' => '22813'],
            ['city_id' => '124', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Humbang Hasundutan', 'postal_code' => '22457'],
            ['city_id' => '165', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Karo', 'postal_code' => '22119'],
            ['city_id' => '173', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Labuhan Batu', 'postal_code' => '21412'],
            ['city_id' => '217', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Labuhan Batu Selatan', 'postal_code' => '21511'],
            ['city_id' => '218', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Labuhan Batu Utara', 'postal_code' => '21711'],
            ['city_id' => '229', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Langkat', 'postal_code' => '20811'],
            ['city_id' => '240', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Medan', 'postal_code' => '20228'],
            ['city_id' => '257', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Mandailing Natal', 'postal_code' => '22916'],
            ['city_id' => '307', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Nias', 'postal_code' => '22876'],
            ['city_id' => '308', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Nias Barat', 'postal_code' => '22895'],
            ['city_id' => '309', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Nias Selatan', 'postal_code' => '22865'],
            ['city_id' => '310', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Nias Utara', 'postal_code' => '22856'],
            ['city_id' => '319', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Padang Lawas', 'postal_code' => '22763'],
            ['city_id' => '320', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Padang Lawas Utara', 'postal_code' => '22753'],
            ['city_id' => '355', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Padang Sidempuan', 'postal_code' => '22727'],
            ['city_id' => '353', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Pakpak Bharat', 'postal_code' => '22272'],
            ['city_id' => '381', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Pematang Siantar', 'postal_code' => '21126'],
            ['city_id' => '399', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Samosir', 'postal_code' => '22392'],
            ['city_id' => '404', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Serdang Bedagai', 'postal_code' => '20915'],
            ['city_id' => '407', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Sibolga', 'postal_code' => '22522'],
            ['city_id' => '413', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Simalungun', 'postal_code' => '21162'],
            ['city_id' => '459', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Tanjung Balai', 'postal_code' => '21321'],
            ['city_id' => '463', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Tapanuli Selatan', 'postal_code' => '22742'],
            ['city_id' => '464', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Tapanuli Tengah', 'postal_code' => '22611'],
            ['city_id' => '465', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Tapanuli Utara', 'postal_code' => '22414'],
            ['city_id' => '470', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Tebing Tinggi', 'postal_code' => '20632'],
            ['city_id' => '481', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten', 'city_name' => 'Toba Samosir', 'postal_code' => '22316']
        ]
        ];

        if ($provinceId && isset($cities[$provinceId])) {
            return $cities[$provinceId];
        } elseif ($provinceId) {
            // If province ID is provided but not in our static list, return empty
            return [];
        }
        
        // If no province ID, combine all cities
        $allCities = [];
        foreach ($cities as $provinceCities) {
            $allCities = array_merge($allCities, $provinceCities);
        }
        
        return $allCities;
    }

    protected function getStaticShippingCost($origin, $destination, $weight, $courier)
    {
        $courierData = [
            'jne' => [
                'code' => 'jne',
                'name' => 'JNE',
                'costs' => [
                    [
                        'service' => 'OKE',
                        'description' => 'Ongkos Kirim Ekonomis',
                        'cost' => [
                            [
                                'value' => 15000,
                                'etd' => '2-3',
                                'note' => ''
                            ]
                        ]
                    ],
                    [
                        'service' => 'REG',
                        'description' => 'Layanan Reguler',
                        'cost' => [
                            [
                                'value' => 20000,
                                'etd' => '1-2',
                                'note' => ''
                            ]
                        ]
                    ],
                    [
                        'service' => 'YES',
                        'description' => 'Yakin Esok Sampai',
                        'cost' => [
                            [
                                'value' => 30000,
                                'etd' => '1',
                                'note' => ''
                            ]
                        ]
                    ],
                ]
            ],
            'tiki' => [
                'code' => 'tiki',
                'name' => 'TIKI',
                'costs' => [
                    [
                        'service' => 'ECO',
                        'description' => 'Economy Service',
                        'cost' => [
                            [
                                'value' => 14000,
                                'etd' => '2-3',
                                'note' => ''
                            ]
                        ]
                    ],
                    [
                        'service' => 'REG',
                        'description' => 'Regular Service',
                        'cost' => [
                            [
                                'value' => 19000,
                                'etd' => '1-2',
                                'note' => ''
                            ]
                        ]
                    ],
                ]
            ],
            'pos' => [
                'code' => 'pos',
                'name' => 'POS Indonesia',
                'costs' => [
                    [
                        'service' => 'POS Reguler',
                        'description' => 'Pos Reguler',
                        'cost' => [
                            [
                                'value' => 18000,
                                'etd' => '2-3',
                                'note' => ''
                            ]
                        ]
                    ],
                    [
                        'service' => 'POS Express',
                        'description' => 'Pos Express',
                        'cost' => [
                            [
                                'value' => 25000,
                                'etd' => '1-2',
                                'note' => ''
                            ]
                        ]
                    ],
                ]
            ],
            // Adding missing couriers that might be in the frontend
            'anteraja' => [
                'code' => 'anteraja',
                'name' => 'AnterAja',
                'costs' => [
                    [
                        'service' => 'Regular',
                        'description' => 'Layanan Reguler',
                        'cost' => [
                            [
                                'value' => 16000,
                                'etd' => '1-2',
                                'note' => ''
                            ]
                        ]
                    ],
                    [
                        'service' => 'Express',
                        'description' => 'Layanan Express',
                        'cost' => [
                            [
                                'value' => 25000,
                                'etd' => '1',
                                'note' => ''
                            ]
                        ]
                    ],
                ]
            ],
            'sicepat' => [
                'code' => 'sicepat',
                'name' => 'SiCepat',
                'costs' => [
                    [
                        'service' => 'REG',
                        'description' => 'Layanan Reguler',
                        'cost' => [
                            [
                                'value' => 17000,
                                'etd' => '1-2',
                                'note' => ''
                            ]
                        ]
                    ],
                    [
                        'service' => 'BEST',
                        'description' => 'Besok Sampai',
                        'cost' => [
                            [
                                'value' => 28000,
                                'etd' => '1',
                                'note' => ''
                            ]
                        ]
                    ],
                ]
            ],
            'jnt' => [
                'code' => 'jnt',
                'name' => 'J&T Express',
                'costs' => [
                    [
                        'service' => 'EZ',
                        'description' => 'Economy Service',
                        'cost' => [
                            [
                                'value' => 15500,
                                'etd' => '2-3',
                                'note' => ''
                            ]
                        ]
                    ],
                    [
                        'service' => 'REG',
                        'description' => 'Regular Service',
                        'cost' => [
                            [
                                'value' => 22000,
                                'etd' => '1-2',
                                'note' => ''
                            ]
                        ]
                    ],
                ]
            ],
        ];
        
        // Apply weight multiplier (for heavier packages)
        if ($weight > 1000) {
            $weightMultiplier = ceil($weight / 1000);
            
            // Loop through each courier service and update costs based on weight
            foreach ($courierData as &$courierInfo) {
                foreach ($courierInfo['costs'] as &$service) {
                    foreach ($service['cost'] as &$cost) {
                        $cost['value'] = $cost['value'] * $weightMultiplier;
                    }
                }
            }
            
            Log::info('Applying weight multiplier to shipping cost', [
                'weight' => $weight, 
                'multiplier' => $weightMultiplier
            ]);
        }

        // Return data for the requested courier only
        if (array_key_exists(strtolower($courier), $courierData)) {
            Log::info('Returning static shipping cost for courier', ['courier' => $courier]);
            return [$courierData[strtolower($courier)]];
        }
        
        Log::warning('Courier not found in static data', ['requested' => $courier]);
        return [];
    }
}

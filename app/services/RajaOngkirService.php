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
            // DKI Jakarta
            '6' => [
                ['city_id' => '152', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Pusat', 'postal_code' => '10540'],
                ['city_id' => '153', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Barat', 'postal_code' => '11220'],
                ['city_id' => '154', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Selatan', 'postal_code' => '12230'],
                ['city_id' => '155', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Timur', 'postal_code' => '13330'],
                ['city_id' => '156', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Utara', 'postal_code' => '14140'],
            ],
            // Jawa Barat
            '9' => [
                ['city_id' => '22', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Bandung', 'postal_code' => '40311'],
                ['city_id' => '23', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bandung', 'postal_code' => '40111'],
                ['city_id' => '24', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kabupaten', 'city_name' => 'Bandung Barat', 'postal_code' => '40721'],
                ['city_id' => '34', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bekasi', 'postal_code' => '17111'],
                ['city_id' => '54', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bogor', 'postal_code' => '16111'],
                ['city_id' => '115', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Depok', 'postal_code' => '16416'],
            ],
            // Jawa Tengah
            '10' => [
                ['city_id' => '398', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Semarang', 'postal_code' => '50111'],
                ['city_id' => '349', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Pekalongan', 'postal_code' => '51122'],
                ['city_id' => '472', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Surakarta (Solo)', 'postal_code' => '57113'],
            ],
            // Jawa Timur
            '11' => [
                ['city_id' => '444', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Surabaya', 'postal_code' => '60119'],
                ['city_id' => '257', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Malang', 'postal_code' => '65112'],
                ['city_id' => '179', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Kediri', 'postal_code' => '64125'],
            ],
            // DI Yogyakarta
            '5' => [
                ['city_id' => '501', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kota', 'city_name' => 'Yogyakarta', 'postal_code' => '55111'],
                ['city_id' => '39', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten', 'city_name' => 'Bantul', 'postal_code' => '55715'],
                ['city_id' => '419', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten', 'city_name' => 'Sleman', 'postal_code' => '55513'],
            ],
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
        ];
        
        // Apply weight multiplier (for heavy packages)
        if ($weight > 1000) {
            $weightMultiplier = ceil($weight / 1000);
            foreach ($courierData as &$courier) {
                foreach ($courier['costs'] as &$cost) {
                    $cost['cost'][0]['value'] *= $weightMultiplier;
                }
            }
        }

        // Return data for the requested courier only
        if (array_key_exists($courier, $courierData)) {
            return [$courierData[$courier]];
        }
        
        return [];
    }
}

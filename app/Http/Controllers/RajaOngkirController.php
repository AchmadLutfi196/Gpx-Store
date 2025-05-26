<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirController extends Controller
{
    protected $apiKey;
    protected $baseUrl;
    protected $isProduction;

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.key');
        $this->isProduction = config('services.rajaongkir.production', false);
        $this->baseUrl = $this->isProduction 
            ? 'https://api.rajaongkir.com/starter' 
            : 'https://api.sandbox.rajaongkir.com/starter';
    }

    public function getProvinces()
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/province");

            $data = $response->json();
            
            if ($response->successful() && isset($data['rajaongkir']['results'])) {
                return response()->json([
                    'success' => true,
                    'data' => $data['rajaongkir']['results']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to get provinces'
            ], 400);
        } catch (\Exception $e) {
            Log::error('RajaOngkir provinces error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting provinces: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCities($provinceId)
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/city", [
                'province' => $provinceId
            ]);

            $data = $response->json();
            
            if ($response->successful() && isset($data['rajaongkir']['results'])) {
                return response()->json([
                    'success' => true,
                    'data' => $data['rajaongkir']['results']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to get cities'
            ], 400);
        } catch (\Exception $e) {
            Log::error('RajaOngkir cities error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting cities: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getShippingCost(Request $request)
    {
        try {
            Log::info('Shipping cost request received', $request->all());
            
            $request->validate([
                'origin' => 'required|string',
                'destination' => 'required|string',
                'weight' => 'required|numeric|min:100',
                'courier' => 'required|string'
            ]);

            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->post("{$this->baseUrl}/cost", [
                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => strtolower($request->courier)
            ]);

            $data = $response->json();
            Log::info('RajaOngkir API response', $data);
            
            if ($response->successful() && isset($data['rajaongkir']['results'])) {
                return response()->json([
                    'success' => true,
                    'results' => $data['rajaongkir']['results']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to get shipping costs',
                'errors' => $data['rajaongkir']['status'] ?? null
            ], 400);
        } catch (\Exception $e) {
            Log::error('RajaOngkir shipping cost error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error calculating shipping cost: ' . $e->getMessage()
            ], 500);
        }
    }
}

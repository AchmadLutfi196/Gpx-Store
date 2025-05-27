<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    protected $rajaOngkirService;
    
    public function __construct(RajaOngkirService $rajaOngkirService)
    {
        $this->rajaOngkirService = $rajaOngkirService;
    }
    
    public function getProvinces()
    {
        $provinces = $this->rajaOngkirService->getProvinces();
        
        return response()->json([
            'success' => true,
            'data' => $provinces
        ]);
    }
    
    public function getCities($provinceId)
    {
        $cities = $this->rajaOngkirService->getCities($provinceId);
        
        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }
    
    public function getShippingCost(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'origin' => 'required|string',
            'destination' => 'required|string',
            'weight' => 'required|numeric|min:100',
            'courier' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Log the request parameters for debugging
        Log::info('Shipping cost request', $request->all());
        
        // Get shipping costs from RajaOngkir service
        $shippingCosts = $this->rajaOngkirService->getShippingCost(
            $request->origin,
            $request->destination,
            $request->weight,
            $request->courier
        );
        
        // Log the response for debugging
        Log::info('Shipping cost response', ['results' => $shippingCosts]);
        
        // Return the shipping costs
        return response()->json([
            'success' => true,
            'results' => $shippingCosts
        ]);
    }
}

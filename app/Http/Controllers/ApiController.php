<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        
        $shippingCosts = $this->rajaOngkirService->getShippingCost(
            $request->origin,
            $request->destination,
            $request->weight,
            $request->courier
        );
        
        return response()->json([
            'success' => true,
            'results' => $shippingCosts
        ]);
    }
}

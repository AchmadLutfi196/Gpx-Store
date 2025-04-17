<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PromoCodeController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
        ]);
        
        $code = $request->coupon_code;
        $subtotal = $request->subtotal;
        
        // Find promo code
        $promoCode = PromoCode::where('code', $code)->first();
        
        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak ditemukan'
            ]);
        }
        
        // Check if the promo code is valid
        $validationResult = $promoCode->isValid($subtotal);
        
        if (!$validationResult['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validationResult['message']
            ]);
        }
        
        // Calculate discount
        $discount = $promoCode->calculateDiscount($subtotal);
        
        // Store promo code details in session
        Session::put('applied_promo', [
            'id' => $promoCode->id,
            'code' => $promoCode->code,
            'discount_type' => $promoCode->discount_type,
            'discount_value' => $promoCode->discount_value,
            'discount_amount' => $discount
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil diterapkan',
            'promo' => [
                'code' => $promoCode->code,
                'discount_type' => $promoCode->discount_type,
                'discount_value' => $promoCode->discount_value,
                'discount_amount' => $discount,
                'formatted_discount' => 'Rp ' . number_format($discount, 0, ',', '.'),
            ]
        ]);
    }
    
    public function remove()
    {
        Session::forget('applied_promo');
        
        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil dihapus'
        ]);
    }
}
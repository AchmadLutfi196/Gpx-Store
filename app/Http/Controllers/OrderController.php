<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        // Pastikan user hanya bisa melihat ordernya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        $order->load('items.product');
        
        return view('orders.show', compact('order'));
    }
}
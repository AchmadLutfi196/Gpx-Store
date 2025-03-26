<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\Address;

class AccountController extends Controller
{
    /**
     * Show the user dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        try {
            $recentOrders = Order::where('user_id', $user->id)
                          ->orderBy('created_at', 'desc')
                          ->take(5)
                          ->get();
                              
            $totalOrders = Order::where('user_id', $user->id)->count();
            $pendingOrders = Order::where('user_id', $user->id)
                           ->where('status', 'pending')
                           ->count();
            $totalSpent = Order::where('user_id', $user->id)
                      ->where('status', 'delivered')
                      ->sum('total');
        } catch (\Exception $e) {
            // If there's an error (like table doesn't exist), use empty data
            $recentOrders = collect();
            $totalOrders = 0;
            $pendingOrders = 0;
            $totalSpent = 0;
        }
                      
        return view('account.dashboard', [
            'user' => $user,
            'recentOrders' => $recentOrders,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'totalSpent' => $totalSpent,
        ]);
    }
    
    /**
     * Show the user orders.
     *
     * @return \Illuminate\View\View
     */
    public function orders()
    {
        $user = Auth::user();
        
        try {
            $orders = Order::where('user_id', $user->id)
                   ->orderBy('created_at', 'desc')
                   ->paginate(10);
        } catch (\Exception $e) {
            // If there's an error, use empty collection with pagination
            $orders = collect()->paginate(10);
        }
                   
        return view('account.orders', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }
    
    /**
     * Show the user profile.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        return view('account.profile', [
            'user' => $user,
        ]);
    }
    
    /**
     * Update the user profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Validate request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'min:8', 'confirmed'],
        ]);
        
        // Update basic info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        
        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }
        
        // Handle password change if provided
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()
                    ->withInput()
                    ->withErrors(['current_password' => 'Password saat ini tidak cocok']);
            }
            
            $user->password = Hash::make($validated['new_password']);
        }
        
        $user->save();
            
        return back()->with('success', 'Profil berhasil diperbarui');
    }
    
    /**
     * Show the user addresses.
     *
     * @return \Illuminate\View\View
     */
    public function addresses()
    {
        $user = Auth::user();
        
        try {
            $addresses = Address::where('user_id', $user->id)->get();
        } catch (\Exception $e) {
            // If there's an error, use empty collection
            $addresses = collect();
        }
        
        return view('account.addresses', [
            'user' => $user,
            'addresses' => $addresses,
        ]);
    }
    
    /**
     * Show the user wishlist.
     *
     * @return \Illuminate\View\View
     */
    public function wishlist()
    {
        $user = Auth::user();
        
        try {
            $wishlistItems = $user->wishlistItems;
        } catch (\Exception $e) {
            // If the relationship doesn't exist yet
            $wishlistItems = collect();
        }
        
        return view('account.wishlist', [
            'user' => $user,
            'wishlistItems' => $wishlistItems,
        ]);
    }
}
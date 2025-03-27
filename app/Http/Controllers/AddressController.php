<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

#[\Illuminate\Routing\Controllers\Middleware(['auth'])]
class AddressController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Use the query builder's latest() method before calling get()
        $addresses = Auth::user()->addresses()->latest()->get();
        
        return view('profile.addresses', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('profile.address-form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'is_default' => 'sometimes|boolean',
        ]);
        
        $user = Auth::user();
        
        // If this is the first address or is_default is checked, make it default
        $makeDefault = $request->has('is_default') || $user->addresses()->count() === 0;
        
        // If making this address default, unset any existing defaults
        if ($makeDefault) {
            $user->addresses()->update(['is_default' => false]);
        }
        
        // Create the address
        $address = $user->addresses()->create(array_merge(
            $validated,
            ['is_default' => $makeDefault]
        ));
        
        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        // Check if the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('profile.address-form', compact('address'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {
        // Check if the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'is_default' => 'sometimes|boolean',
        ]);
        
        $user = Auth::user();
        
        // If making this address default, unset any existing defaults
        if ($request->has('is_default') && !$address->is_default) {
            $user->addresses()->update(['is_default' => false]);
            $validated['is_default'] = true;
        }
        
        $address->update($validated);
        
        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        // Check if the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $user = Auth::user();
        
        // Don't allow deletion if this is the only address
        if ($user->addresses()->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus alamat terakhir.');
        }
        
        $wasDefault = $address->is_default;
        $address->delete();
        
        // If the deleted address was the default, set another address as default
        if ($wasDefault) {
            $newDefaultAddress = $user->addresses()->first();
            if ($newDefaultAddress) {
                $newDefaultAddress->update(['is_default' => true]);
            }
        }
        
        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil dihapus');
    }

    /**
     * Set the specified address as default.
     */
    public function setDefault(Address $address)
    {
        // Check if the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $user = Auth::user();
        
        // Unset any existing default addresses
        $user->addresses()->update(['is_default' => false]);
        
        // Set the new default address
        $address->update(['is_default' => true]);
        
        return back()->with('success', 'Alamat utama berhasil diperbarui');
    }
}
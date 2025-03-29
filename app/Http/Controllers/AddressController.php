<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the user's addresses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();
        
        return view('addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new address.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get list of provinces for select input
        $provinces = $this->getProvincesList();
        
        return view('addresses.create', compact('provinces'));
    }

    /**
     * Store a newly created address in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'province' => 'required|string|max:100',
        ]);
        
        // Add user_id to the validated data
        $validated['user_id'] = Auth::id();
        
        // If this is the first address or if set_as_default is checked, make it default
        if (Auth::user()->addresses()->count() === 0 || $request->has('set_as_default')) {
            $validated['is_default'] = true;
            
            // If making this address default, remove default status from other addresses
            if (Auth::user()->addresses()->count() > 0) {
                Auth::user()->addresses()->update(['is_default' => false]);
            }
        }
        
        // Create the address
        $address = Address::create($validated);
        
        return redirect()->route('addresses.index')
                         ->with('success', 'Address has been added successfully.');
    }

    /**
     * Show the form for editing the specified address.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $address = Address::where('user_id', Auth::id())
                          ->findOrFail($id);
        
        $provinces = $this->getProvincesList();
        
        return view('addresses.edit', compact('address', 'provinces'));
    }

    /**
     * Update the specified address in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', Auth::id())
                          ->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'province' => 'required|string|max:100',
        ]);
        
        // If set_as_default is checked, make it default
        if ($request->has('set_as_default') && !$address->is_default) {
            // Remove default status from other addresses
            Auth::user()->addresses()->where('id', '!=', $address->id)
                                    ->update(['is_default' => false]);
            
            $validated['is_default'] = true;
        }
        
        // Update the address
        $address->update($validated);
        
        return redirect()->route('addresses.index')
                         ->with('success', 'Address has been updated successfully.');
    }

    /**
     * Remove the specified address from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $address = Address::where('user_id', Auth::id())
                          ->findOrFail($id);
        
        // If deleting the default address, make another address default if available
        if ($address->is_default) {
            $newDefault = Auth::user()->addresses()
                                     ->where('id', '!=', $address->id)
                                     ->first();
            
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }
        
        $address->delete();
        
        return redirect()->route('addresses.index')
                         ->with('success', 'Address has been deleted successfully.');
    }

    /**
     * Set the specified address as default.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setDefault($id)
    {
        $address = Address::where('user_id', Auth::id())
                          ->findOrFail($id);
        
        // Remove default status from all addresses
        Auth::user()->addresses()->update(['is_default' => false]);
        
        // Set the selected address as default
        $address->update(['is_default' => true]);
        
        return redirect()->route('addresses.index')
                         ->with('success', 'Default address has been updated.');
    }

    /**
     * Get list of provinces for select input.
     *
     * @return array
     */
    private function getProvincesList()
    {
        // This is a basic list of Indonesian provinces
        // You can replace this with an API call or a more complete list
        return [
            'Aceh',
            'Bali',
            'Bangka Belitung',
            'Banten',
            'Bengkulu',
            'DI Yogyakarta',
            'DKI Jakarta',
            'Gorontalo',
            'Jambi',
            'Jawa Barat',
            'Jawa Tengah',
            'Jawa Timur',
            'Kalimantan Barat',
            'Kalimantan Selatan',
            'Kalimantan Tengah',
            'Kalimantan Timur',
            'Kalimantan Utara',
            'Kepulauan Riau',
            'Lampung',
            'Maluku',
            'Maluku Utara',
            'Nusa Tenggara Barat',
            'Nusa Tenggara Timur',
            'Papua',
            'Papua Barat',
            'Riau',
            'Sulawesi Barat',
            'Sulawesi Selatan',
            'Sulawesi Tengah',
            'Sulawesi Tenggara',
            'Sulawesi Utara',
            'Sumatera Barat',
            'Sumatera Selatan',
            'Sumatera Utara',
        ];
    }
}
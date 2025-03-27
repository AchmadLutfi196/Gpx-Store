<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the wishlist items.
     */
    public function index()
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your wishlist.');
        }

        $wishlists = Wishlist::with('product')
                            ->where('user_id', Auth::id())
                            ->latest()
                            ->get();

        return view('wishlist', compact('wishlists'));
    }

    /**
     * Add a product to the wishlist.
     */
    public function add(Request $request, $productId)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to add item to wishlist.'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to add item to wishlist.');
        }

        try {
            // Periksa apakah produk ada
            $product = Product::findOrFail($productId);

            // Periksa apakah produk sudah ada di wishlist
            $existingWishlist = Wishlist::where('user_id', Auth::id())
                                        ->where('product_id', $productId)
                                        ->first();

            if ($existingWishlist) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This product is already in your wishlist.'
                    ]);
                }
                return redirect()->back()->with('error', 'This product is already in your wishlist.');
            }

            // Tambahkan ke wishlist
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $productId
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product added to wishlist.'
                ]);
            }
            return redirect()->back()->with('success', 'Product added to wishlist.');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add product to wishlist.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to add product to wishlist.');
        }
    }

    /**
     * Remove a product from the wishlist.
     */
    public function remove($id)
{
    $wishlist = Wishlist::where('id', $id)
        ->where('user_id', Auth::id())
        ->first();
        
    if (!$wishlist) {
        return response()->json([
            'success' => false,
            'message' => 'Wishlist item not found'
        ], 404);
    }
    
    $wishlist->delete();
    
    // Always return a JSON response for AJAX requests
    return response()->json([
        'success' => true,
        'message' => 'Product removed from wishlist'
    ]);
}

    /**
     * Clear the entire wishlist.
     */
    public function clear(Request $request)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to clear wishlist.'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to clear wishlist.');
        }

        try {
            Wishlist::where('user_id', Auth::id())->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Wishlist cleared successfully.'
                ]);
            }
            return redirect()->back()->with('success', 'Wishlist cleared successfully.');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear wishlist.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to clear wishlist.');
        }
    }

   /**
 * Toggle product in wishlist (add if not exists, remove if exists).
 */
public function toggle(Request $request, $productId)
{
    // Pastikan user sudah login
    if (!Auth::check()) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to update wishlist.'
            ], 401);
        }
        return redirect()->route('login')->with('error', 'Please login to update wishlist.');
    }

    try {
        // Periksa apakah produk sudah ada di wishlist
        $existingWishlist = Wishlist::where('user_id', Auth::id())
                                   ->where('product_id', $productId)
                                   ->first();

        if ($existingWishlist) {
            // Hapus dari wishlist jika sudah ada
            $existingWishlist->delete();
            $status = 'removed';
            $message = 'Product removed from wishlist.';
        } else {
            // Tambahkan ke wishlist jika belum ada
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $productId
            ]);
            $status = 'added';
            $message = 'Product added to wishlist.';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $message
            ]);
        }
        return redirect()->back()->with('success', $message);

    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update wishlist.'
            ], 500);
        }
        return redirect()->back()->with('error', 'Failed to update wishlist.');
    }
}
}
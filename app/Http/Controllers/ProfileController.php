<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Schema;

#[\Illuminate\Routing\Controllers\Middleware('auth')]
class ProfileController extends Controller
{

    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user just verified their email but no success message is set
        if (Schema::hasColumn('users', 'email_verified_at') && 
            $user->email_verified_at !== null && 
            !session('verification_success') && 
            !session('status') && 
            session('verified') === null) {
            session()->flash('verification_success', 'Email Anda telah terverifikasi!');
        }
        
        // Mark that verification success has been displayed
        session(['verified' => true]);
        
        return view('profile.index', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|confirmed|min:8',
            'remove_avatar' => 'nullable|boolean',
        ]);

        // Handle avatar removal
        if ($request->boolean('remove_avatar')) {
            // Get current avatar path
            $currentAvatar = Auth::user()->avatar;
            
            // Delete the file if it exists
            if ($currentAvatar && Storage::disk('public')->exists($currentAvatar)) {
                Storage::disk('public')->delete($currentAvatar);
            }
            
            // Set avatar to null in data array
            $data['avatar'] = null;
        }
        // Handle avatar upload (only if not removing)
        elseif ($request->hasFile('avatar')) {
            // Get current avatar path
            $currentAvatar = Auth::user()->avatar;
            
            // Delete the old file if it exists
            if ($currentAvatar && Storage::disk('public')->exists($currentAvatar)) {
                Storage::disk('public')->delete($currentAvatar);
            }
            
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle password change
        if (!empty($data['password'])) {
            if (!Hash::check($data['current_password'], Auth::user()->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.']);
            }
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Remove fields that shouldn't be updated directly
        unset($data['current_password']);
        unset($data['remove_avatar']); // Remove from data array to avoid DB errors

        User::where('id', Auth::id())->update($data);

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Show the form for changing the user's password.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    return $fail(__('Password saat ini tidak cocok.'));
                }
            }],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user = Auth::user();
        User::where('id', $user->id)->update([
            'password' => Hash::make($validated['password'])
        ]);
        
        return redirect()->route('profile.index')->with('success', 'Password berhasil diperbarui');
    }

    /**
     * Update the user's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfilePicture(Request $request)
    {
        $validated = $request->validate([
            'profile_picture' => ['required', 'image', 'max:2048'], // Max 2MB
        ]);
        
        $user = Auth::user();
        
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            // Store the new image
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            
            User::where('id', $user->id)->update([
                'profile_picture' => $path,
            ]);
        }
        
        return redirect()->route('profile.index')->with('success', 'Foto profil berhasil diperbarui');
    }

    /**
     * Show the user's order history.
     * This method avoids calling the User::orders() relationship directly to prevent
     * any conflicts with the relationship method.
     *
     * @return \Illuminate\View\View
     */
    public function getOrders()
    {
        $user = Auth::user();
        // Using the Order model directly instead of the relationship
        $orders = Order::where('user_id', $user->id)->latest()->paginate(10);
        
        return view('profile.orders', compact('orders'));
    }
    /**
     * Show details for a specific order.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showOrder($id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->findOrFail($id);
        
        return view('profile.order-detail', compact('order'));
    }

    /**
     * Show the user's wishlist items.
     * Using a different method name to avoid conflicts
     *
     * @return \Illuminate\View\View
     */
    public function getWishlist()
    {
        $user = Auth::user();
        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->with('product')
            ->latest()
            ->paginate(12);
        
        return view('profile.wishlist', compact('wishlistItems'));
    }

    /**
     * Remove an item from the user's wishlist.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFromWishlist($id)
    {
        $user = Auth::user();
        Wishlist::where('user_id', $user->id)->where('id', $id)->delete();
        
        return redirect()->route('profile.wishlist')->with('success', 'Item berhasil dihapus dari wishlist');
    }

    /**
     * Add a product to the user's wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToWishlist(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id']
        ]);
        
        $user = Auth::user();
        
        // Check if product already exists in wishlist
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $validated['product_id'])
            ->exists();
            
        if (!$exists) {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $validated['product_id']
            ]);
            $message = 'Produk berhasil ditambahkan ke wishlist';
        } else {
            $message = 'Produk sudah ada dalam wishlist';
        }
        
        return back()->with('success', $message);
    }

    /**
     * Show the user's reviews.
     * Using a different method name to avoid conflicts
     *
     * @return \Illuminate\View\View
     */
    public function getUserReviews()
    {
        $user = Auth::user();
        $reviews = Review::where('user_id', $user->id)
            ->with('product')
            ->latest()
            ->paginate(10);
        
        return view('profile.reviews', compact('reviews'));
    }


    /**
     * Delete a review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteReview($id)
    {
        $user = Auth::user();
        Review::where('user_id', $user->id)->where('id', $id)->delete();
        
        return redirect()->route('profile.reviews')->with('success', 'Review berhasil dihapus');
    }

    /**
     * Delete the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    return $fail(__('Password tidak cocok.'));
                }
            }],
        ]);
        
        $user = Auth::user();
        
        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }
        
        // Logout and delete account
        Auth::logout();
        User::where('id', $user->id)->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Akun Anda telah berhasil dihapus');
    }

    public function getAddresses()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)
            ->latest()
            ->get();
        
        return view('profile.addresses', compact('addresses'));
    }
}
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Filament\Http\Middleware\Authenticate;
use Filament\Facades\Filament;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/product/{id}', [HomeController::class, 'product'])->name('product');
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
Route::get('/tentang-kami', [App\Http\Controllers\AboutController::class, 'index'])->name('about');
Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'allReviews'])->name('reviews.all');
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
Route::get('/check-message-status', [App\Http\Controllers\MessageController::class, 'checkStatus'])->name('message.check-status');
Route::post('/check-message-status', [App\Http\Controllers\MessageController::class, 'viewStatus'])->name('message.view-status');
Route::get('/messages/{id}', [App\Http\Controllers\MessageController::class, 'viewMessage'])->name('message.view');

// Social login routes
Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider']);
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);

// Cart Routes
Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove', [App\Http\Controllers\CartController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/clear', [App\Http\Controllers\CartController::class, 'clearCart'])->name('cart.clear');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Email Verification Routes
Route::get('/email/verify', function () {
    // If the email is already verified, redirect to profile with success message
    if (Schema::hasColumn('users', 'email_verified_at') && 
            Auth::user()->email_verified_at !== null) {
        return redirect()->route('profile.index')
            ->with('success', 'Email Anda sudah terverifikasi.');
    }
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Use the new controller for the verification route
Route::get('/email/verify/{id}/{hash}', 
    App\Http\Controllers\Auth\VerifyEmailController::class
)->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    // Check if already verified
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('profile.index')
            ->with('success', 'Email Anda sudah terverifikasi.');
    }
    
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Wishlist routes
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/add/{productId}', [App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{wishlistId}', [App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::delete('/wishlist/clear', [App\Http\Controllers\WishlistController::class, 'clear'])->name('wishlist.clear');
    Route::post('/wishlist/toggle/{productId}', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Address routes
    Route::get('/profile/addresses', [AddressController::class, 'index'])->name('profile.addresses');
    
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/regenerate-payment', [App\Http\Controllers\OrderController::class, 'regeneratePayment'])->name('orders.regenerate-payment');
    Route::post('/orders/{order}/complete', [App\Http\Controllers\OrderController::class, 'completeOrder'])->name('orders.complete');
    Route::delete('/orders/{order}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
    
    // Checkout routes - Replace the middleware approach with controller-based verification
    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/payment/finish/{order}', [App\Http\Controllers\CheckoutController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/{order}', [App\Http\Controllers\PaymentController::class, 'show'])->name('payment');

    // Review routes
    Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/orders/{order}/review', [App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/orders/{order}/review', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [App\Http\Controllers\ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Promo code routes
    Route::post('/coupon/apply', [App\Http\Controllers\PromoCodeController::class, 'apply'])->name('coupon.apply');
    Route::post('/coupon/remove', [App\Http\Controllers\PromoCodeController::class, 'remove'])->name('coupon.remove');
    Route::post('/cart/apply-promo', [CartController::class, 'applyPromo'])->name('cart.apply-promo');
});

// Route untuk mengecek status wishlist (tidak perlu auth)
Route::get('/wishlist/check/{product}', [App\Http\Controllers\WishlistController::class, 'check'])->name('wishlist.check');

// Profile Routes
Route::prefix('profile')->name('profile.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    
    Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('change-password');
    Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
    
    Route::put('/update-profile-picture', [ProfileController::class, 'updateProfilePicture'])->name('update-profile-picture');
    
    // Orders
    Route::get('/orders', [ProfileController::class, 'getOrders'])->name('orders');
    Route::get('/orders/{id}', [ProfileController::class, 'showOrder'])->name('orders.show');
    
    // Wishlist
    Route::get('/wishlist', [ProfileController::class, 'getWishlist'])->name('wishlist');
    Route::post('/wishlist/add', [ProfileController::class, 'addToWishlist'])->name('wishlist.add');
    Route::delete('/wishlist/{id}', [ProfileController::class, 'removeFromWishlist'])->name('wishlist.remove');
    
    // Reviews
    Route::get('/reviews', [ProfileController::class, 'getUserReviews'])->name('reviews');
    Route::delete('/reviews/{id}', [ProfileController::class, 'deleteReview'])->name('reviews.delete');
    
    // Account deletion
    Route::delete('/delete-account', [ProfileController::class, 'deleteAccount'])->name('delete-account');
});

// Address routes
Route::middleware(['auth'])->group(function () {
    Route::get('/addresses', [App\Http\Controllers\AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [App\Http\Controllers\AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [App\Http\Controllers\AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.default');
    Route::put('/addresses/{address}/set-default', [App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.set-default');
});

// Protected routes that require email verification
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', function() {
        return redirect()->route('profile.index')->with('verification_success', 'Email berhasil diverifikasi! Akun Anda sekarang aktif.');
    })->name('user.home');
    
    // Other protected routes...
});

// Midtrans notification webhook - no auth required
Route::post('/payment/notification', [App\Http\Controllers\PaymentController::class, 'notification'])->name('payment.notification');

Route::middleware([Authenticate::class])->group(function () {
    // Route khusus admin Filament otomatis sudah diatur lewat AdminPanelProvider
});

// Add this route to your existing web.php file
Route::get('/test-email', [App\Http\Controllers\TestEmailController::class, 'sendTestEmail']);

// Add this route to your existing web.php file (admin only)
Route::get('/debug-email-config', [App\Http\Controllers\EmailDebugController::class, 'debug'])
    ->middleware(['auth', 'admin']);

// Test route for payment success (only in development environment)
if (app()->environment('local')) {
    Route::get('/test-payment-success/{orderId}', [App\Http\Controllers\CheckoutController::class, 'paymentSuccess'])
        ->name('test.payment.success');
}

// Newsletter Routes
Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterSubscriberController::class, 'subscribe'])->name('newsletter.subscribe');
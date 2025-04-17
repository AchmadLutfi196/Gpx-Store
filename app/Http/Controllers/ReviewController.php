<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Perbaikan query untuk mencari pesanan yang belum direview sepenuhnya
        // Menggunakan subquery dengan whereRaw untuk menghindari masalah HAVING pada SQLite
        $completedOrders = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) > 
                        (SELECT COUNT(*) FROM product_reviews WHERE product_reviews.order_id = orders.id)')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Tambahkan items_count dan reviews_count secara manual
        foreach ($completedOrders as $order) {
            $order->items_count = $order->items()->count();
            $order->reviews_count = $order->reviews()->count();
        }
            
        // Ambil review yang sudah ditulis
        $reviews = ProductReview::where('user_id', $user->id)
            ->with(['product', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('reviews.index', compact('completedOrders', 'reviews'));
    }
    
    public function create(Order $order)
    {
        // Pastikan pesanan milik user yang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Pastikan pesanan sudah completed
        if ($order->status !== 'completed') {
            return redirect()->route('orders.index')
                ->with('error', 'Hanya pesanan yang sudah selesai yang dapat direview.');
        }
        
        // Ambil item pesanan yang belum direview
        $reviewedProductIds = $order->reviews()->pluck('product_id')->toArray();
        $unreviewedItems = $order->items()
            ->with('product')
            ->whereNotIn('product_id', $reviewedProductIds)
            ->get();
            
        if ($unreviewedItems->isEmpty()) {
            return redirect()->route('reviews.index')
                ->with('info', 'Semua produk dalam pesanan ini sudah direview.');
        }
        
        return view('reviews.create', compact('order', 'unreviewedItems'));
    }
    
    public function store(Request $request, Order $order)
    {
        // Validasi
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
        ]);
        
        // Pastikan pesanan milik user yang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Pastikan produk ada dalam pesanan
        $orderItem = $order->items()->where('product_id', $request->product_id)->first();
        if (!$orderItem) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan dalam pesanan ini.');
        }
        
        // Cek apakah sudah ada review untuk produk ini di pesanan ini
        $existingReview = ProductReview::where([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'order_id' => $order->id,
        ])->first();
        
        if ($existingReview) {
            return redirect()->back()->with('error', 'Anda sudah mereview produk ini untuk pesanan ini.');
        }
        
        // Buat review baru
        $review = new ProductReview();
        $review->user_id = Auth::id();
        $review->product_id = $request->product_id;
        $review->order_id = $order->id;
        $review->rating = $request->rating;
        $review->review = $request->review;
        $review->verified_purchase = true; // Karena kita tahu ini pembelian terverifikasi
        $review->save();
        
        return redirect()->route('reviews.create', $order->id)
            ->with('success', 'Review berhasil disimpan! Terima kasih atas masukan Anda.');
    }
    
    public function edit(ProductReview $review)
    {
        // Pastikan review milik user yang login
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('reviews.edit', compact('review'));
    }
    
    public function update(Request $request, ProductReview $review)
    {
        // Validasi
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
        ]);
        
        // Pastikan review milik user yang login
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Update review
        $review->rating = $request->rating;
        $review->review = $request->review;
        $review->save();
        
        return redirect()->route('reviews.index')
            ->with('success', 'Review berhasil diperbarui!');
    }
    
    public function destroy(ProductReview $review)
    {
        // Pastikan review milik user yang login
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $review->delete();
        
        return redirect()->route('reviews.index')
            ->with('success', 'Review berhasil dihapus.');
    }
    public function allReviews(Request $request)
{
    // Filter rating jika ada
    $ratingFilter = $request->rating;
    
    // Query dasar
    $query = ProductReview::with(['product', 'user'])
        ->whereNotNull('review')
        ->whereHas('product', function($query) {
            $query->where('is_active', true);
        });
    
    // Terapkan filter rating jika dipilih
    if ($ratingFilter && in_array($ratingFilter, [1, 2, 3, 4, 5])) {
        $query->where('rating', $ratingFilter);
    }
    
    // Ambil semua ulasan
    $reviews = $query->latest()->paginate(12);
    
    // Hitung statistik rating
    $ratingStats = ProductReview::select(DB::raw('rating, count(*) as count'))
        ->groupBy('rating')
        ->orderBy('rating', 'desc')
        ->get()
        ->keyBy('rating');
    
    return view('reviews.all', compact('reviews', 'ratingStats', 'ratingFilter'));
}
}
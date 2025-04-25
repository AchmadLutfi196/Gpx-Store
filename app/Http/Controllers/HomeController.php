<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Use cached data with proper eager loading
        $categories = Cache::remember('home_categories', 60*30, function () {
            return Category::all();
        });
        
        $latestProducts = Cache::remember('home_latest_products', 60*15, function () {
            return Product::with(['brand', 'category', 'reviews'])
                ->latest()
                ->take(8)
                ->get();
        });
        
        $featuredProducts = Cache::remember('home_featured_products', 60*15, function () {
            return Product::with(['brand', 'category', 'reviews'])
                ->where('is_featured', true)
                ->take(4)
                ->get();
        });
        
        // Fix N+1 for best selling product
        $bestSellingProduct = Cache::remember('best_selling_product', 60*30, function () {
            return Product::with(['brand', 'category', 'reviews'])
                ->withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->first();
        });
        
        $bestReviews = Cache::remember('home_best_reviews', 60*15, function () {
            return ProductReview::with(['product.brand', 'product.category', 'user'])
                ->select('product_reviews.*')
                ->join('products', 'product_reviews.product_id', '=', 'products.id')
                ->whereNotNull('product_reviews.review')
                ->where('product_reviews.rating', '>=', 4)
                ->orderBy('product_reviews.created_at', 'desc')
                ->take(4)
                ->get();
        });

        return view('user.home', compact(
            'latestProducts', 
            'featuredProducts', 
            'categories',
            'bestReviews',
            'bestSellingProduct'
        ));
    }
    
    /**
     * Show the shop page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function shop(Request $request)
    {
        // Kode tidak diubah
        // ...
    }
    
    /**
     * Show a product detail page.
     *
     * @param  int|string  $id
     * @return \Illuminate\View\View
     */
    public function product($id)
    {
        // Kode tidak diubah
        // ...
    }
    
    /**
     * Show the cart page.
     *
     * @return \Illuminate\View\View
     */
    public function cart()
    {
        return view('cart');
    }
    
    /**
     * Show the checkout page.
     *
     * @return \Illuminate\View\View
     */
    public function checkout()
    {
        return view('checkout');
    }
    
    /**
     * Show the wishlist page.
     *
     * @return \Illuminate\View\View
     */
    public function wishlist()
    {
        return view('wishlist');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\PromoCode;
use App\Models\ProductReview;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Show the home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Use cached data but make sure rating information is loaded properly
        $categories = Cache::remember('home_categories', 60*30, function () {
            return Category::all();
        });
        
        // Don't cache the latest products - this ensures reviews always show updated
        // Or use a shorter cache time (1 minute) and include rating calculations
        $latestProducts = Product::with(['brand', 'category'])
            ->withCount('reviews')  // Count the number of reviews
            ->withAvg('reviews', 'rating') // Calculate average rating
            ->latest()
            ->take(8)
            ->get();
            
        // Similarly for featured products
        $featuredProducts = Product::with(['brand', 'category'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('is_featured', true)
            ->take(4)
            ->get();
        
        // Fix N+1 for best selling product
        $bestSellingProduct = Cache::remember('best_selling_product', 60*10, function () {
            return Product::with(['brand', 'category'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->first();
        });
        
        // Don't cache reviews so they're always fresh
        $bestReviews = ProductReview::with(['product.brand', 'product.category', 'user'])
            ->select('product_reviews.*')
            ->join('products', 'product_reviews.product_id', '=', 'products.id')
            ->whereNotNull('product_reviews.review')
            ->where('product_reviews.rating', '>=', 4)
            ->orderBy('product_reviews.created_at', 'desc')
            ->take(4)
            ->get();

        // Get a featured promo for the hero banner - priority to ones ending soon
        $homepagePromo = PromoCode::where('is_active', true)
            ->where('show_on_homepage', true)
            ->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            })
            ->where(function ($query) {
                $query->where('usage_limit', 0)
                    ->orWhereRaw('used_count < usage_limit');
            })
            ->orderBy('end_date', 'asc') // Show soonest expiring promo first
            ->first();
        
        // If we have a promo, calculate remaining days for countdown
        $daysRemaining = 0;
        $hoursRemaining = 0;
        $minutesRemaining = 0;
        
        if ($homepagePromo && $homepagePromo->end_date) {
            $now = Carbon::now();
            $endDate = $homepagePromo->end_date;
            
            if ($endDate->gt($now)) {
                $interval = $now->diff($endDate);
                $daysRemaining = $interval->d;
                $hoursRemaining = $interval->h;
                $minutesRemaining = $interval->i;
            }
        }

        return view('user.home', compact(
            'categories',
            'latestProducts',
            'featuredProducts',
            'bestSellingProduct',
            'bestReviews',
            'homepagePromo',
            'daysRemaining',
            'hoursRemaining',
            'minutesRemaining'
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
        $categoryId = $request->query('category');
        $search = $request->query('search');
        $sort = $request->query('sort', 'newest');
        $priceMin = $request->query('price_min');
        $priceMax = $request->query('price_max');
        
        // Fetch categories for sidebar filtering
        $categories = Category::all();
        
        // Fetch brands for filtering
        $brands = Brand::all();
        
        // Build the products query
        $productsQuery = Product::query();
        
        // Apply category filter
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }
        
        // Apply search filter
        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }
        
        // Apply brand filter if it exists in the request
        if ($request->has('brand')) {
            $brandIds = $request->query('brand');
            if (is_array($brandIds)) {
                $productsQuery->whereIn('brand_id', $brandIds);
            } else {
                $productsQuery->where('brand_id', $brandIds);
            }
        }
        
        // Apply price range filter
        if ($priceMin) {
            $productsQuery->where('price', '>=', $priceMin);
        }
        
        if ($priceMax) {
            $productsQuery->where('price', '<=', $priceMax);
        }
        
        // Apply sorting
        switch ($sort) {
            case 'price_asc':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $productsQuery->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $productsQuery->orderBy('name', 'desc');
                break;
            case 'oldest':
                $productsQuery->oldest();
                break;
            default: // newest
                $productsQuery->latest();
                break;
        }
        
        // Paginate the results
        $products = $productsQuery->paginate(12);
        
        // Get the category details if a category is selected
        $selectedCategory = null;
        if ($categoryId) {
            $selectedCategory = Category::find($categoryId);
        }
        
        return view('shop', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'category' => $categoryId,
            'selectedCategory' => $selectedCategory,
            'search' => $search,
            'sort' => $sort,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
        ]);
    }
    
    /**
     * Show a product detail page.
     *
     * @param  int|string  $id
     * @return \Illuminate\View\View
     */
    public function product($id)
    {
        // Try to find product by ID first
        $product = Product::find($id);
        
        // If not found by ID and ID is not numeric, try to find by slug
        if (!$product && !is_numeric($id)) {
            $product = Product::where('slug', $id)->first();
        }
        
        // If product still not found, return 404
        if (!$product) {
            abort(404);
        }
        
        // Get related products in the same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();
        
        return view('product', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
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
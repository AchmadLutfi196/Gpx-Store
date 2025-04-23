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
                ->whereNotNull('review')
                ->where('rating', '>=', 4)
                ->latest()
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
        $categoryId = $request->query('category');
        $search = $request->query('search');
        $sort = $request->query('sort', 'newest');
        $priceMin = $request->query('price_min');
        $priceMax = $request->query('price_max');
        
        // Cache common lookup data
        $categories = Cache::remember('shop_categories', 60*30, function () {
            return Category::all();
        });
        
        $brands = Cache::remember('shop_brands', 60*30, function () {
            return Brand::all();
        });
        
        // Build the products query with eager loading
        $productsQuery = Product::with(['brand', 'category', 'reviews']);
        
        // Apply filters and sorting
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }
        
        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('brand')) {
            $brandIds = $request->query('brand');
            if (is_array($brandIds)) {
                $productsQuery->whereIn('brand_id', $brandIds);
            } else {
                $productsQuery->where('brand_id', $brandIds);
            }
        }
        
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
        // Try to find product by ID or slug with eager loading
        $product = Product::with(['brand', 'category', 'reviews.user'])
            ->when(is_numeric($id), function($query) use ($id) {
                return $query->where('id', $id);
            }, function($query) use ($id) {
                return $query->where('slug', $id);
            })
            ->firstOrFail();
        
        // Get related products with eager loading
        $relatedProducts = Product::with(['brand', 'category', 'reviews'])
            ->where('category_id', $product->category_id)
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
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;

class HomeController extends Controller
{
    /**
     * Show the home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch categories to display in the home page
        $categories = Category::all();
        
        // Fetch latest products
        $latestProducts = Product::latest()->take(8)->get();
        
        // You may also want to get other data like featured products
        $featuredProducts = Product::where('is_featured', true)->take(4)->get();
        
        return view('user.home', [
            'categories' => $categories,
            'latestProducts' => $latestProducts,
            'featuredProducts' => $featuredProducts,
        ]);
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
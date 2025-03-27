<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display the shop page with product listings.
     */
    public function shop(Request $request)
    {
        $query = Product::active();
        
        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $category = Category::findOrFail($request->category);
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category->id);
            });
        }
        
        // Filter by search query if provided
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }
        
        // Filter by price range if provided
        if ($request->has('price_min') && $request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }
        
        if ($request->has('price_max') && $request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }
        
        // Filter by sale items
        if ($request->has('sale') && $request->sale == 'true') {
            $query->where('is_sale', true);
        }
        
        // Filter by new arrivals
        if ($request->has('filter') && $request->filter == 'new') {
            $query->where('is_new', true);
        }
        
        // Sort products
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
                break;
            default:
                $query->latest();
                break;
        }
        
        $products = $query->paginate(12);
        
        // Get all active categories for the filter sidebar
        $categories = Category::where('is_active', true)->get();
        
        return view('shop', compact('products', 'categories'));
    }
    
    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::active()->findOrFail($id);
        
        // Increment view count
        $product->incrementViewsCount();
        
        // Get related products
        $relatedProducts = Product::active()
            ->whereHas('categories', function ($query) use ($product) {
                $query->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();
        
        // Get approved reviews
        $reviews = $product->reviews()->where('is_approved', true)->latest()->get();
        
        return view('product', compact('product', 'relatedProducts', 'reviews'));
    }
    
    /**
     * Store a new review for the product.
     */
    public function storeReview(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);
        
        // Check if user has already reviewed this product
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();
        
        if ($existingReview) {
            return redirect()->back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }
        
        // Create new review
        $review = new Review([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'user_id' => auth()->id() ?? null,
            'product_id' => $product->id,
            'is_approved' => true, // Auto-approve for simplicity
        ]);
        
        $review->save();
        
        return redirect()->back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
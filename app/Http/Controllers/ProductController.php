<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller {
    public function index(Request $request) {
        $query = Product::with('vendor', 'category')->where('is_active', true);

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        if ($request->min_price) $query->where('price', '>=', $request->min_price);
        if ($request->max_price) $query->where('price', '<=', $request->max_price);

        $sort = $request->sort ?? 'newest';
        if ($sort === 'price_low') $query->orderBy('price', 'asc');
        elseif ($sort === 'price_high') $query->orderBy('price', 'desc');
        else $query->latest();

        return response()->json($query->paginate(12));
    }

    public function show($id) {
        return response()->json(Product::with('vendor.user', 'category')->findOrFail($id));
    }

    public function featured() {
        return response()->json(Product::with('vendor', 'category')->where('is_active', true)->latest()->take(8)->get());
    }

    // Vendor CRUD
    public function vendorProducts(Request $request) {
        $vendor = $request->user()->vendor;
        return response()->json(Product::where('vendor_id', $vendor->id)->with('category')->latest()->get());
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required', 'category_id' => 'required|exists:categories,id',
            'description' => 'required', 'price' => 'required|numeric',
            'stock' => 'required|integer', 'image' => 'nullable|image|max:2048'
        ]);

        $vendor = $request->user()->vendor;
        if (!$vendor || $vendor->status !== 'approved') {
            return response()->json(['message' => 'Vendor not approved'], 403);
        }

        $data = $request->only(['name', 'category_id', 'description', 'price', 'discount_price', 'stock']);
        $data['vendor_id'] = $vendor->id;
        $data['slug'] = Str::slug($request->name) . '-' . time();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);
        return response()->json($product, 201);
    }

    public function update(Request $request, $id) {
        $product = Product::findOrFail($id);
        if ($product->vendor_id !== $request->user()->vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->only(['name', 'category_id', 'description', 'price', 'discount_price', 'stock', 'is_active']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $product->update($data);
        return response()->json($product);
    }

    public function destroy(Request $request, $id) {
        $product = Product::findOrFail($id);
        if ($product->vendor_id !== $request->user()->vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $product->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller {
    public function index() { return response()->json(Category::withCount('products')->get()); }

    public function store(Request $request) {
        $request->validate(['name' => 'required|unique:categories']);
        return response()->json(Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon ?? 'bi-tag'
        ]), 201);
    }

    public function update(Request $request, $id) {
        $cat = Category::findOrFail($id);
        $cat->update(['name' => $request->name, 'slug' => Str::slug($request->name), 'icon' => $request->icon]);
        return response()->json($cat);
    }

    public function destroy($id) {
        Category::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

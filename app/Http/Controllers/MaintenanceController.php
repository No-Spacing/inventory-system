<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    public function IndexCategory(){
        return Inertia::render('Admin/Maintenance/Category', [
            'categories' => Category::all(),
        ]);
    }

    public function StoreCategory(Request $request) {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            // 'product_code' => 'required|exists:products,id',
            'product_code' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ]);

        // Create a new category
        Category::create($validated);

        // Redirect back to the categories index with a success message
        return redirect('/category')->with('message', 'Category created successfully!');
    }
}

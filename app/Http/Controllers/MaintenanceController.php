<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Lot;

use Inertia\Inertia;

class MaintenanceController extends Controller
{
    public function IndexUnit(){
        return Inertia::render('Admin/Maintenance/Units', 
        [
            'units' => Unit::all()
        ]);
    }

    public function StoreUnit (Request $request) {
        $validated = $request->validate([
            'unit' => 'required|string|max:255',
        ]);

        Unit::create($validated);

        return redirect('/units')->with('message', 'Unit created successfully!');
    }

    public function IndexCategory(){
        return Inertia::render('Admin/Maintenance/Category', [
            'categories' => Category::all(),
        ]);
    }

    public function StoreCategory(Request $request) {
        $validated = $request->validate([
            // 'product_code' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ]);

        Category::create($validated);

        return redirect('/category')->with('message', 'Category created successfully!');
    }

    public function IndexProduct() {
        return Inertia::render('Admin/Maintenance/Product', [
            'categories' => Category::all(),
            'units' => Unit::all(),
            'products' => Product::all(),
        ]);
    }

    public function StoreProduct (Request $request) {
        $validated = $request->validate([
            'code' => 'required|unique:products',
            'description' => 'required',
            'category' => 'required',
            'unit' => 'required',
            'type' => 'required',
            'remaining_items' => 'required'
        ]);

        Product::create([
            'code' => $request->code,
            'description' => $request->description,
            'category_id' => $request->category,
            'unit_id' => $request->unit,
            'type' => $request->type,
            'remaining_items' => $request->remaining_items,
        ]);

        return redirect('/product')->with('message', 'Product created successfully!');
    }

    public function IndexLot() {
        return Inertia::render('Admin/Maintenance/Lot', [
            'lots' => Lot::all(),
        ]);
    }
    
    public function StoreLot(Request $request) {
        $validated = $request->validate([
            'lot' => 'required|unique:lots'
        ]);

        Lot::create($validated);
          
        // return redirect('/lot')->with('message', 'Lot created successfully!');
        return back()->with('message', 'Lot created successfully!');
    }

}

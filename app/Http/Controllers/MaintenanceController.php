<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Lot;
use App\Models\User;
use App\Models\Role;
use App\Models\Serial;
use App\Models\Warehouse;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

use App\Imports\ProductImport;
use App\Imports\SerialImport;
use App\Imports\LotImport;

use Maatwebsite\Excel\Facades\Excel;

use Inertia\Inertia;

class MaintenanceController extends Controller
{
    // Unit section starts here...
    public function ViewUnit(Request $request){
        return Inertia::render('Admin/Maintenance/Units', 
        [
            'units' => Unit::where('unit', 'Like', '%'.$request->search.'%')
                        ->paginate(10)
        ]);
    }

    public function StoreUnit(Request $request) {
        $validated = $request->validate([
            'unit' => 'required|string|max:255|unique:units',
        ]);

        Unit::create($validated);

        return redirect('/units')->with('message', 'Unit created successfully!');
    }

    public function UpdateUnit(Request $request) {
        $unit = Unit::findOrFail($request->id);

        $unit->fill($request->all());

        if ($unit->isDirty('unit')) {
            $validated = $request->validate([
                'unit' => 'required|string|max:255|unique:units'
            ]);
        }else {
            $validated = $request->validate([
                'unit' => 'required|string|max:255'
            ]);
        }

        Unit::where('id', $request->id)->update(['unit' => $request->unit]);
        return redirect('/units')->with('message', 'Unit updated successfully!');
    }

    public function DeleteUnit(Request $request) {
        try {
            Unit::where('id', $request->id)->delete();
            return redirect('/units')->with('message', 'Unit deleted successfully');
        } catch (QueryException $e) {
            return back()->withErrors(['message' => "Cannot delete this unit, the product of this unit still exist"]);
        }

    }

    // Unit section ends here...

    // Category Section Starts Here...
    public function ViewCategory(Request $request){
        return Inertia::render('Admin/Maintenance/Category', [
            'categories' => Category::where('category_name', 'Like', '%'.$request->search.'%')->paginate(10),
        ]);
    }

    public function StoreCategory(Request $request) {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories'
        ]);

        Category::create([
            'category_name' => $request->category_name , 
            'slug' => $request->slug
        ]);

        return redirect('/category')->with('message', 'Category created successfully!');
    }

    public function UpdateCategory(Request $request){
        $category = Category::findOrFail($request->id);

        $category->fill($request->all());

        if ($category->isDirty('category_name')) {
            $validated = $request->validate([
                'category_name' => 'required|string|max:255|unique:categories'
            ]);
        }else {
            $validated = $request->validate([
                'category_name' => 'required|string|max:255'
            ]);
        }
        
        Category::where('id', $request->id)->update(['category_name' => $request->category_name, 'slug' => $request->slug]);

        return redirect('/category')->with('message', 'Category updated successfully!');
    }

    public function DeleteCategory(Request $request) {
        try {
            Category::where('id', $request->id)->delete();
            return redirect('/category')->with('message', 'Category deleted successfully');
        } catch (QueryException $e) {
            return back()->withErrors(['message' => "Cannot delete this category, the product of this category still exist"]);
        }
    }
    // Category Section Ends Here...

    public function ViewProduct(Request $request) {
        return Inertia::render('Admin/Maintenance/Product', [
            'categories' => Category::all(),
            'units' => Unit::all(),
            'products' => Product::with('category')
            ->with('unit')
            ->where('code', 'Like', '%'.$request->search.'%')
            ->paginate(10)
            ->through(function ($product) {
                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'description' => $product->description,
                    'unit' => $product->unit_id,
                    'category' => $product->category_id,
                    'type' => $product->type,
                    'remaining_items' => $product->remaining_items,
                ];
            }),
        ]);
    }

    public function StoreProduct (Request $request) {
        $validated = $request->validate([
            'code' => 'required|unique:products',
            'description' => 'required',
            'category' => 'required',
            'unit' => 'required',
            'type' => 'required',
        ]);

        Product::create([
            'code' => $request->code,
            'description' => $request->description,
            'category_id' => $request->category,
            'unit_id' => $request->unit,
            'type' => $request->type,
        ]);

        return redirect('/product')->with('message', 'Product created successfully!');  
    }

    public function ImportProduct (Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:10240'
        ]);

        Excel::import(new ProductImport, $request->file);

        return redirect('/product')->with('message', 'Product imported successfully!');
    }

    public function UpdateProduct (Request $request) {
        $product = Product::findOrFail($request->id);

        $product->fill($request->all());

        if ($product->isDirty('code')) {
            $validated = $request->validate([
                'code' => 'required|unique:products',
                'description' => 'required',
                'category' => 'required',
                'unit' => 'required',
                'type' => 'required',
            ]);
        }else {
            $validated = $request->validate([
                'code' => 'required',
                'description' => 'required',
                'category' => 'required',
                'unit' => 'required',
                'type' => 'required',
            ]);
        }

        Product::where('id', $request->id)
        ->update([
            'code' => $request->code,
            'description' => $request->description,
            'category_id' => $request->category,
            'unit_id' => $request->unit,
            'type' => $request->type,
        ]);
        
        return redirect('/product')->with('message', 'Product updated successfully!');
    }

    public function DeleteProduct (Request $request) {
        try {
            Product::where('id', $request->id)->delete();
            return redirect('/product')->with('message', 'Product deleted successfully!');
        } catch (QueryException $e) {
            return back()->withErrors(['message' => "Cannot delete this Product, the stockcard of this product still exist"]);
        }
    }

    public function ViewLot(Request $request) {
        return Inertia::render('Admin/Maintenance/Lot', [
            'lots' => Lot::with('product')
            ->where('lot', 'Like', '%'.$request->search.'%')
            ->paginate(10)
            ->through(function ($lot) {
                return [
                    'id' => $lot->id,
                    'product_id' => $lot->product->id,
                    'code' => $lot->product->code,
                    'lot' => $lot->lot,
                    'expiration_date' => $lot->expiration_date,
                ];
            }),
            'products' => Product::select('id','code')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->where('type', 'Track by Lot')
            ->limit(10)
            ->get()
        ]);
    }
    
    public function StoreLot(Request $request) {
        $validated = $request->validate([
            'code' => 'required',
            'lot' => 'required',
        ]);

        Lot::create([
            'lot' => $request->lot,
            'product_id' => $request->code,
            'expiration_date' => $request->expiration_date
                                ? \Carbon\Carbon::parse($request->expiration_date)->setTimezone('Asia/Manila')->toDateString()
                                : null,
            'total_items' => 0,
        ]);
        
        return back()->with('message', 'Lot created successfully!');
    }

    public function ImportLot (Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:10240'
        ]);

        Excel::import(new LotImport, $request->file);

        return redirect('/lot')->with('message', 'Lot imported successfully!');
    }

    public function UpdateLot(Request $request) {
        $lot = Lot::findOrFail($request->id);

        $lot->fill($request->all());

        if ($lot->isDirty('lot')) {
            $validated = $request->validate([
                'code' => 'required',
                'lot' => 'required|unique:lots',
            ]);
        }else {
            $validated = $request->validate([
                'code' => 'required',
                'lot' => 'required',
            ]);
        }
        
        Lot::where('id', $request->id)
        ->update([
            'lot' => $request->lot, 
            'product_id' => $request->code,
            'expiration_date' => $request->expiration_date
                                ? \Carbon\Carbon::parse($request->expiration_date)->setTimezone('Asia/Manila')->toDateString()
                                : null,
        ]);
        
        return back()->with('message', 'Lot updated successfully!');
    }

    public function DeleteLot(Request $request) {
        try {
            Lot::where('id', $request->id)->delete();
            return redirect('/lot')->with('message', 'Lot deleted successfully!');
        } catch (QueryException $e) {
            return back()->withErrors(['message' => "Cannot delete this Lot, the stockcard of this lot still exist"]);
        }
    }

    public function ViewSerial(Request $request) {
        return Inertia::render('Admin/Maintenance/Serial', [
            'serials' => Serial::where('serial_no', 'Like', '%'.$request->search.'%')
            ->paginate(10)
            ->withQueryString(),
            'products' => Product::select('id','code')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->where('type', 'Track by Serial')
            ->limit(5)
            ->get(),
        ]);
    } 

    public function StoreSerial(Request $request) {

        $request->validate([
            'code' => 'required',
            'serial_no' => 'required|unique:serials'
        ]);

        Serial::create([
            'serial_no' => $request->serial_no,
            'product_id' => $request->code,
        ]);

        return redirect('/serial')->with('message', 'Serial created successfully!');
    }

    public function ImportSerial (Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:10240'
        ]);

        Excel::import(new SerialImport, $request->file);

        return redirect('/serial')->with('message', 'Serial imported successfully!');
    }

    public function StoreMultipleSerial(Request $request)
    {
        $request->validate([
            'serial_no' => 'required|unique:serials',
            'serialStarting' => 'lt:serialEnding|numeric',
            'serialEnding' => 'required|gt:serialStarting|numeric',
        ],
        [
            'serialEnding.required' => 'The Ending No. field is required.',
            'serialStarting.lt' => 'Starting No. field must be less than the Ending No. field.',
            'serialEnding.gt' => 'Ending No. field must be greater than the Starting No. field.'
        ]);

        $serials = Serial::where('serial_no', $request->serial_no . $request->serialStarting)
        ->orWhere('serial_no', $request->serial_no . $request->serialEnding)
        ->first();
        
        if($serials){
            return redirect('/serial')->withErrors(['message' => "Serial already exist!"]);
        }else{
            $length = strlen($request->serialStartingNo);
            for($i = $request->serialStarting; $i <= $request->serialEnding; $i++){
                try {
                    Serial::create([
                        'serial_no' => $request->serial_no . str_pad($i, $length, "0", STR_PAD_LEFT),
                        'product_id' => $request->code,
                    ]);
                } catch (QueryException $e) {
                    return back()->withErrors(['message' => "Some serial has duplicate entry! please check and try again!"]);
                }
            }
        }
    }

    public function UpdateSerial(Request $request) {
        $serial = Serial::findOrFail($request->id);

        $serial->fill($request->all());

        if ($serial->isDirty('serial_no')) {
            $request->validate([
                'serial_no' => 'required|unique:serials'
            ]);
        }else {
            $request->validate([
                'serial_no' => 'required'
            ]);
        }
        
        Serial::where('id', $request->id)->update(['serial_no' => $request->serial_no]);

        return redirect('/serial')->with('message', 'Serial updated successfully!');
    }

    public function DeleteSerial(Request $request) {
        try {
            Serial::where('id', $request->id)->delete();
            return redirect('/serial')->with('message', 'Category deleted successfully');
        } catch (QueryException $e) {
            return back()->withErrors(['message' => "Cannot delete this Serial, the stockcard of this serial still exist"]);
        }
    }

    public function ViewWarehouse(Request $request)
    {
        return Inertia::render('Admin/Maintenance/Warehouse', [
            'warehouses' => Warehouse::where('warehouse', 'Like', '%'.$request->search.'%')
            ->paginate(10)
            ->withQueryString(),
        ]);
    }

    public function StoreWarehouse(Request $request)
    {
        // $request->validate(
        //     [
        //         'warehouse' => 'required|unique:warehouses'
        //     ]
        // );

        Validator::make($request->all(), [
            'warehouse' => 'required',
        ])->validate();
    }

    public function ViewUsers(){
        return Inertia::render('Admin/Maintenance/Users', [
            'users' => User::with('role')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->name,
                    'created_at' => \Carbon\Carbon::parse($user->created_at)->format('Y-m-d g:i a')
                ];
            }),
            'roles' => Role::all(),
        ]);
    }

    public function StoreUser(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'role' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role 
        ]);

        return back()->with('message', 'User created successfully!');
    }

}

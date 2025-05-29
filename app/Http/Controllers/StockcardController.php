<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use Inertia\Inertia;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Stockcard;
use App\Models\Lot;


class StockcardController extends Controller
{
    public function Dashboard(){
        return Inertia::render('Admin/Dashboard', [
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
            'products' => Product::get()
            ->map(function ($product) {
                return [
                    'code' => $product->code,
                    'description' => $product->description,
                    'remaining_items' => $product->remaining_items,
                    'created_at' => \Carbon\Carbon::parse($product->created_at)->format('Y-m-d g:i a')
                ];
            }),
            'lots' => Lot::get(),
        ]);
    }

    public function In(Request $request){
        return Inertia::render('Admin/Transaction/In', [
            'users' => User::with('role')->get(),
            'stockcards' => Stockcard::with('product')
            ->where('transaction_type', 'in')
            ->get()
            ->map(function ($product) {
                return [
                    'code' => $product->product->code,
                    'description' => $product->product->description,
                    'quantity' => $product->quantity,
                    'created_at' => \Carbon\Carbon::parse($product->created_at)->format('Y-m-d g:i a')
                ];
            }),
            'products' => Product::with('unit')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->limit(5)
            ->get(),
            'lots' => Lot::all(),
        ]);
    }

    public function CreateStockcardIn(Request $request){

        $request->validate([
            'code' => 'required',
            'quantity' => 'required',
            'warehouse' => 'required',
            'purchaseDate' => 'required',
            'transactionDate' => 'required', 
            'expirationDate' => 'required',
        ]);

        Stockcard::create([
            'product_id' => $request->code,
            'lot_id' => $request->lot,
            'transaction_type' => 'in',
            'quantity' => $request->quantity,
            'warehouse' => $request->warehouse,
            'enduser_id' => Auth::user()->id,
            'purchase_date' => \Carbon\Carbon::parse($request->purchaseDate)->format('Y-m-d'),
            'transaction_date' => \Carbon\Carbon::parse($request->transactionDate)->format('Y-m-d'),
            'expire_date' => \Carbon\Carbon::parse($request->expirationDate)->format('Y-m-d'),
            'reference' => $request->reference,
            'remarks' => $request->remarks,
        ]);

        Product::where('id', $request->code)->increment('remaining_items', $request->quantity);

        return redirect('/in')->with('message', 'Stock has been successfully added to inventory.');
    }

    public function Out(Request $request){
        return Inertia::render('Admin/Transaction/Out', [
            'users' => User::with('role')->get(),
            'stockcards' => Stockcard::with('product')
            ->where('transaction_type', 'out')
            ->get()
            ->map(function ($product) {
                return [
                    'code' => $product->product->code,
                    'description' => $product->product->description,
                    'quantity' => $product->quantity,
                    'created_at' => \Carbon\Carbon::parse($product->created_at)->format('Y-m-d g:i a')
                ];
            }),
            'products' => Product::with('unit')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->limit(5)
            ->get(),
            'lots' => Lot::all(),
        ]);
    }
    
    public function CreateStockcardOut(Request $request){
        $request->validate([
            'code' => 'required',
            'quantity' => 'required',
            'warehouse' => 'required',
            'purchaseDate' => 'required',
            'transactionDate' => 'required',
            'expirationDate' => 'required',
        ]);

        Stockcard::create([
            'product_id' => $request->code,
            'lot_id' => $request->lot,
            'transaction_type' => 'out',
            'quantity' => $request->quantity,
            'warehouse' => $request->warehouse,
            'enduser_id' => Auth::user()->id,
            'purchase_date' => \Carbon\Carbon::parse($request->purchaseDate)->format('Y-m-d'),
            'transaction_date' => \Carbon\Carbon::parse($request->transactionDate)->format('Y-m-d'),
            'expire_date' => \Carbon\Carbon::parse($request->expirationDate)->format('Y-m-d'),
            'reference' => $request->reference,
            'remarks' => $request->remarks,
        ]);

        Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);

        return redirect('/out')->with('message', 'Stock has been successfully released from inventory.');
    }

}
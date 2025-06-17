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
use App\Models\Serial;


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
            ->with('lot')
            ->with('serial')
            ->where('transaction_type', 'inbound')
            ->get()
            ->map(function ($product) {
                return [
                    'code' => $product->product->code,
                    'description' => $product->product->description,
                    'serialLotNo' => optional($product->lot)->lot . optional($product->serial)->serial_no,
                    'quantity' => $product->quantity,
                    'warehouse' => $product->warehouse,
                    'created_at' => \Carbon\Carbon::parse($product->transaction_date)->format('Y-m-d'),
                    'expiration_date' => \Carbon\Carbon::parse($product->expiration_date)->format('Y-m-d'),
                ];
            }),
            'products' => Product::with('unit')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->limit(5)
            ->get(),
            'lots' => Lot::where('lot', 'Like', '%'.$request->lot.'%')
            ->limit(5)
            ->get(),
        ]);
    }

    public function CreateStockcardIn(Request $request){
        $sampleArray = [];
        if($request->product_type == "Track by Serial"){
            $request->validate([
                'serial_no' => 'required|unique:serials',
                'transactionDate' => 'required',
            ]);
        }else if($request->product_type == "Track by Lot"){
            $request->validate([
                'lot' => 'required',
                'expirationDate' => 'required',
                'quantity' => 'required',
                'transactionDate' => 'required', 
            ]);
        }
        
        if(!empty($request->serialStartingNo)){
            $request->validate([
                'serialStartingNo' => 'lt:serialEndingNo',
                'serialEndingNo' => 'required|gt:serialStartingNo',
            ],
            [
                'serialEndingNo.required' => 'The Ending No. field is required.',
                'serialStartingNo.lt' => 'Starting No. field must be less than the Ending No. field.',
                'serialEndingNo.gt' => 'Ending No. field must be greater than the Starting No. field.'
            ]);
            for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){

                $serials = Serial::create(['serial_no' => $request->serial_no . $i]);
                Product::where('id', $request->code)->increment('remaining_items', $request->quantity);

                $remaining_items = Product::where('id', $request->code)->value('remaining_items');

                Stockcard::create([
                    'product_id' => $request->code,
                    'lot_id' => null,
                    'serial_id' => $serials->id,
                    'transaction_type' => 'inbound',
                    'quantity' => $request->quantity,
                    'item_balance' => $remaining_items,
                    'enduser_id' => Auth::user()->id,
                    'expiration_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->expirationDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                    'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                    'warehouse' => $request->warehouse,
                    'reference' => $request->reference,
                    'remarks' => $request->remarks,
                ]);
            }
        }else{
            Product::where('id', $request->code)->increment('remaining_items', $request->quantity);

            $remaining_items = Product::where('id', $request->code)->value('remaining_items');

            Stockcard::create([
                'product_id' => $request->code,
                'lot_id' => $request->lot,
                'serial_no' => null,
                'transaction_type' => 'inbound',
                'quantity' => $request->quantity,
                'item_balance' => $remaining_items,
                'enduser_id' => Auth::user()->id,
                'expiration_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->expirationDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                'warehouse' => $request->warehouse,
                'reference' => $request->reference,
                'remarks' => $request->remarks,
            ]);
        }

        return redirect('/in')->with('message', 'Stock has been successfully added to inventory.');
    }

    public function Out(Request $request){
        return Inertia::render('Admin/Transaction/Out', [
            'users' => User::with('role')->get(),
            'stockcards' => Stockcard::with('product')
            ->where('transaction_type', 'outbound')
            ->get()
            ->map(function ($product) {
                return [
                    'code' => $product->product->code,
                    'description' => $product->product->description,
                    'serialLotNo' => optional($product->lot)->lot . optional($product->serial)->serial_no,
                    'quantity' => $product->quantity,
                    'warehouse' => $product->warehouse,
                    'created_at' => \Carbon\Carbon::parse($product->transaction_date)->format('Y-m-d'),
                    'expiration_date' => \Carbon\Carbon::parse($product->expiration_date)->format('Y-m-d'),
                ];
            }),
            'products' => Product::with('unit')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->limit(5)
            ->get(),
            'lots' => Lot::where('lot', 'Like', '%'.$request->lot.'%')
            ->limit(5)
            ->get(),
        ]);
    }
    
    public function CreateStockcardOut(Request $request){
       if($request->product_type == "Track by Serial"){
            $request->validate([
                'serial_no' => 'required',
                'transactionDate' => 'required',
            ]);
        }else if($request->product_type == "Track by Lot"){
            $request->validate([
                'lot' => 'required',
                'expirationDate' => 'required',
                'quantity' => 'required',
                'transactionDate' => 'required', 
            ]);
        }
        
        if(!empty($request->serialStartingNo)){
           
            $request->validate([
                'serialStartingNo' => 'lt:serialEndingNo',
                'serialEndingNo' => 'required|gt:serialStartingNo',
            ],
            [
                'serialEndingNo.required' => 'The Ending No. field is required.',
                'serialStartingNo.lt' => 'Starting No. field must be less than the Ending No. field.',
                'serialEndingNo.gt' => 'Ending No. field must be greater than the Starting No. field.'
            ]);
            for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){
                $serials = Serial::where('serial_no', $request->serial_no . $i)->first();
                
                Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);

                $remaining_items = Product::where('id', $request->code)->value('remaining_items');

                Stockcard::create([
                    'product_id' => $request->code,
                    'lot_id' => null,
                    'serial_id' => $serials->id,
                    'transaction_type' => 'outbound',
                    'quantity' => $request->quantity,
                    'item_balance' => $remaining_items,
                    'enduser_id' => Auth::user()->id,
                    'expiration_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->expirationDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                    'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                    'warehouse' => $request->warehouse,
                    'reference' => $request->reference,
                    'remarks' => $request->remarks,
                ]);
            }
        }else{
            Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);

            $remaining_items = Product::where('id', $request->code)->value('remaining_items');

            Stockcard::create([
                'product_id' => $request->code,
                'lot_id' => $request->lot,
                'serial_no' => null,
                'transaction_type' => 'outbound',
                'quantity' => $request->quantity,
                'item_balance' => $remaining_items,
                'enduser_id' => Auth::user()->id,
                'expiration_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->expirationDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                'warehouse' => $request->warehouse,
                'reference' => $request->reference,
                'remarks' => $request->remarks,
            ]);
        }

        return redirect('/out')->with('message', 'Stock has been successfully released from inventory.');
    }

}
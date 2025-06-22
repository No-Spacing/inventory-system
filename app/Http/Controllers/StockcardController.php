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
use App\Models\Warehouse;


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

        $prod = Product::where('code', $request->code)->first();

        $lot = Lot::where('lot', $request->lot)->first();

        $serial = Serial::where('serial_no', $request->serial)->first();

        $lots = Lot::where('lot', 'Like', '%'.$request->lot.'%')
        ->where('product_id', optional($prod)->id)
        ->limit(5)
        ->get();

        $serials = Serial::where('serial_no', 'Like', '%'.$request->serial.'%')
        ->where('product_id', optional($prod)->id)
        ->limit(5)
        ->get();

        $remainingLotItems = StockCard::getLotRemainingStock(
            optional($prod)->id,
            optional($lot)->id,
            $request->warehouse,
        );

        $remainingSerialItems = StockCard::getSerialRemainingStock(
            optional($prod)->id,
            optional($serial)->id,
            $request->warehouse,
        );

        $remainingItems = StockCard::getRemainingStock(
            optional($prod)->id,
            $request->warehouse,
        );

        return Inertia::render('Admin/Transaction/In', [
            'users' => User::with('role')->get(),
            'stockcards' => Stockcard::with('product')
            ->with('lot')
            ->with('serial')
            ->with('warehouse')
            ->get()
            ->map(function ($product) {
                return [
                    'code' => $product->product->code,
                    'description' => $product->product->description,
                    'serialLotNo' => optional($product->lot)->lot . optional($product->serial)->serial_no,
                    'quantity' => $product->quantity,
                    'warehouse' => $product->warehouse->warehouse,
                    'transaction_type' => $product->transaction_type,
                    'created_at' => \Carbon\Carbon::parse($product->transaction_date)->format('Y-m-d'),
                    'expiration_date' => \Carbon\Carbon::parse($product->expiration_date)->format('Y-m-d'),
                ];
            }),
            'products' => Product::with([
                'unit',
                'lots',
                'serials',
            ])
            ->where('code', 'like', '%' . $request->code . '%')
            ->limit(5)
            ->get(),
            'lots' => $lots,
            'serials' => $serials,
            'warehouses' => Warehouse::all(),
            'remainingLotItems' => $remainingLotItems,
            'remainingItems' => $remainingItems,
            'remainingSerialItems' => $remainingSerialItems
        ]);
    }

    public function CreateStockcardIn(Request $request){

        if($request->product_type == "Track by Lot"){
            $request->validate([
                'lot' => 'required',
                'quantity' => 'required',
                'transactionDate' => 'required', 
                'warehouse' => 'required',
            ]);
            
            if($request->input('action') === "inbound") {
                Stockcard::create([
                    'product_id' => $request->code,
                    'lot_id' => $request->lot,
                    'serial_no' => null,
                    'transaction_type' => $request->input('action'),
                    'quantity' => $request->quantity,
                    'enduser_id' => Auth::user()->id,
                    'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                    'warehouse_id' => $request->warehouse,
                    'reference' => $request->reference,
                    'document_no' => $request->document_no,
                    'remarks' => $request->remarks,
                ]);
            }

            if($request->input('action') === "outbound"){
                $available = Stockcard::getLotRemainingStock(
                    $request->code,
                    $request->lot,
                    $request->warehouse
                );

                if ($available >= $request->quantity) {
                    Stockcard::create([
                        'product_id' => $request->code,
                        'lot_id' => $request->lot,
                        'serial_no' => null,
                        'transaction_type' => $request->input('action'),
                        'quantity' => -$request->quantity,
                        'enduser_id' => Auth::user()->id,
                        'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                        'warehouse_id' => $request->warehouse,
                        'reference' => $request->reference,
                        'document_no' => $request->document_no,
                        'remarks' => $request->remarks,
                    ]);
                } else {
                    return back()->withErrors(['message' => 'Insufficient balance for this specific lot.']);
                }
            }
        }

        if($request->product_type == "Track by Serial"){
            $request->validate([
                'serial_no' => 'required|unique:serials',
                'transactionDate' => 'required',
                'warehouse' => 'required',
            ]);

            if(!empty($request->serialStartingNo)){ // check if has input on Starting No.
                $request->validate([
                    'serialStartingNo' => 'lt:serialEndingNo',
                    'serialEndingNo' => 'required|gt:serialStartingNo',
                ],
                [
                    'serialEndingNo.required' => 'The Ending No. field is required.',
                    'serialStartingNo.lt' => 'Starting No. field must be less than the Ending No. field.',
                    'serialEndingNo.gt' => 'Ending No. field must be greater than the Starting No. field.'
                ]);

                $serials = Serial::where('serial_no', $request->serial_no . $request->serialStartingNo)->first();

                if($serials){ // check if serial exist
                    for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){
                    
                        $serials = Serial::where('serial_no', $request->serial_no .$i)->first();

                        Stockcard::create([
                            'product_id' => $request->code,
                            'lot_id' => null,
                            'serial_id' => $serials->id,
                            'transaction_type' => $request->input('action'),
                            'quantity' => -$request->quantity,
                            'enduser_id' => Auth::user()->id,
                            'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                            'warehouse_id' => $request->warehouse,
                            'reference' => $request->reference,
                            'document_no' => $request->document_no,
                            'remarks' => $request->remarks,
                        ]);
                    }
                }
            }else{
                if ($request->input('action') === 'inbound') {
                    $available = Stockcard::getSerialRemainingStock(
                        $request->code,
                        $request->serial_no,
                        $request->warehouse
                    );
                    if($available == $request->quantity){
                        return back()->withErrors(['message' => 'This Serial No. still has item!.']);
                    }else{
                        Stockcard::create([
                            'product_id' => $request->code,
                            'lot_id' => null,
                            'serial_id' => $request->serial_no,
                            'transaction_type' => $request->input('action'),
                            'quantity' => $request->quantity,
                            'enduser_id' => Auth::user()->id,
                            'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                            'warehouse_id' => $request->warehouse,
                            'reference' => $request->reference,
                            'document_no' => $request->document_no,
                            'remarks' => $request->remarks,
                        ]);
                    }
                }

                if ($request->input('action') === 'outbound') {
                    $available = Stockcard::getSerialRemainingStock(
                        $request->code,
                        $request->serial_no,
                        $request->warehouse
                    );
                    if($available >= $request->quantity){
                        Stockcard::create([
                            'product_id' => $request->code,
                            'lot_id' => null,
                            'serial_id' => $request->serial_no,
                            'transaction_type' => $request->input('action'),
                            'quantity' => -$request->quantity,
                            'enduser_id' => Auth::user()->id,
                            'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                            'warehouse_id' => $request->warehouse,
                            'reference' => $request->reference,
                            'document_no' => $request->document_no,
                            'remarks' => $request->remarks,
                        ]);
                    } else {
                        return back()->withErrors(['message' => 'Insufficient balance for this specific serial.']);
                    }
                }
            }
        }

        if($request->input('action') === 'inbound'){
            $message = 'Stock has been successfully to inventory.';
        }   

        if($request->input('action') === 'outbound'){
            $message = 'Stock has been successfully released from inventory.';
        }
        
        return redirect('/in')->with('message', $message);
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
                'serial_no' => 'required|unique:serials',
                'transactionDate' => 'required',
            ]);

            if(!empty($request->serialStartingNo)){ // check if has input on Starting No.
            $request->validate([
                'serialStartingNo' => 'lt:serialEndingNo',
                'serialEndingNo' => 'required|gt:serialStartingNo',
            ],
            [
                'serialEndingNo.required' => 'The Ending No. field is required.',
                'serialStartingNo.lt' => 'Starting No. field must be less than the Ending No. field.',
                'serialEndingNo.gt' => 'Ending No. field must be greater than the Starting No. field.'
            ]);

            $serials = Serial::where('serial_no', $request->serial_no . $request->serialStartingNo)->first();

            if($serials){ // check if serial exist
                for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){
                    Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);
                    
                    $remaining_items = Product::where('id', $request->code)->value('remaining_items');

                    $serial_id = Serial::select('id')->where('serial_no', $request->serial_no . $i)->first(); // get the existing serials

                    Serial::where('id', $serial_id->id)->decrement('total_items', $request->quantity);

                    Stockcard::create([
                        'product_id' => $request->code,
                        'lot_id' => null,
                        'serial_id' => $serial_id->id,
                        'transaction_type' => 'outbound',
                        'quantity' => $request->quantity,
                        'enduser_id' => Auth::user()->id,
                        'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                        'warehouse' => $request->warehouse,
                        'reference' => $request->reference,
                        'document_no' => $request->document_no,
                        'remarks' => $request->remarks,
                    ]);
                }
            }else{  // write the raw serial if it doesn't have the Starting No.
                for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){
                    Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);

                    $remaining_items = Product::where('id', $request->code)->value('remaining_items');

                    $serial_id = Serial::create(['serial_no' => $request->serial_no . $i]); // write the new serials

                    Serial::where('id', $serial_id->id)->update(['total_items' => $request->quantity]);

                    Stockcard::create([
                        'product_id' => $request->code,
                        'lot_id' => null,
                        'serial_id' => $serial_id->id,
                        'transaction_type' => 'outbound',
                        'quantity' => $request->quantity,
                        'enduser_id' => Auth::user()->id,
                        'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                        'warehouse' => $request->warehouse,
                        'reference' => $request->reference,
                        'document_no' => $request->document_no,
                        'remarks' => $request->remarks,
                    ]);
                }
            }
        }else{ // This section goes for lot inputs
            Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);

            $remaining_items = Product::where('id', $request->code)->value('remaining_items');

            Serial::create(['serial_no' => $request->serial_no, 'total_items' => $request->quantity]);

            Stockcard::create([
                'product_id' => $request->code,
                'lot_id' => null,
                'serial_no' => $request->serial_no,
                'transaction_type' => 'outbound',
                'quantity' => $request->quantity,
                'enduser_id' => Auth::user()->id,
                'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                'warehouse' => $request->warehouse,
                'reference' => $request->reference,
                'document_no' => $request->document_no,
                'remarks' => $request->remarks,
            ]);
        }
        }else if($request->product_type == "Track by Lot"){
            $request->validate([
                'lot' => 'required',
                'quantity' => 'required',
                'transactionDate' => 'required', 
            ]);

            Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);

            Lot::where('id', $request->lot)->decrement('total_items', $request->quantity);

            $remaining_items = Product::where('id', $request->code)->value('remaining_items');

            Stockcard::create([
                'product_id' => $request->code,
                'lot_id' => $request->lot,
                'serial_no' => null,
                'transaction_type' => 'outbound',
                'quantity' => $request->quantity,
                'enduser_id' => Auth::user()->id,
                'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
                'warehouse' => $request->warehouse,
                'reference' => $request->reference,
                'document_no' => $request->document_no,
                'remarks' => $request->remarks,
            ]);

        }

        return redirect('/out')->with('message', 'Stock has been successfully released from inventory.');
    }

}
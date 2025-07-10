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

use App\Imports\StockcardImport;

use Maatwebsite\Excel\Facades\Excel;

class StockcardController extends Controller
{
    public function Dashboard(){
        return Inertia::render('Dashboard', [
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

    public function Transaction(Request $request){

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

        $remainingItems = StockCard::getProductWarehouseStock(
            optional($prod)->id,
            $request->warehouse,
        );

        return Inertia::render('Transaction/Transaction', [
            'users' => User::with('role')->get(),
            'stockcards' => Stockcard::with(['product', 'lot', 'serial', 'warehouse'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('serial', function ($q) use ($search) {
                    $q->where('serial_no', 'like', "%{$search}%");
                })->orWhereHas('lot', function ($q) use ($search) {
                    $q->where('lot', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->through(function ($product) {
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

    public function ImportStockcard(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:10240'
        ]);

        Excel::import(new StockcardImport, $request->file);

        return redirect('/transaction')->with('message', 'Stockcard imported successfully!');
    }

    public function StockcardData(Request $request){

        $request->validate([
                'warehouse' => 'required',
                'code' => 'required',
                'transactionDate' => 'required',
            ]);

        $product = Product::select('id')->where('code', $request->code)->first();

        if($request->product_type == "Track by Lot"){
            $request->validate([
                'lot' => 'required',
                'quantity' => 'required',
            ]);

            $lot = Lot::select('id')->where('lot', $request->lot)->where('product_id', $product->id)->first();

            if($request->input('action') === "inbound") 
            {
                $this->createStockcardData($request->all(), $lot->id, null, $product->id);
                $message = 'Stock has been stored successfully to inventory.';
            }

            if($request->input('action') === "outbound")
            {
                $available = Stockcard::getLotRemainingStock(
                    $product->id,
                    $lot->id,
                    $request->warehouse
                );

                if ($available >= $request->quantity) {
                    $this->createStockcardData($request->all(), $lot->id, null, $product->id);
                    $message = 'Stock has been released successfully from inventory.';
                } else {
                    return back()->withErrors(['message' => 'Insufficient balance for this specific lot.']);
                }   
            }
            
            if($request->input('action') === "transfer")
            {   
                $request->validate([
                    'transferWarehouse' => 'required|different:warehouse'
                ]);

                $warehouse = Warehouse::select('warehouse')
                ->where('id', $request->transferWarehouse)
                ->first();

                $available = Stockcard::getLotRemainingStock(
                    $product->id,
                    $lot->id,
                    $request->warehouse
                );
                
                if ($available >= $request->quantity) {
                    $transferRemarks = "Transfer to " . $warehouse->warehouse;
                    $request->merge(['remarks' => $transferRemarks]);
                    $request->merge(['action' => "outbound"]);
                    $this->createStockcardData($request->all(), $lot->id, null, $product->id);
                    $message = 'Stock has been released successfully from inventory.';
                } else {
                    return back()->withErrors(['message' => 'Insufficient balance for this specific lot.']);
                }   
            }
        }

        if($request->product_type == "Track by Serial"){
            
            $request->validate([
                'serial_no' => 'required',
            ]);

            $serial = Serial::select('id')->where('serial_no', $request->serial_no)->where('product_id', $product->id)->first();
    
            if($request->input('action') === 'inbound') {
                if(isset($request->serialStartingNo))
                {
                    $request->validate([
                        'serial_no' => 'required|unique:serials',
                        'serialStartingNo' => 'lt:serialEndingNo|numeric',
                        'serialEndingNo' => 'required|gt:serialStartingNo|numeric',
                    ],
                    [
                        'serialEndingNo.required' => 'The Ending No. field is required.',
                        'serialStartingNo.lt' => 'Starting No. field must be less than the Ending No. field.',
                        'serialEndingNo.gt' => 'Ending No. field must be greater than the Starting No. field.'
                    ]);
                    
                    $length = strlen($request->serialStartingNo);
                    for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){  
                        $serial = Serial::select('id')
                        ->where('serial_no', $request->serial_no . str_pad($i, $length, "0", STR_PAD_LEFT))
                        ->where('product_id', $product->id)
                        ->first(); 
                        $this->createStockcardData($request->all(), null, $serial->id, $product->id);
                    }

                    $message = 'Stock has been stored successfully to inventory.';
                } else {
                    $available = Stockcard::getSerialRemainingStock(
                        $product->id,
                        $serial->id,
                        $request->warehouse
                    );
                    if($available == $request->quantity){
                        return back()->withErrors(['message' => 'This Serial No. still has an item!.']);
                    }else{
                        $this->createStockcardData($request->all(), null, $serial->id, $product->id);
                        $message = 'Stock has been stored successfully to inventory.';
                    }
                }
            }

            if ($request->input('action') === 'outbound') {
                $available = Stockcard::getSerialRemainingStock(
                    $product->id,
                    $serial->id,
                    $request->warehouse
                );
                if($available >= $request->quantity){
                    $this->createStockcardData($request->all(), null, $serial->id, $product->id);
                    $message = 'Stock has been released successfully to inventory.';
                } else {
                    return back()->withErrors(['message' => 'Insufficient balance for this specific serial.']);
                }
            }

            if($request->input('action') === "transfer")
            {   
                $request->validate([
                    'transferWarehouse' => 'required|different:warehouse'
                ]);
                $warehouse = Warehouse::select('warehouse')
                ->where('id', $request->transferWarehouse)
                ->first();

                $available = Stockcard::getSerialRemainingStock(
                    $product->id,
                    $serial->id,
                    $request->warehouse
                );
                
                if ($available >= $request->quantity) {
                    $transferRemarks = "Transfer to " . $warehouse->warehouse;
                    $request->merge(['remarks' => $transferRemarks]);
                    $request->merge(['action' => "outbound"]);
                    $this->createStockcardData($request->all(), null, $serial->id, $product->id);
                    $message = 'Stock has been released successfully from inventory.';
                } else {
                    return back()->withErrors(['message' => 'Insufficient balance for this specific lot.']);
                }   
            }
        }
        
        return redirect('/transaction')->with('message', $message);
    }

    public function createStockcardData($data, $lotId, $serialId, $productId)
    {
        Stockcard::create([
            'product_id' => $productId,
            'lot_id' => $lotId,
            'serial_id' => $serialId,
            'transaction_type' => $data['action'],
            'quantity' => $data['quantity'],
            'enduser_id' => Auth::user()->id,
            'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($data['transactionDate'])->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
            'warehouse_id' => $data['warehouse'],
            'reference' => $data['reference'],
            'document_no' => $data['document_no'],
            'remarks' => $data['remarks'],
        ]);
    }

    public function IndexReceive (Request $request) {

        $warehouse = Warehouse::where('warehouse', $request->from)->first();
        $stockcards =  Stockcard::with('product')
        ->with('lot')
        ->with('serial')
        ->with('warehouse')
        ->where('remarks', "Transfer to " . $request->to)
        ->where('warehouse_id' , optional($warehouse)->id)
        ->where('received_at', NULL)
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'code' => $product->product->code,
                'description' => $product->product->description,
                'serialLotNo' => optional($product->lot)->lot . optional($product->serial)->serial_no,
                'quantity' => $product->quantity,
                'document_no' => $product->document_no,
                'remarks' => $product->remarks,
            ];
        });

        return Inertia::render('Transaction/Receive',
        [
            'stockcards' => $stockcards,   
            'warehouses' => Warehouse::all(),
        ]);
    }

    public function Received(Request $request)
    {
        $stockcard = Stockcard::where('id', $request->stockcardId)->first();
        
        Stockcard::where('id', $request->stockcardId)->update(['received_at' => \Carbon\Carbon::parse(\Carbon\Carbon::parse(now())->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d')]);
        
        $warehouse = Warehouse::where('warehouse', $request->to)->first();

        Stockcard::create([
            'product_id' => $stockcard->product_id,
            'lot_id' => $stockcard->lot_id,
            'serial_id' => $stockcard->serial_id,
            'transaction_type' => 'inbound',
            'quantity' => $stockcard->quantity,
            'enduser_id' => Auth::user()->id,
            'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse(now())->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
            'warehouse_id' => $warehouse->id,
            'reference' => $stockcard->reference,
            'document_no' => $stockcard->document_no,
            'remarks' => 'Received from ' . $request->from,
        ]);

        return back()->with('message', 'Stock has been successfully received from inventory.');
    }

    // public function Out(Request $request){
    //     return Inertia::render('Admin/Transaction/Out', [
    //         'users' => User::with('role')->get(),
    //         'stockcards' => Stockcard::with('product')
    //         ->where('transaction_type', 'outbound')
    //         ->get()
    //         ->map(function ($product) {
    //             return [
    //                 'code' => $product->product->code,
    //                 'description' => $product->product->description,
    //                 'serialLotNo' => optional($product->lot)->lot . optional($product->serial)->serial_no,
    //                 'quantity' => $product->quantity,
    //                 'warehouse' => $product->warehouse,
    //                 'created_at' => \Carbon\Carbon::parse($product->transaction_date)->format('Y-m-d'),
    //                 'expiration_date' => \Carbon\Carbon::parse($product->expiration_date)->format('Y-m-d'),
    //             ];
    //         }),
    //         'products' => Product::with('unit')
    //         ->where('code', 'Like', '%'.$request->code.'%')
    //         ->limit(5)
    //         ->get(),
    //         'lots' => Lot::where('lot', 'Like', '%'.$request->lot.'%')
    //         ->limit(5)
    //         ->get(),
    //     ]);
    // }
    
    // public function CreateStockcardOut(Request $request){
    //     if($request->product_type == "Track by Serial"){
    //         $request->validate([
    //             'serial_no' => 'required|unique:serials',
    //             'transactionDate' => 'required',
    //         ]);

    //         if(!empty($request->serialStartingNo)){ // check if has input on Starting No.
    //         $request->validate([
    //             'serialStartingNo' => 'lt:serialEndingNo',
    //             'serialEndingNo' => 'required|gt:serialStartingNo',
    //         ],
    //         [
    //             'serialEndingNo.required' => 'The Ending No. field is required.',
    //             'serialStartingNo.lt' => 'Starting No. field must be less than the Ending No. field.',
    //             'serialEndingNo.gt' => 'Ending No. field must be greater than the Starting No. field.'
    //         ]);

    //         $serials = Serial::where('serial_no', $request->serial_no . $request->serialStartingNo)->first();

    //         if($serials){ // check if serial exist
    //             for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){
    //                 Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);
                    
    //                 $remaining_items = Product::where('id', $request->code)->value('remaining_items');

    //                 $serial_id = Serial::select('id')->where('serial_no', $request->serial_no . $i)->first(); // get the existing serials

    //                 Serial::where('id', $serial_id->id)->decrement('total_items', $request->quantity);

    //                 Stockcard::create([
    //                     'product_id' => $request->code,
    //                     'lot_id' => null,
    //                     'serial_id' => $serial_id->id,
    //                     'transaction_type' => 'outbound',
    //                     'quantity' => $request->quantity,
    //                     'enduser_id' => Auth::user()->id,
    //                     'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
    //                     'warehouse' => $request->warehouse,
    //                     'reference' => $request->reference,
    //                     'document_no' => $request->document_no,
    //                     'remarks' => $request->remarks,
    //                 ]);
    //             }
    //         }else{  // write the raw serial if it doesn't have the Starting No.
    //             for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){
    //                 Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);

    //                 $remaining_items = Product::where('id', $request->code)->value('remaining_items');

    //                 $serial_id = Serial::create(['serial_no' => $request->serial_no . $i]); // write the new serials

    //                 Serial::where('id', $serial_id->id)->update(['total_items' => $request->quantity]);

    //                 Stockcard::create([
    //                     'product_id' => $request->code,
    //                     'lot_id' => null,
    //                     'serial_id' => $serial_id->id,
    //                     'transaction_type' => 'outbound',
    //                     'quantity' => $request->quantity,
    //                     'enduser_id' => Auth::user()->id,
    //                     'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
    //                     'warehouse' => $request->warehouse,
    //                     'reference' => $request->reference,
    //                     'document_no' => $request->document_no,
    //                     'remarks' => $request->remarks,
    //                 ]);
    //             }
    //         }
    //     }else{ // This section goes for lot inputs
    //         Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);

    //         $remaining_items = Product::where('id', $request->code)->value('remaining_items');

    //         Serial::create(['serial_no' => $request->serial_no, 'total_items' => $request->quantity]);

    //         Stockcard::create([
    //             'product_id' => $request->code,
    //             'lot_id' => null,
    //             'serial_no' => $request->serial_no,
    //             'transaction_type' => 'outbound',
    //             'quantity' => $request->quantity,
    //             'enduser_id' => Auth::user()->id,
    //             'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
    //             'warehouse' => $request->warehouse,
    //             'reference' => $request->reference,
    //             'document_no' => $request->document_no,
    //             'remarks' => $request->remarks,
    //         ]);
    //     }
    //     }else if($request->product_type == "Track by Lot"){
    //         $request->validate([
    //             'lot' => 'required',
    //             'quantity' => 'required',
    //             'transactionDate' => 'required', 
    //         ]);

    //         Product::where('id', $request->code)->decrement('remaining_items', $request->quantity);

    //         Lot::where('id', $request->lot)->decrement('total_items', $request->quantity);

    //         $remaining_items = Product::where('id', $request->code)->value('remaining_items');

    //         Stockcard::create([
    //             'product_id' => $request->code,
    //             'lot_id' => $request->lot,
    //             'serial_no' => null,
    //             'transaction_type' => 'outbound',
    //             'quantity' => $request->quantity,
    //             'enduser_id' => Auth::user()->id,
    //             'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->transactionDate)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
    //             'warehouse' => $request->warehouse,
    //             'reference' => $request->reference,
    //             'document_no' => $request->document_no,
    //             'remarks' => $request->remarks,
    //         ]);

    //     }

    //     return redirect('/out')->with('message', 'Stock has been successfully released from inventory.');
    // }

}
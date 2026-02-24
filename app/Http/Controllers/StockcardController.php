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
    public function Dashboard(Request $request){

        $monthlyInAndOut = DB::table('stockcards')->whereMonth('created_at', now()->month)->count();

        return Inertia::render('Dashboard/Dashboard', [
            'products' => Product::latest()
            ->limit(7)
            ->get()
            ->map(function ($product) {
                return [
                    'code' => $product->code,
                    'description' => $product->description,
                    'remaining_items' => $product->remaining_items,
                    'created_at' => \Carbon\Carbon::parse($product->created_at)->format('m-d-Y'),
                ];
            }),
            'product_count' => Product::count(),
            'lot_count' => Lot::count(),
            'serial_count' => Serial::count(),
            'stockcard_count' => Stockcard::count(),
            'monthlyInAndOut' => $monthlyInAndOut,
        ]);
    }

    public function Transaction(Request $request){
        $search = $request->search;

        $query = DB::table('stockcards')
        ->join('products', 'stockcards.product_id', '=', 'products.id')
        ->leftJoin('lots', 'stockcards.lot_id', '=', 'lots.id')
        ->leftJoin('serials', 'stockcards.serial_id', '=', 'serials.id')
        ->join('warehouses', 'stockcards.warehouse_id', '=', 'warehouses.id')
        ->select(
            'products.code',
            'products.description',
            DB::raw("CONCAT(COALESCE(lots.lot, ''), COALESCE(serials.serial_no, '')) AS serialLotNo"),
            'stockcards.quantity',
            'warehouses.warehouse',
            'stockcards.transaction_type',
            'stockcards.transaction_date',
            'lots.expiration_date'
        )
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('serials.serial_no', 'like', "%{$search}%")
                ->orWhere('lots.lot', 'like', "%{$search}%")
                ->orWhere('products.code', 'like', "%{$search}%");
            });
        })
        ->orderBy('stockcards.created_at', 'DESC');

        // Paginate manually
        $page = request()->get('page', 1);
        $perPage = 10;
        $results = $query->paginate($perPage, ['*'], 'page', $page);

        // Format the results
        $formatted = $results->getCollection()->transform(function ($product) {
            return [
                'code' => $product->code,
                'description' => $product->description,
                'serialLotNo' => $product->serialLotNo,
                'quantity' => $product->quantity,
                'warehouse' => $product->warehouse,
                'transaction_type' => $product->transaction_type,
                'created_at' => Carbon::parse($product->transaction_date)->format('Y-m-d'),
                'expiration_date' => Carbon::parse($product->expiration_date)->format('Y-m-d'),
            ];
        });

        // Replace the collection with formatted data
        $results->setCollection($formatted);

        $products = [];

        $serials = [];

        $remainingItems = 0; 
        $remainingLotItems = 0;
        $remainingSerialItems = 0;

        if(isset($request->code)){
            $products = Product::with([
                'unit',
            ])
            ->where('code', 'like', '%' . $request->code . '%')
            ->limit(5)
            ->get();
        }

        $lots = Lot::where('lot', 'Like', '%'.$request->lotName.'%')
        ->where('product_id', $request->product_id)
        ->limit(5)
        ->get();

        $serials = Serial::where('serial_no', 'Like', '%'.$request->serial_no.'%')
        ->where('product_id', $request->product_id)
        ->limit(5)
        ->get();
    
        if(isset($request->product_id) && isset($request->warehouse_id)){
            $remainingItems = StockCard::getProductWarehouseStock(
                $request->product_id,
                $request->warehouse_id,
            );
        }

        if(isset($request->warehouse_id) && isset($request->product_id) && isset($request->serial_id)){
            $remainingSerialItems = StockCard::getSerialRemainingStock(
                $request->product_id,
                $request->serial_id,
                $request->warehouse_id,
            );
        }

        if(isset($request->warehouse_id) && isset($request->product_id) && isset($request->lot_id))
        {
            $remainingLotItems = StockCard::getLotRemainingStock(
                $request->product_id,
                $request->lot_id,
                $request->warehouse_id,
            );
        }

        return Inertia::render('Transaction/Transaction', [
            'users' => User::with('role')->get(),
            'stockcards' => $results,
            'products' => $products,
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

    public function SubmitInbound(Request $request){
        $request->validate([
            'warehouse_id' => 'required',
            'product_id' => 'required',
        ]);

        if($request->product_type == "Track by Lot"){
            $request->validate([
                'lot_id' => 'required',
                'quantity' => 'required',
            ]);

            $this->createStockcardData($request->all());
            $message = 'Stock has been stored successfully to inventory.';
         
        }

        if($request->product_type == "Track by Serial"){
             if(isset($request->serial_id)){
                $available = Stockcard::getSerialRemainingStock(
                    $request->product_id,
                    $request->serial_id,
                    $request->warehouse_id
                );
                if($available == $request->quantity){
                    return back()->withErrors(['message' => 'This Serial No. still has an item!.']);
                }else{
                    $this->createStockcardData($request->all());
                    $message = 'Stock has been stored successfully to inventory.';
                }
            }    

            if(isset($request->serialStartingNo)){
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
                
                $array = [];
                
                $length = strlen($request->serialStartingNo);

                $missingStockSerials = [];
                for ($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++) {
                    $formattedSerialNo = $request->serial_no . str_pad($i, $length, "0", STR_PAD_LEFT);
                    
                    $serial = Serial::select('id')
                    ->where('serial_no', $formattedSerialNo)
                    ->where('product_id', $request->product_id)
                    ->first();

                    if (!$serial) {
                        $missingStockSerials[] = $formattedSerialNo;
                        continue;
                    }
                    
                    $available = Stockcard::getSerialRemainingStock(
                        $request->product_id,
                        $serial->id,
                        $request->warehouse_id
                    );
                    
                    if ($available >= 1) {
                        $missingStockSerials[] = $formattedSerialNo;
                    }
                }
                
                if (count($missingStockSerials) > 0) {
                    return back()->withErrors(['message' => 'Some Serials are Missing or still has stocks! please check and try again']);
                }

                for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){  
                    $serial = Serial::select('id')
                    ->where('serial_no', $request->serial_no . str_pad($i, $length, "0", STR_PAD_LEFT))
                    ->where('product_id', $request->product_id)
                    ->first(); 

                    $request->merge([
                        'serial_id' => $serial->id,
                    ]);

                    $this->createStockcardData($request->all());
                }

                $message = 'Stock has been stored successfully to inventory.';
            } 
        }
        return redirect('/transaction')->with('message', $message);
    }

    public function SubmitOutbound(Request $request){
        $available = Stockcard::getLotRemainingStock(
            $request->product_id,
            $request->lot_id,
            $request->warehouse_id
        );
        if($request->product_type == "Track by Lot"){
            if ($available >= $request->quantity) {
                $this->createStockcardData($request->all());
                $message = 'Stock has been released successfully from inventory.';
            } else {
                return back()->withErrors(['message' => 'Insufficient balance for this specific lot.']);
            }
        }
        
        if($request->product_type == "Track by Serial"){
            if (isset($request->serialStartingNo)) {
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
                
                $array = [];
                $length = strlen($request->serialStartingNo);

                $missingStockSerials = [];

                for ($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++) {
                    $formattedSerialNo = $request->serial_no . str_pad($i, $length, "0", STR_PAD_LEFT);
                    
                    $serial = Serial::select('id')
                    ->where('serial_no', $formattedSerialNo)
                    ->where('product_id', $request->product_id)
                    ->first();

                    if (!$serial) {
                        $missingStockSerials[] = $formattedSerialNo;
                        continue;
                    }

                    $available = Stockcard::getSerialRemainingStock(
                        $request->product_id,
                        $serial->id,
                        $request->warehouse_id
                    );
                    
                    if ($available == 0) {
                        $missingStockSerials[] = $formattedSerialNo;
                    }
                }
                
                if (count($missingStockSerials) > 0) {
                    return back()->withErrors(['message' => 'Some Serials are Missing or still has no stocks! please check and try again']);
                }

                for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){  
                    $serial = Serial::select('id')
                    ->where('serial_no', $request->serial_no . str_pad($i, $length, "0", STR_PAD_LEFT))
                    ->where('product_id', $request->product_id)
                    ->first(); 

                    $request->merge([
                        'serial_id' => $serial->id,
                    ]);

                    $this->createStockcardData($request->all());
                }

                $message = 'Stock has been released successfully to inventory.';
            } else {
                $available = Stockcard::getSerialRemainingStock(
                    $request->product_id,
                    $request->serial_id,
                    $request->warehouse_id
                );
                if($available >= $request->quantity){
                    $this->createStockcardData($request->all());
                    $message = 'Stock has been released successfully to inventory.';
                } else {
                    return back()->withErrors(['message' => 'Insufficient balance for this specific serial.']);
                }
            }
        }

        return redirect('/transaction')->with('message', $message);
    }

    public function SubmitTransfer (Request $request) {
        $request->validate([
            'transferWarehouse' => 'required|different:warehouse_id'
        ]);

        $warehouse = Warehouse::select('warehouse')
        ->where('id', $request->transferWarehouse)
        ->first();

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
            
            $array = [];
            $length = strlen($request->serialStartingNo);

            $missingStockSerials = [];

            for ($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++) {
                $formattedSerialNo = $request->serial_no . str_pad($i, $length, "0", STR_PAD_LEFT);
                
                $serial = Serial::select('id')
                ->where('serial_no', $formattedSerialNo)
                ->where('product_id', $request->product_id)
                ->first();

                if (!$serial) {
                    $missingStockSerials[] = $formattedSerialNo;
                    continue;
                }

                $available = Stockcard::getSerialRemainingStock(
                    $request->product_id,
                    $serial->id,
                    $request->warehouse_id
                );
                
                if ($available == 0) {
                    $missingStockSerials[] = $formattedSerialNo;
                }
            }
            
            if (count($missingStockSerials) > 0) {
                return back()->withErrors(['message' => 'Some Serials are Missing or still has no stocks! please check and try again']);
            }

            for($i = $request->serialStartingNo; $i <= $request->serialEndingNo; $i++){  
                $serial = Serial::select('id')
                ->where('serial_no', $request->serial_no . str_pad($i, $length, "0", STR_PAD_LEFT))
                ->where('product_id', $request->product_id)
                ->first(); 
                
                $transferRemarks = "Transfer to " . $warehouse->warehouse;
                
                $request->merge([
                    'serial_id' => $serial->id,
                ]);

                $request->merge(['remarks' => $transferRemarks]);
                $request->merge(['action' => "outbound"]);

                $this->createStockcardData($request->all());
            }

            $message = 'Stock has been released successfully to inventory.';
        } else {
            $available = Stockcard::getSerialRemainingStock(
                $request->product_id,
                $request->serial_id,
                $request->warehouse_id
            );
            
            if ($available >= $request->quantity) {
                $transferRemarks = "Transfer to " . $warehouse->warehouse;

                $request->merge(['remarks' => $transferRemarks]);
                $request->merge(['action' => "outbound"]);

                $this->createStockcardData($request->all());

                $message = 'Stock has been released successfully from inventory.';
            } else {
                return back()->withErrors(['message' => 'Insufficient balance for this specific lot.']);
            } 
        }

        return redirect('/transaction')->with('message', $message);
    }

    public function createStockcardData($data)
    {
        Stockcard::create([
            'product_id' => $data['product_id'],
            'lot_id' => $data['lot_id'],
            'serial_id' => $data['serial_id'],
            'transaction_type' => $data['action'],
            'quantity' => $data['quantity'],
            'enduser_id' => Auth::user()->id,
            'transaction_date' => \Carbon\Carbon::parse(\Carbon\Carbon::parse($data['transactionDate'])->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d'),
            'warehouse_id' => $data['warehouse_id'],
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
}
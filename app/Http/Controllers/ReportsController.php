<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

use App\Models\Product;
use App\Models\Lot;
use App\Models\Serial;
use App\Models\Stockcard;
use App\Models\Warehouse;

use Inertia\Inertia;

class ReportsController extends Controller
{
    public function ViewStockcard(Request $request){
        return Inertia::render('Report/Stockcard', [
            'warehouses' => Warehouse::all(),
            'products' => Product::with(['lots'])
            ->select('id','code', 'description', 'type')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->limit(5)
            ->get(),
            'lots' => Lot::select('id', 'lot')
            ->where('lot', 'Like', '%'.$request->lot.'%')
            ->limit(5)
            ->get(),
            'serials' => Serial::select('id', 'serial_no')
            ->limit(5)
            ->get(),
            'stockcards' => Stockcard::with('product')
            ->with('lot')
            ->where('product_id', $request->code)
            ->get()
            ->map(function ($product) {
                return [
                    'lot' => optional($product->lot)->lot,
                    'serial' => $product->serial_no,
                ];
            })
        ]);
    }

    public function GenerateStockcardReport(Request $request) {

        $request->validate([
            'warehouse' => 'required',
            'code' => 'required',
            'from' => 'required',
            'to' => 'required',
        ]);

        $from = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->from)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');
        $to = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->to)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');

        $product = Product::with('unit', 'lot', 'serial')->where('id', $request->code)->first();

        $totalItems = 0;
        if ($product->type === "Track by Serial") {

            $request->validate([
                'serial' => 'required',
            ]);
            
            $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse', 'user')
            ->where('product_id', $request->code)
            ->where('serial_id', $request->serial)
            ->where('warehouse_id', $request->warehouse)
            ->whereBetween('transaction_date', [$from, $to])
            ->orderBy('warehouse_id')
            ->orderBy('lot_id')
            ->orderBy('serial_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy(function ($item) {
                return $item->warehouse_id . '-' . ($item->lot_id ?? 'null') . '-' . ($item->serial_id ?? 'null');
            })
            ->flatMap(function ($group) {
                $balance = 0;

                return $group->map(function ($product) use (&$balance) {
                    if ($product->transaction_type == 'inbound') {
                        $balance += $product->quantity;
                    } else {
                        $balance -= $product->quantity;
                    }
                    return [
                        'transaction_date' => $product->transaction_date,
                        'lotNo' => optional($product->lot)->lot,
                        'serialNo' => optional($product->serial)->serial_no,
                        'expiration_date' => optional($product->lot)->expiration_date,
                        'transaction_type' => $product->transaction_type,
                        'warehouse' => $product->warehouse->warehouse,
                        'quantity' => $product->quantity,
                        'balance' => $balance,
                        'reference' => $product->reference,
                        'remarks' => $product->remarks,
                        'document_no' => $product->document_no,
                        'enduser' => $product->user->name,
                        'created_at' => $product->created_at,
                    ];
                });
            });

            if($stockcards->isEmpty())
            {
                $totalItems = 0;
            }
            else
            {
                $totalItems = StockCard::getSerialRemainingStock(
                    $request->code,
                    $request->serial,
                    $request->warehouse,
                );
            } 

        }else if($product->type === "Track by Lot"){

            $request->validate([
                'lot' => 'required',
            ]);
            
            $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse', 'user')
            ->where('product_id', $request->code)
            ->where('lot_id', $request->lot)
            ->where('warehouse_id', $request->warehouse)
            ->whereBetween('transaction_date', [$from, $to])
            ->orderBy('warehouse_id')
            ->orderBy('lot_id')
            ->orderBy('serial_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy(function ($item) {
                return $item->warehouse_id . '-' . ($item->lot_id ?? 'null') . '-' . ($item->serial_id ?? 'null');
            })
            ->flatMap(function ($group) {
                $balance = 0;

                return $group->map(function ($product) use (&$balance) {
                    if ($product->transaction_type == 'inbound') {
                        $balance += $product->quantity;
                    } else {
                        $balance -= $product->quantity;
                    }

                    return [
                        'transaction_date' => $product->transaction_date,
                        'lotNo' => optional($product->lot)->lot,
                        'serialNo' => optional($product->serial)->serial_no,
                        'expiration_date' => optional($product->lot)->expiration_date,
                        'transaction_type' => $product->transaction_type,
                        'warehouse' => $product->warehouse->warehouse,
                        'quantity' => $product->quantity,
                        'balance' => $balance,
                        'reference' => $product->reference,
                        'remarks' => $product->remarks,
                        'document_no' => $product->document_no,
                        'enduser' => $product->user->name,
                        'created_at' => $product->created_at,
                    ];
                });
            });

            if($stockcards->isEmpty())
            {
                $totalItems = 0;
            }else{
                $totalItems = StockCard::getLotRemainingStock(
                    $request->code,
                    $request->lot,
                    $request->warehouse,
                );
            } 
        }

        $stockcardSorted = $stockcards->sortBy('created_at')->values();

        return view('pdf.stockcard-pdf', 
        [   
            'stockcards' => $stockcardSorted, 
            'totalItems' => $totalItems,
            'product' => $product,
        ]);
        // return Pdf::view('pdf.stockcard-pdf',        
        // [  
        //     'stockcards' => $stockcardSorted, 
        //     'totalItems' => $totalItems,
        //     'product' => $product,
        // ])
        // ->format('a4')
        // ->landscape()
        // ->name('invoice.pdf');
    }

    public function ProductReport(Request $request){
        return Inertia::render('Report/Product', [
            'warehouses' => Warehouse::all(),
            'products' => Product::with(['lots'])
            ->select('id','code', 'description', 'type')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->limit(5)
            ->get(),
        ]);
    }

    public function GenerateProductReport(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'from' => 'required',
            'to' => 'required'
        ]);

        $from = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->from)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');
        $to = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->to)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');

        $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse', 'user')
        ->where('product_id', $request->code)
        ->whereBetween('transaction_date', [$from, $to])
        ->orderBy('warehouse_id')
        ->orderBy('lot_id')
        ->orderBy('serial_id')
        ->orderBy('created_at')
        ->orderBy('id')
        ->get()
        ->groupBy(function ($item) {
            return $item->warehouse_id . '-' . ($item->lot_id ?? 'null') . '-' . ($item->serial_id ?? 'null');
        })
        ->flatMap(function ($group) {
            $balance = 0;

            return $group->map(function ($product) use (&$balance) {
                return [
                    'code' => $product->product->code,
                    'description' => $product->product->description,
                    'transaction_date' => $product->transaction_date,
                    'lotNo' => optional($product->lot)->lot,
                    'serialNo' => optional($product->serial)->serial_no,
                    'expiration_date' => optional($product->lot)->expiration_date,
                    'transaction_type' => $product->transaction_type,
                    'warehouse' => $product->warehouse->warehouse,
                    'quantity' => $product->quantity,
                    'reference' => $product->reference,
                    'remarks' => $product->remarks,
                    'document_no' => $product->document_no,
                    'enduser' => $product->user->name,
                    'created_at' => $product->created_at,
                ];
            });
        });

        $productStock = StockCard::getProductStock(
                                    $request->code,
                                );

        $stockcardSorted = $stockcards->sortBy('created_at')->values();

        return view('pdf.warehouse-pdf', 
        [   
            'stockcards' => $stockcardSorted, 
            'stocks' => $productStock,
        ]);

    }

    public function WarehouseReport(Request $request){
        return Inertia::render('Report/Warehouse', [
            'warehouses' => Warehouse::all(),
            'products' => Product::with(['lots'])
            ->select('id','code', 'description', 'type')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->limit(5)
            ->get(),
        ]);
    }

    public function GenerateWarehouseReport(Request $request)
    {
        $request->validate([
            'warehouse' => 'required',
            'from' => 'required',
            'to' => 'required'
        ]);

        $from = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->from)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');
        $to = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->to)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');

        if(isset($request->code))
        {
            $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse', 'user')
            ->where('warehouse_id', $request->warehouse)
            ->where('product_id', $request->code)
            ->whereBetween('transaction_date', [$from, $to])
            ->orderBy('warehouse_id')
            ->orderBy('lot_id')
            ->orderBy('serial_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy(function ($item) {
                return $item->warehouse_id . '-' . ($item->lot_id ?? 'null') . '-' . ($item->serial_id ?? 'null');
            })
            ->flatMap(function ($group) {
                $balance = 0;

                return $group->map(function ($product) use (&$balance) {
                    return [
                        'code' => $product->product->code,
                        'description' => $product->product->description,
                        'transaction_date' => $product->transaction_date,
                        'lotNo' => optional($product->lot)->lot,
                        'serialNo' => optional($product->serial)->serial_no,
                        'expiration_date' => optional($product->lot)->expiration_date,
                        'transaction_type' => $product->transaction_type,
                        'warehouse' => $product->warehouse->warehouse,
                        'quantity' => $product->quantity,
                        'reference' => $product->reference,
                        'remarks' => $product->remarks,
                        'document_no' => $product->document_no,
                        'enduser' => $product->user->name,
                        'created_at' => $product->created_at,
                    ];
                });
            });

            $warehouseProductStock = StockCard::getProductWarehouseStock(
                                        $request->code,
                                        $request->warehouse,
                                    );
        }else{
            $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse', 'user')
            ->where('warehouse_id', $request->warehouse)
            ->whereBetween('transaction_date', [$from, $to])
            ->orderBy('warehouse_id')
            ->orderBy('lot_id')
            ->orderBy('serial_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy(function ($item) {
                return $item->warehouse_id . '-' . ($item->lot_id ?? 'null') . '-' . ($item->serial_id ?? 'null');
            })
            ->flatMap(function ($group) {
                $balance = 0;

                return $group->map(function ($product) use (&$balance) {
                    return [
                        'code' => $product->product->code,
                        'description' => $product->product->description,
                        'transaction_date' => $product->transaction_date,
                        'lotNo' => optional($product->lot)->lot,
                        'serialNo' => optional($product->serial)->serial_no,
                        'expiration_date' => optional($product->lot)->expiration_date,
                        'transaction_type' => $product->transaction_type,
                        'warehouse' => $product->warehouse->warehouse,
                        'quantity' => $product->quantity,
                        'reference' => $product->reference,
                        'remarks' => $product->remarks,
                        'document_no' => $product->document_no,
                        'enduser' => $product->user->name,
                        'created_at' => $product->created_at,
                    ];
                });
            });

            $warehouseProductStock = StockCard::getWarehouseStock(
                                        $request->warehouse,
                                    );
        }

        $stockcardSorted = $stockcards->sortBy('created_at')->values();

        return view('pdf.warehouse-pdf', 
        [   
            'stockcards' => $stockcardSorted, 
            'stocks' => $warehouseProductStock,
        ]);
    }

    public function ViewSummary() {
        return Inertia::render('Report/Summary');
    }

    public function GenerateSummary(Request $request)
    {
        $request->validate(
            [
                'from' => 'required',
                'to' => 'required',
            ]
        );

        $from = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->from)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');
        $to = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->to)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');

        $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse', 'user')
        ->whereBetween('transaction_date', [$from, $to])
        ->orderBy('warehouse_id')
        ->orderBy('lot_id')
        ->orderBy('serial_id')
        ->orderBy('created_at')
        ->orderBy('id')
        ->get()
        ->groupBy(function ($item) {
            return $item->warehouse_id . '-' . ($item->lot_id ?? 'null') . '-' . ($item->serial_id ?? 'null');
        })
        ->flatMap(function ($group) {
            $balance = 0;

            return $group->map(function ($product) use (&$balance) {
                return [
                    'code' => $product->product->code,
                    'description' => $product->product->description,
                    'transaction_date' => $product->transaction_date,
                    'lotNo' => optional($product->lot)->lot,
                    'serialNo' => optional($product->serial)->serial_no,
                    'expiration_date' => optional($product->lot)->expiration_date,
                    'transaction_type' => $product->transaction_type,
                    'warehouse' => $product->warehouse->warehouse,
                    'quantity' => $product->quantity,
                    'reference' => $product->reference,
                    'remarks' => $product->remarks,
                    'document_no' => $product->document_no,
                    'enduser' => $product->user->name,
                    'created_at' => $product->created_at,
                ];
            });
        });

        $stockcardSorted = $stockcards->sortBy('created_at')->values();

        return view('pdf.summary-pdf', 
        [   
            'stockcards' => $stockcardSorted, 
        ]);
    }
}

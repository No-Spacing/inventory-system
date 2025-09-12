<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Exports\StockcardExport;
use App\Exports\ProductExport;
use App\Exports\SummaryExport;
use App\Exports\WarehouseExport;

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
            
            $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse')
            ->where('product_id', $request->code)
            ->where('serial_id', $request->serial)
            ->where('warehouse_id', $request->warehouse)
            ->orderBy('warehouse_id')
            ->orderBy('lot_id')
            ->orderBy('serial_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy(function ($item) {
                return $item->warehouse_id . '-' . ($item->lot_id ?? 'null') . '-' . ($item->serial_id ?? 'null');
            })
            ->flatMap(function ($group) use ($from) {
                // Calculate opening balance before the 'from' date
                $openingBalance = $group->filter(function ($product) use ($from) {
                    return $product->transaction_date < $from;
                })->reduce(function ($carry, $product) {
                    return $carry + ($product->transaction_type === 'inbound' ? $product->quantity : -$product->quantity);
                }, 0);

                $runningBalance = $openingBalance;

                return $group->filter(function ($product) use ($from) {
                    return $product->transaction_date >= $from;
                })->map(function ($product) use (&$runningBalance) {
                    if ($product->transaction_type === 'inbound') {
                        $runningBalance += $product->quantity;
                    } else {
                        $runningBalance -= $product->quantity;
                    }

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
                        'balance' => $runningBalance,
                        'reference' => $product->reference,
                        'remarks' => $product->remarks,
                        'document_no' => $product->document_no,
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
            
            $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse')
            ->where('product_id', $request->code)
            ->where('lot_id', $request->lot)
            ->where('warehouse_id', $request->warehouse)
            ->orderBy('warehouse_id')
            ->orderBy('lot_id')
            ->orderBy('serial_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy(function ($item) {
                return $item->warehouse_id . '-' . ($item->lot_id ?? 'null') . '-' . ($item->serial_id ?? 'null');
            })
            ->flatMap(function ($group) use ($from) {
                $openingBalance = $group->filter(function ($product) use ($from) {
                    return $product->transaction_date < $from;
                })->reduce(function ($carry, $product) {
                    return $carry + ($product->transaction_type === 'inbound' ? $product->quantity : -$product->quantity);
                }, 0);

                $runningBalance = $openingBalance;

                return $group->filter(function ($product) use ($from) {
                    return $product->transaction_date >= $from;
                })->map(function ($product) use (&$runningBalance) {
                    if ($product->transaction_type === 'inbound') {
                        $runningBalance += $product->quantity;
                    } else {
                        $runningBalance -= $product->quantity;
                    }

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
                        'balance' => $runningBalance,
                        'reference' => $product->reference,
                        'remarks' => $product->remarks,
                        'document_no' => $product->document_no,
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

        $filteredResults = $stockcards->filter(function ($item) use ($from, $to) {
            return $item['transaction_date'] >= $from && $item['transaction_date'] <= $to;
        });

        $stockcardSorted = $filteredResults->sortBy('created_at')->values();

        if($request->action == "pdf")
        {
            // return view('pdf.stockcard-pdf', 
            // [   
            //     'stockcards' => $stockcardSorted, 
            //     'totalItems' => $totalItems,
            //     'product' => $product,
            // ]);
            $pdf = Pdf::loadView('pdf.stockcard-pdf', [
                'stockcards' => $stockcardSorted, 
                'totalItems' => $totalItems,
                'product' => $product,
            ]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('stockcard.pdf');
        }

        if($request->action == "excel")
        {
            // return Excel::download(new StockcardExport, 'stockcard.xlsx');
            return Excel::download(new StockcardExport($stockcardSorted), 'stockcard_report.xlsx');
        }
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

        $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse')
            ->where('product_id', $request->code)
            ->orderBy('warehouse_id')
            ->orderBy('lot_id')
            ->orderBy('serial_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy(function ($item) {
                return $item->warehouse_id . '-' . ($item->lot_id ?? 'null') . '-' . ($item->serial_id ?? 'null');
            })
            ->flatMap(function ($group) use ($from) {
                $openingBalance = $group->filter(function ($product) use ($from) {
                    return $product->transaction_date < $from;
                })->reduce(function ($carry, $product) {
                    return $carry + ($product->transaction_type === 'inbound' ? $product->quantity : -$product->quantity);
                }, 0);

                $runningBalance = $openingBalance;

                return $group->filter(function ($product) use ($from) {
                    return $product->transaction_date >= $from;
                })->map(function ($product) use (&$runningBalance) {
                    if ($product->transaction_type === 'inbound') {
                        $runningBalance += $product->quantity;
                    } else {
                        $runningBalance -= $product->quantity;
                    }

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
                        'balance' => $runningBalance,
                        'reference' => $product->reference,
                        'remarks' => $product->remarks,
                        'document_no' => $product->document_no,
                        'created_at' => $product->created_at,
                    ];
                });
            });

        $productStock = StockCard::getProductStock(
                                    $request->code,
                                );

        $stockcardSorted = $stockcards->sortBy('created_at')->values();

        if($request->action == 'pdf')
        {
            // return view('pdf.warehouse-pdf', 
            // [   
            //     'stockcards' => $stockcardSorted, 
            //     'stocks' => $productStock,
            // ]);

            $pdf = Pdf::loadView('pdf.warehouse-pdf', [
                'stockcards' => $stockcardSorted, 
                'stocks' => $productStock,
            ]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('product.pdf');
            
        }

        if($request->action == 'excel')
        {
            return Excel::download(new ProductExport($stockcardSorted), 'product.xlsx');
        }   
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
      
        $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse')
        ->when($request->warehouse, function ($query, $warehouse) {
            return $query->where('warehouse_id', $warehouse);
        })
        ->when($request->code, function ($query, $code) {
            return $query->where('product_id', $code);
        })
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
                    'unit' => $product->product->unit->unit,
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
                    'created_at' => $product->created_at,
                ];
            });
        });

        $warehouseProductStock = StockCard::getWarehouseStock(
                                    $request->warehouse,
                                    $request->code,
                                );

        $stockcardSorted = $stockcards->sortBy('created_at')->values();

        if($request->action == 'pdf'){
            return view('pdf.warehouse-pdf', 
            [   
                'stockcards' => $stockcardSorted, 
                'stocks' => $warehouseProductStock,
            ]);
        }

        if($request->action == 'excel')
        {
            return Excel::download(new WarehouseExport($stockcardSorted), 'warehouse.xlsx');
        }


        
        // $pdf = Pdf::loadView('pdf.warehouse-pdf', [
        //     'stockcards' => $stockcardSorted, 
        //     'stocks' => $warehouseProductStock,
        // ]);
        // $pdf->setPaper('A4', 'landscape');
        // return $pdf->stream('warehouse.pdf');
        
    }

    public function ViewSummary() {
        return Inertia::render('Report/Summary');
    }

    public function GenerateSummary(Request $request)
    {
        // $request->validate(
        //     [
        //         'from' => 'required',
        //         'to' => 'required',
        //     ]
        // );

        // $from = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->from)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');
        // $to = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->to)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');

        $stockcards = Stockcard::with('product.unit', 'lot', 'serial', 'warehouse')
        ->orderBy('warehouse_id')
        ->orderBy('lot_id')
        ->orderBy('serial_id')
        ->orderBy('created_at')
        ->orderBy('id')
        ->get()
        ->groupBy(function ($item) {
            return implode('-', [
                $item->warehouse_id,
                $item->product_id,
                $item->lot_id ?? 'null',
            ]);
        })
        ->map(function ($group) {
            $first = $group->first();

            // Filter for only serials with positive balance
            $filteredSerials = $group->groupBy('serial_id')->filter(function ($serialGroup) {
                $netQty = $serialGroup->sum(function ($item) {
                    return $item->transaction_type === 'inbound'
                        ? $item->quantity
                        : -$item->quantity;
                });
                return $netQty > 0;
            });

            $serials = $filteredSerials->pluck('*.serial.serial_no')
                ->flatten()
                ->unique()
                ->implode(', ');

            $net_quantity = $filteredSerials->flatten()->sum(function ($item) {
                return $item->transaction_type === 'inbound'
                    ? $item->quantity
                    : -$item->quantity;
            });

            return [
                'code' => $first->product->code,
                'description' => $first->product->description,
                'unit' => $first->product->unit->unit,
                'lotNo' => optional($first->lot)->lot,
                'serialNo' => $serials ?: 'None',
                'expiration_date' => optional($first->lot)->expiration_date,
                'warehouse' => $first->warehouse->warehouse,
                'total_quantity' => $net_quantity,
            ];
        })->filter(fn($item) => $item['total_quantity'] > 0);

        // $stockcardSorted = $stockcards->sortBy('created_at')->values();

        if($request->action == "pdf")
        {
            return view('pdf.summary-pdf', 
            [   
                'stockcards' => $stockcards, 
            ]);

            // $pdf = Pdf::loadView('pdf.summary-pdf', [
            //     'stockcards' => $stockcardSorted, 
            // ]);
            // $pdf->setPaper('A4', 'landscape');
            // return $pdf->stream('sumarry.pdf');
        }

        if($request->action == "excel")
        {
            // return Excel::download(new StockcardExport, 'stockcard.xlsx');
            return Excel::download(new SummaryExport($stockcards), 'summary.xlsx');
        }

        // return view('pdf.summary-pdf', 
        // [   
        //     'stockcards' => $stockcardSorted, 
        // ]);
        // return Pdf::view('pdf.warehouse-pdf',        
        // [  
                // 'stockcards' => $stockcardSorted, 
        // ])
        // ->format('a4')
        // ->landscape()
        // ->name('invoice.pdf');
    }

    
public function SerialReport(Request $request){
    return Inertia::render('Report/Serial', [
        'warehouses' => Warehouse::all(),
        'products' => Product::with(['lots'])
        ->select('id','code', 'description', 'type')
        ->where('code', 'Like', '%'.$request->code.'%')
        ->where('type', 'Track by Serial')
        ->limit(5)
        ->get(),
    ]);
}

public function GenerateSerialReport(Request $request)
{
    $request->validate([
        'from' => 'required',
        'to' => 'required'
    ]);

    $from = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->from)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');
    $to = \Carbon\Carbon::parse(\Carbon\Carbon::parse($request->to)->setTimezone('Asia/Manila')->toIso8601String())->format('Y-m-d');

    $stockcards = Stockcard::with('product', 'serial', 'warehouse')
    ->where('warehouse_id', $request->warehouse)
    ->whereBetween('transaction_date', [$from, $to])
    ->whereHas('product', function ($query) {
        $query->where('type', 'Track by Serial');
    })
    ->orderBy('warehouse_id')
    ->orderBy('lot_id')
    ->orderBy('serial_id')
    ->orderBy('created_at')
    ->orderBy('id')
    ->get()
    ->map(function ($item) {
        return [
            'code' => $item->product->code,
            'description' => $item->product->description,
            'transaction_date' => $item->transaction_date,
            'serialNo' => optional($item->serial)->serial_no,
            'transaction_type' => $item->transaction_type,
            'warehouse' => $item->warehouse->warehouse,
            'quantity' => $item->quantity,
            'reference' => $item->reference,
            'remarks' => $item->remarks,
            'document_no' => $item->document_no,
            'created_at' => $item->created_at,
        ];
    });

    $stockcardSorted = $stockcards->sortBy('created_at')->values();

    return view('pdf.serial', 
    [   
        'stockcards' => $stockcardSorted, 
        'from' => $from,
        'to' => $to,
    ]);
    // if($request->action == "pdf")
    // {
    //     $pdf = Pdf::loadView('pdf.serial', [
    //         'stockcards' => $compiled, 
    //     ]);
    //     $pdf->setPaper('A4', 'landscape');
    //     return $pdf->stream('serial.pdf');
    // }

    // if($request->action == "excel")
    // {
    //     // return Excel::download(new StockcardExport, 'stockcard.xlsx');
    //     return Excel::download(new SummaryExport($compiled), 'summary.xlsx');
    // }

}

}

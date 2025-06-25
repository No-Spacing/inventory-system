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
    public function IndexStockcard(Request $request){
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

    public function ViewStockcardPdf(Request $request) {

        $product = Product::with('unit', 'lot', 'serial')->where('id', $request->code)->first();

        $totalItems = 0;
        if ($request->filled('serial')) {
            $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse', 'user')
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
            ->flatMap(function ($group) {
                $balance = 0;

                return $group->map(function ($product) use (&$balance) {
                    $balance += $product->quantity;

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
                    ];
                });
            });
            $totalItems = StockCard::getSerialRemainingStock(
                $request->code,
                $request->serial,
                $request->warehouse,
            );
        }else if($request->filled('lot')){

            $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse', 'user')
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
                    ];
                });
            });

            $totalItems = StockCard::getLotRemainingStock(
                $request->code,
                $request->lot,
                $request->warehouse,
            );
        }else{
            if(isset($request->warehouse)){
                $balance = 0;
                $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse', 'user')
                ->where('product_id', $request->code)
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
                        ];
                    });
                });

                $totalItems = StockCard::getRemainingStock(
                    $request->code,
                    $request->warehouse,
                );
            }else{
                $balance = 0;
                $stockcards = Stockcard::with('product', 'lot', 'serial', 'warehouse', 'user')
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
                        ];
                    });
                });

                $totalItems = StockCard::getRemainingStock(
                    $request->code,
                );
            }

        }

        $stockcardSorted = $stockcards->sortBy('transaction_date')->values();

        return view('pdf.stockcard-pdf', 
        [   'stockcards' => $stockcardSorted, 
            'totalItems' => $totalItems,
            'product' => $product,
        ]);
        // return Pdf::view('pdf.stockcard-pdf', ['stockcards' => $stockcards])
        // ->format('a4')
        // ->landscape()
        // ->name('invoice.pdf');
    }


    public function GenerateStockcardPdf(){
        $stockcards = Stockcard::with('product')
        ->where('product_id', $request->code)
        ->get();
        
        return Pdf::view('pdf.stockcard-pdf', ['stockcards' => $stockcards])
        ->format('a4')
        ->landscape()
        ->name('invoice.pdf');
    }

    public function IndexSummary() {
        return Inertia::render('Report/Summary');
    }
}

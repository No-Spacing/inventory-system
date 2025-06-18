<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

use App\Models\Product;
use App\Models\Lot;
use App\Models\Serial;
use App\Models\Stockcard;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function IndexStockcard(Request $request){
        return Inertia::render('Admin/Report/Stockcard', [
            'products' => Product::select('id','code', 'description', 'type')
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
        if ($request->filled('serial')) {
            $stockcards = Stockcard::with('product', 'lot', 'serial')
            ->where('product_id',$request->code)
            ->where('serial_id', $request->serial)
            ->get()
            ->map(function ($product) {
                return [
                    'transaction_date' => $product->transaction_date,
                    'lotNo' => optional($product->lot)->lot,
                    'serialNo' => optional($product->serial)->serial_no,
                    'transaction_type' => $product->transaction_type,
                    'warehouse' => $product->warehouse,
                    'quantity' => $product->quantity,
                    'balance' => $product->item_balance,
                    'reference' => $product->reference,
                    'remarks' => $product->remarks,
                    'document_no' => $product->document_no
                ];
            });
        }else if($request->filled('lot')){
            $stockcards = Stockcard::with('product', 'lot', 'serial')
            ->where('product_id', $request->code)
            ->where('lot_id', $request->lot)
            ->get()
            ->map(function ($product) {
                return [
                    'transaction_date' => $product->transaction_date,
                    'lotNo' => optional($product->lot)->lot,
                    'serialNo' => optional($product->serial)->serial_no,
                    'transaction_type' => $product->transaction_type,
                    'warehouse' => $product->warehouse,
                    'quantity' => $product->quantity,
                    'balance' => $product->item_balance,
                    'reference' => $product->reference,
                    'remarks' => $product->remarks,
                    'document_no' => $product->document_no
                ];
            });
        }else{
            $stockcards = Stockcard::with('product', 'lot', 'serial')
            ->where('product_id', $request->code)
            ->get()
            ->map(function ($product) {
                return [
                    'transaction_date' => $product->transaction_date,
                    'lotNo' => optional($product->lot)->lot,
                    'serialNo' => optional($product->serial)->serial_no,
                    'transaction_type' => $product->transaction_type,
                    'warehouse' => $product->warehouse,
                    'quantity' => $product->quantity,
                    'balance' => $product->item_balance,
                    'reference' => $product->reference,
                    'remarks' => $product->remarks,
                    'document_no' => $product->document_no 
                ];
            });
        }

        return view('pdf.stockcard-pdf', ['stockcards' => $stockcards]);
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
        return Inertia::render('Admin/Report/Summary');
    }
}

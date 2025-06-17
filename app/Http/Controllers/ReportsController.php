<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

use App\Models\Product;
use App\Models\Lot;
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
            'lots' => Lot::select('id','lot')
            ->where('lot', 'Like', '%'.$request->lot.'%')
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
        $stockcards = Stockcard::with('product')
        ->with('lot')
        ->where('product_id', $request->code)
        ->get()
        ->map(function ($product) {
            return [
                'transaction_date' => $product->transaction_date,
                'lotNo' => optional($product->lot)->lot,
                'serialNo' => $product->serial_no,
                'transaction_type' => $product->transaction_type,
                'quantity' => $product->quantity,
                'balance' => $product->item_balance,
                'reference' => $product->reference,
                'remarks' => $product->remarks,
            ];
        });
        
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

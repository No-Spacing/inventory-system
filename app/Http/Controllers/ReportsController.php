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
            ->whereHas('product', function ($query) use ($request) {
                $query->where('code', 'LIKE', '%'.$request->code.'%');
            })
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'serial' => $product->serial_no,
                    'lot' => optional($product->lots)->lot,
                ];
            }),
        ]);
    }

    public function IndexPdf(Request $request) {
        dd($request->code);
        // return view('pdf.stockcard-pdf');
    }

    public function GenerateStockcardPdf(){
        return Pdf::view('pdf.invoice', ['invoice' => 'invoice'])
            ->format('a4')
            ->landscape()
            ->name('invoice.pdf');
    }

    public function IndexSummary() {
        return Inertia::render('Admin/Report/Summary');
    }
}

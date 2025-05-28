<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

use App\Models\Product;
use App\Models\Lot;

use Inertia\Inertia;

class ReportsController extends Controller
{
    public function IndexStockcard(Request $request){
        return Inertia::render('Admin/Report/Stockcard', [
            'products' => Product::select('id','code')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->limit(5)
            ->get(),
            'lots' => Lot::select('id','lot')
            ->where('lot', 'Like', '%'.$request->lot.'%')
            ->limit(5)
            ->get(),
        ]);
    }

    public function IndexPdf(Request $request) {
        // $request->validate([
        // ]);
        return view('pdf.invoice');
    }

    public function GenerateStockcardPdf(){
        return Pdf::view('pdf.invoice', ['invoice' => 'invoice'])
            ->format('a4')
            ->landscape()
            ->name('invoice.pdf');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class ReportsController extends Controller
{
    public function index() {
        return view('pdf.invoice');
    }

    public function GenerateStockcardPdf(){
         return redirect('index-pdf');
        Pdf::view('pdf.invoice')
        ->format('a4')
        ->name('invoice.pdf');
    }
}

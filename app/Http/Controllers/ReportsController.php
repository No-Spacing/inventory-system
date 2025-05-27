<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class ReportsController extends Controller
{
    public function index() {
        return view('pdf.invoice', ['items' => 'item']);
    }

    public function GenerateStockcardPdf(){
        Pdf::view('pdf.invoice', ['invoice' => 'Items']);
    }
}

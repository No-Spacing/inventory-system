<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

use App\Http\Controllers\UserController;
use App\Http\Controllers\StockcardController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportsController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Model\User;
use App\Policies\UserPolicy;

Route::get('pr-pdf', function () {
    return view('pdf.pr-pdf');
});

Route::get('stock-pdf', function () {
    return view('pdf.stock-pdf');
});

Route::get('stockcard-pdf', function () {
    return view('pdf.stockcard-pdf');
});

Route::middleware(['user-group'])->group(function () {
    Route::inertia('/login', 'Auth/Login');

    Route::controller(StockcardController::class)->group(function () {
        Route::get('/dashboard', 'Dashboard');
        
        Route::get('/in', 'In');
        Route::post('/create-stockcard-in', 'CreateStockcardIn');

        Route::get('out', 'Out');
        Route::post('/create-stockcard-out', 'CreateStockcardOut');

    });

    Route::controller(ReportsController::class)->group(function () {
        Route::get('/stockcard', 'IndexStockcard');

        Route::get('/generate-stockcard-pdf', 'GenerateStockcardPdf')->name('show.stockcard.pdf');
        Route::post('index-pdf', 'IndexPdf');
    });

    // Route::inertia('/stockcard', 'Admin/Report/Stockcard');

    Route::inertia('/stocks', 'Admin/Report/Stocks');

    Route::inertia('/stocks_with_pr', 'Admin/Report/Stocks_Pr_no');

    Route::inertia('/product_expiry', 'Admin/Report/ProductExpiry');

    Route::inertia('/summary', 'Admin/Report/Summary');

    Route::inertia('/maintenance', 'Admin/Maintenance')->can('view', User::class);

    // Maintenance routes
    Route::inertia('/units', 'Admin/Maintenance/Units')->can('view', User::class);

    Route::controller(MaintenanceController::class)->group(function () {
        Route::get('/category', 'IndexCategory');
        Route::post('/store-category', 'StoreCategory');

        Route::get('/units', 'IndexUnit');
        Route::post('/store-unit', 'StoreUnit');

        Route::get('/product', 'IndexProduct');
        Route::post('/store-product', 'StoreProduct');

        Route::get('/lot', 'IndexLot');
        Route::post('/store-lot', 'StoreLot');
    });

    // Route::get('/category', [CategoryController::class, 'index'])->can('view', User::class)->name('products.index');
    // Route::post('/category/store', [CategoryController::class, 'store'])->can('view', User::class)->name('products.create');

    // Route::inertia('/product', 'Admin/Maintenance/Product')->can('view', User::class);
    // Route::inertia('/lot', 'Admin/Maintenance/Lot')->can('view', User::class);
    Route::inertia('/serial', 'Admin/Maintenance/Serial')->can('view', User::class);

    Route::inertia('/forgot-password', 'Auth/ForgotPassword');

    Route::get('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    });

});

Route::post('/submit-login', [UserController::class, 'SubmitLogin']);

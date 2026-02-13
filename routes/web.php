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
    Route::inertia('/', 'Auth/Login');

    Route::controller(StockcardController::class)->group(function () {
        Route::get('/dashboard', 'Dashboard');
        
        Route::get('/transaction', 'Transaction');
        Route::post('/import-stockcard', 'ImportStockcard');
        Route::post('/stockcard-data', 'StockcardData');

        Route::post('/submit-inbound', 'SubmitInbound');
        Route::post('/submit-outbound', 'SubmitOutbound');
        Route::post('/submit-transfer', 'SubmitTransfer');

        Route::get('/receive', 'IndexReceive');
        Route::patch('/received', 'Received');
    });

    Route::controller(ReportsController::class)->group(function () {
        Route::get('/stockcard', 'StockcardReport');
        Route::post('/generate-stockcard-report', 'GenerateStockcardReport');

        Route::get('/serial-report', 'SerialReport');
        Route::post('/generate-serial-report', 'GenerateSerialReport');

        Route::get('/product-report', 'ProductReport');
        Route::post('/generate-product-report', 'GenerateProductReport');

        Route::get('/warehouse-report', 'WarehouseReport');
        Route::post('/generate-warehouse-report', 'GenerateWarehouseReport');

        Route::get('/summary', 'ViewSummary');
        Route::post('/generate-summary', 'GenerateSummary');

        Route::get('/excel', 'exportToExcel');
    });

    // Maintenance routes

    Route::controller(MaintenanceController::class)->group(function () {
        Route::get('/units', 'ViewUnit')->can('view', User::class);
        Route::post('/store-unit', 'StoreUnit')->can('create', User::class);
        Route::patch('/update-unit', 'UpdateUnit')->can('update', User::class);
        Route::delete('/delete-unit', 'DeleteUnit')->can('delete', User::class);

        Route::get('/category', 'ViewCategory')->can('view', User::class);
        Route::post('/store-category', 'StoreCategory')->can('create', User::class);
        Route::patch('/update-category', 'UpdateCategory')->can('update', User::class);
        Route::delete('/delete-category', 'DeleteCategory')->can('delete', User::class);

        Route::get('/product', 'ViewProduct')->can('view', User::class);
        Route::post('/store-product', 'StoreProduct')->can('create', User::class);
        Route::post('/import-product', 'ImportProduct')->can('create', User::class);
        Route::patch('/update-product', 'UpdateProduct')->can('update', User::class);
        Route::delete('/delete-product', 'DeleteProduct')->can('delete', User::class);

        Route::get('/lot', 'ViewLot')->can('view', User::class);
        Route::post('/store-lot', 'StoreLot')->can('create', User::class);
        Route::post('/import-lot', 'ImportLot')->can('create', User::class);
        Route::patch('/update-lot', 'UpdateLot')->can('update', User::class);
        Route::delete('/delete-lot', 'DeleteLot')->can('delete', User::class);

        Route::get('/serial', 'ViewSerial')->can('view', User::class);
        Route::post('/store-serial', 'StoreSerial')->can('create', User::class);
        Route::post('/store-multiple-serial', 'StoreMultipleSerial')->can('create', User::class);
        Route::post('/import-serial', 'ImportSerial')->can('create', User::class);
        Route::patch('/update-serial', 'UpdateSerial')->can('update', User::class);
        Route::delete('/delete-serial', 'DeleteSerial')->can('delete', User::class);

        Route::get('/warehouse', 'ViewWarehouse')->can('view', User::class);
        Route::post('/store-warehouse', 'StoreWarehouse')->can('view', User::class);

        Route::get('/users', 'ViewUsers')->can('viewUser', User::class);
        Route::post('/store-user', 'StoreUser')->can('addUser', User::class);

        Route::get('/audit-trail', 'AuditTrail')->can('view', User::class);
    });

    Route::inertia('/forgot-password', 'Auth/ForgotPassword');

    Route::get('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    });

});

Route::post('/submit-login', [UserController::class, 'SubmitLogin']);

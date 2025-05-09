<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Model\User;
use App\Policies\UserPolicy;

Route::middleware(['user-group'])->group(function () {
    Route::inertia('/login', 'Auth/Login');

    Route::get('/dashboard', [UserController::class, 'Dashboard']);

    Route::get('/in', [UserController::class, 'In']);

    Route::get('/out', [UserController::class, 'Out']);

    Route::inertia('/stockcard', 'Admin/Report/Stockcard');

    Route::inertia('/stocks', 'Admin/Report/Stocks');

    Route::inertia('/stocks_with_pr', 'Admin/Report/Stocks_Pr_no');

    Route::inertia('/product_expiry', 'Admin/Report/ProductExpiry');

    Route::inertia('/summary', 'Admin/Report/Summary');

    Route::inertia('/maintenance', 'Admin/Maintenance')->can('view', User::class);

    // Maintenance routes
    Route::inertia('/units', 'Admin/Maintenance/Units')->can('view', User::class);
    Route::inertia('/category', 'Admin/Maintenance/Category')->can('view', User::class);
    Route::inertia('/lot', 'Admin/Maintenance/Lot')->can('view', User::class);
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

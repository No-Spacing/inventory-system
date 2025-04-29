<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Model\User;

Route::middleware(['user-group'])->group(function () {
    Route::inertia('/login', 'Login');

    Route::get('/dashboard', [UserController::class, 'Dashboard']);

    Route::get('/in', [UserController::class, 'In']);

    Route::get('/out', [UserController::class, 'Out']);

    Route::inertia('/reports', 'Reports');

    Route::inertia('/maintenance', 'Maintenance')->can('view', User::class);

    // Maintenance routes
    Route::inertia('/units', 'maintenance/Units')->can('view', User::class);
    Route::inertia('/category', 'maintenance/Category')->can('view', User::class);
    Route::inertia('/lot', 'maintenance/Lot')->can('view', User::class);
    Route::inertia('/serial', 'maintenance/Serial')->can('view', User::class);

    Route::get('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    });

});

Route::post('/submit-login', [UserController::class, 'SubmitLogin']);

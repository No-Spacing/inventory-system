<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Model\User;

Route::middleware(['user-group'])->group(function () {
    Route::inertia('/login', 'Login');

    Route::inertia('/dashboard', 'Dashboard');

    Route::inertia('/in', 'In');

    Route::inertia('/out', 'Out');

    Route::inertia('/reports', 'Reports');

    Route::inertia('/maintenance', 'Maintenance')->can('view', User::class);

    Route::get('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    });

});

Route::post('/submit-login', [UserController::class, 'SubmitLogin']);

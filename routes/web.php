<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



Route::middleware(['user-group'])->group(function () {
    Route::inertia('/login', 'Login');    

    Route::inertia('/maintenance', 'Maintenance');

    Route::inertia('/in', 'In');

    Route::inertia('/out', 'Out');

    Route::get('/logout', function (Request $request) {
        Auth::logout();
 
        $request->session()->invalidate();
     
        $request->session()->regenerateToken();
     
        return redirect('/login');
    });

});

Route::post('/submit-login', [UserController::class, 'SubmitLogin']);

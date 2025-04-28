<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

use App\Http\Controllers\UserController;


Route::inertia('/in', 'In');

Route::inertia('/out', 'Out');

Route::middleware(['user-group'])->group(function () {
    Route::inertia('/login', 'Login');    

    Route::inertia('/maintenance', 'Maintenance');
});

Route::post('/submit-login', [UserController::class, 'SubmitLogin']);

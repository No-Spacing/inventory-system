<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::inertia('/login', 'Login');

Route::post('/submit-login', [UserController::class, 'SubmitLogin']);

Route::inertia('/in', 'In');

Route::inertia('/out', 'Out');

Route::inertia('/maintenance', 'Maintenance');
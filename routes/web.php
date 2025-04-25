<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

Route::get('/', function () {
    return view('welcome');
});

Route::inertia('/login', 'Login');

Route::inertia('/in', 'In');

Route::inertia('/out', 'Out');

Route::inertia('/maintenance', 'Maintenance');
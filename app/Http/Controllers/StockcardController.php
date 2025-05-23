<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Inertia\Inertia;

use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Stockcard;
use App\Models\Lot;


class StockcardController extends Controller
{
    public function Dashboard(){
        return Inertia::render('Admin/Dashboard', [
            'users' => User::with('role')->get(),
        ]);
    }

    public function In(Request $request){
        return Inertia::render('Admin/Transaction/In', [
            'users' => User::with('role')->get(),
            'stockcards' => Stockcard::all(),
            'products' => Product::all(),
            'lots' => Lot::all(),
        ]);
    }

    public function Out(Request $request){
        return Inertia::render('Admin/Transaction/Out', [
            'users' => User::with('role')->get(),
            'stockcards' => Stockcard::all(),
            'products' => Product::all(),
        ]);
    }
}

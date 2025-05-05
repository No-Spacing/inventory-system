<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;

use Illuminate\Support\Facades\DB;

use Inertia\Inertia;

class UserController extends Controller
{
    //
    public function SubmitLogin(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            return redirect('/dashboard');
        } else {
            return back()->with(['message' => 'Invalid Credentials',]);
        }
    }

    public function Dashboard(){
        return Inertia::render('Dashboard', [
            'users' => User::with('role')->get(),
        ]);
    }

    public function In(Request $request){

        $products = DB::table('product')
                    ->when($request->code, function($query, $filter){
                        $query->where('code', 'Like', '%'.$filter.'%');
                    });

        return Inertia::render('In', [
            'users' => User::with('role')->get(),
            'product' => $products->select('code')->limit(5)->get(),
        ]);
    }

    public function Out(){
        return Inertia::render('In', [
            'users' => User::with('role')->get(),
        ]);
    }
}

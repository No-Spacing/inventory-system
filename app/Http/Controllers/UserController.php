<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;

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

    public function In(){
        return Inertia::render('In', [
            'users' => User::with('role')->get(),
        ]);
    }

    public function Out(){
        return Inertia::render('In', [
            'users' => User::with('role')->get(),
        ]);
    }
}

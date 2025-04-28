<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    //
    public function SubmitLogin(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            if(Auth::user()->role_id == Role::IS_ADMIN || Auth::user()->role_id == Role::IS_SUPERVISOR){
                return redirect('/maintenance');
            }else {   
                return redirect('/in');
            }
        } else {
            return back()->with(['message' => 'Invalid Credentials',]);
        }
    }
}

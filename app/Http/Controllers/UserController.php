<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //
    public function SubmitLogin(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
    }
}

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
        return Inertia::render('Admin/Dashboard', [
            'users' => User::with('role')->get(),
        ]);
    }

    public function In(Request $request){
    
        return Inertia::render('Admin/Transaction/In', [
            'users' => User::with('role')->get(),
            'stockcards' => DB::table('stockcard')->where('stocktype', 'in')->join('product', 'product.id', '=', 'stockcard.code')->get(),
            'products' => DB::table('product')
            ->select('code','product_desc','unit', 'quantity')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->limit(5)
            ->get(),
        ]);

    }

    public function Out(Request $request){
        return Inertia::render('Admin/Transaction/Out', [
            'users' => User::with('role')->get(),
            'stockcards' => DB::table('stockcard')->where('stocktype', 'out')->join('product', 'product.id', '=', 'stockcard.code')->get(),
            'products' => DB::table('product')
            ->select('code','product_desc','unit', 'quantity')
            ->where('code', 'Like', '%'.$request->code.'%')
            ->limit(5)
            ->get(),
        ]);
    }

    public function SubmitPassword(Request $request){
        
    }
}

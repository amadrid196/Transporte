<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role == "admin"){
            if(session()->has('url.intended'))
                Session::forget('url.intended');
            return $next($request);
        }
        else{
            Session::put('url.intended', url()->current());
            return $next($request);
            // return redirect()->intended('login');
            // return abort(401);
        }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {    
        // if user is not logged in
        if (!Auth::check()){
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        // Super Admin
        if ($userRole == 'superadmin'){
            return redirect()->route('superadmin.dashboard');
            
        }
        
        // Admin
        elseif ($userRole == 'admin'){
            return $next($request);
        }
        
        // Normal User
        elseif ($userRole == 'user'){
            return redirect()->route('dashboard');
        }
    
    }
}

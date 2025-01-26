<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



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

        // Fetch user's id_pt
        $id_pt = Auth::user()->id_pt;

        // Check if the user's image exists in the database
        $image = DB::table('public.tbl_pt')
        ->where('id_pt', $id_pt)
        ->value('image'); // Only fetch the image column

        // Super Admin
        if ($userRole == 'superadmin'){
            // return redirect()->route('superadmin.dashboard');
            if ($image) {
                return redirect()->route('dashboard.index'); // Redirect to dashboard
            } else {
                return redirect()->route('report-initial-recognition.index'); // Redirect to report
            }
        }
        
        // Admin
        elseif ($userRole == 'admin'){
            return $next($request);
        }
        
        // Normal User
        elseif ($userRole == 'user'){
            // return redirect()->route('dashboard');
            if ($image) {
                return redirect()->route('dashboard.index'); // Redirect to dashboard
            } else {
                return redirect()->route('report-initial-recognition.index'); // Redirect to report
            }
        }
    
    }
}

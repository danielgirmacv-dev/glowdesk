<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BasicAdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('admin_logged_in')) {
            return redirect()->route('admin.login.form')->with('error', 'Please log in to access the admin area.');
        }

        return $next($request);
    }
}

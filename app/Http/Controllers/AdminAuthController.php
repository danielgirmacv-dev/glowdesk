<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $adminPassword = config('app.admin_password', 'secret123'); // Hardcoded fallback

        if ($request->password === $adminPassword) {
            $request->session()->put('admin_logged_in', true);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['password' => 'Invalid password provided.']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_logged_in');
        return redirect()->route('shop.index');
    }
}

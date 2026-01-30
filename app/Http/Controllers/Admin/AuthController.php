<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return response()
            ->view('admin.auth.login')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'The email field is mandatory.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'The password field is mandatory.',
            'password.min' => 'Password must be at least 6 characters long.',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $admin->name . '!');
        }

        return back()->with('error', 'Invalid credentials provided.')->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        
        // Invalidate the session
        $request->session()->invalidate();
        
        // Regenerate CSRF token for next request
        $request->session()->regenerateToken();
        
        // Explicitly delete the session from the database
        $request->session()->flush();
        
        // Return response with cache control headers to prevent back button
        return redirect()->route('admin.login')
            ->with('success', 'Logged out successfully.')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}

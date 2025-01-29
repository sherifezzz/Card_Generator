<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showSignIn()
    {
        return view('signin');
    }

    public function signIn(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Add remember me functionality
        $remember = $request->has('rememberMe');

        // Use password_hash field for authentication
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();

            // Update last login
            $user = Auth::user();
            if ($user instanceof \App\Models\User) {
                $user->update(['last_login' => now()]);
            }

            // Redirect admins to the admin dashboard
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin_dashboard');
            }

            // Redirect regular users to the homepage
            return redirect()->intended(route('homepage'));
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->withInput($request->except('password'));
    }

    public function showSignUp()
    {
        return view('signup');
    }

    public function signUp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required|min:8',
            'confirmPassword' => 'required|same:password'
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password_hash' => bcrypt($validated['password']),
            'role' => 'user',
            'full_name' => $request->username,
            'joined_date' => now()
        ]);

        return redirect()->route('signin')->with('success', 'Registration successful!');
    }


    public function logout(Request $request)
    {
        // Update last_login before logout
        if (Auth::check()) {
            $user = Auth::user();
            if ($user instanceof \App\Models\User) {
                $user->update(['last_login' => now()]);
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin')->with('success', 'You have been logged out successfully.');
    }
}
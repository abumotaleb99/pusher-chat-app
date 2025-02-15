<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm() {
        return view('auth.login-register');
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|max:20'
        ]);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {

            // $request->session()->regenerate();            
            return redirect()->intended('chat')->with('success', 'Successfully logged in!');
        }else {
            return redirect()->back()->with('error', 'Incorrect email or password.');
        }
    }

    public function showRegisterForm() {
        return view('auth.login-register');
    }

    public function register(Request $request) {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|max:20'
        ]);

        $user = User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        Auth::login($user);

        return redirect()->route('chat.index')->with('success', 'Registration successful! Welcome!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        return redirect('login')->with('success', 'Logged out successfully.');
    }
}

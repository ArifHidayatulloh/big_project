<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('nik', 'password');

        // Menambahkan pengecekan 'remember'
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Jika login berhasil
            return redirect()->intended('dashboard');
        }

        // Jika login gagal
        return redirect()->back()->withErrors([
            'nik' => 'NIK atau password salah.',
        ])->withInput();
    }

    function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();

        return redirect('/');
    }
}

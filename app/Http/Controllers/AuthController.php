<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    function dashboard()
    {
        // Ambil data untuk performance chart per unit
        $unitPerformance = DB::table('working_lists')
            ->select('unit_id', 'status', DB::raw('count(*) as total'))
            ->groupBy('unit_id', 'status')
            ->get()
            ->groupBy('unit_id');

        // Ambil data untuk performance chart per user
        $userPerformance = DB::table('working_lists')
            ->select('pic', 'status', DB::raw('count(*) as total'))
            ->groupBy('pic', 'status')
            ->get()
            ->groupBy('pic');


        return view('dashboard', compact('unitPerformance', 'userPerformance'));
    }
}

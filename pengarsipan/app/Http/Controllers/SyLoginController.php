<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SyLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('user.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // simpan data user ke session
        Session::put('nama_user', $user->nama_user);
        Session::put('role', $user->role);

        // flash message
        session()->flash('success', 'Login berhasil! Selamat datang, ' . $user->nama_user);

        // langsung redirect ke dashboard umum
        return redirect()->route('user.page.dashboard');
    }

    return redirect()->route('page.login')->with('error', 'Username atau password salah.');
    }

   public function logout()
    {
        Session::flush(); // hapus semua session
        Auth::logout();
        return redirect()->route('page.login')->with('success', 'Anda berhasil logout.');
    }

}

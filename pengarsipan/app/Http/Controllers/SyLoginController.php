<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SyLoginController extends Controller
{
    public function showLoginForm()
    {
        $credentials = $request->only('username', 'password');
        Log::info('Try Login', $credentials);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            Log::info('LOGIN SUCCESS', ['user' => $user]);

            Session::put('nama_user', $user->nama_user);
            Session::put('role', $user->role);
            Session::put('npp', $user->npp);

            Log::info('SESSION AFTER LOGIN', session()->all());

            return redirect()->route('user.page.dashboard')->with([
                'message' => 'Login berhasil! Selamat datang, ' . $user->nama_user,
                'type' => 'success' // <- akan digunakan oleh SweetAlert
            ]);
        }

        Log::warning('LOGIN FAILED', ['username' => $credentials['username']]);

        return redirect()->route('page.login')->with([
            'message' => 'Username atau password salah',
            'type' => 'error' // <- akan digunakan oleh SweetAlert
        ]);
    }

    public function logoutSy()
    {
        Auth::logout();
        Session::flush(); // Kosongkan semua session

        return redirect()->route('page.login')->with([
            'message' => 'Berhasil logout!',
            'type' => 'success'
        ]);
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class DashboardUser extends Controller
{
    public function index(Request $request)
    {
        // Contoh jika ingin menampilkan notifikasi saat pertama kali login
        if ($request->session()->has('message')) {
            // Tidak perlu apa-apa, view akan membaca session
        }

        return view("user.dashboard.index");
    }
}

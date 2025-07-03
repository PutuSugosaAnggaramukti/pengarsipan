<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
// use App\Models\User\Document\DocumentModel;

class DashboardUser extends Controller
{
    function index()
    {
        $dataBerkas=Document::select("tahun")
        ->distinct()
        ->get()->toArray();
        return view("user/dashboard/index", compact("dataBerkas"));
    }
}
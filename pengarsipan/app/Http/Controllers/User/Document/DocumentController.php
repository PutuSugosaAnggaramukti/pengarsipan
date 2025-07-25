<?php

namespace App\Http\Controllers\User\Document;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentController extends Controller
{
    public function dataDocument($id)
    {
        $dataDocument['berkas']=DocumentModel::where("tahun",$id)->get()->toArray();
        $dataDocument["th"]=$id;
        return view("user/document/dataDocument",compact("dataDocument"));
    }
    public function index()
    {
        $dataDocument = DocumentModel::select('tahun')->distinct()->get();
        return view('user.document.index', compact('dataDocument'));
    }   
}

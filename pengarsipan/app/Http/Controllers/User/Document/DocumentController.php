<?php

namespace App\Http\Controllers\User\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\Document\DocumentModel;

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

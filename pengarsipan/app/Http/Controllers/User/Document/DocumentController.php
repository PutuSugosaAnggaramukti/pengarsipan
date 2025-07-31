<?php

namespace App\Http\Controllers\User\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\Document\DocumentModel;

class DocumentController extends Controller
{
    public function index()
    {
        $dataDocument = DocumentModel::select('tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->get();

        return view('user.document.index', compact('dataDocument'));
    }

    public function dataDocument($id)
    {
        $dataDocument['berkas'] = DocumentModel::where('tahun', $id)->get()->toArray();
        $dataDocument['th'] = $id;

        return view('user.document.dataDocument', compact('dataDocument'));
    }
}

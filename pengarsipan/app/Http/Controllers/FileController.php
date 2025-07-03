<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
   public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:pdf|max:204800', // 200MB = 204800 KB
            'year' => 'required'
        ]);

        foreach ($request->file('files') as $file) {
            $path = $file->store('documents/'.$request->year, 'public');

            File::create([
                'nama_berkas' => $file->getClientOriginalName(),
                'path' => $path,
                'tahun' => $request->year,
            ]);
        }

        return back()->with('success', 'PDF berhasil diupload!');
    }
}

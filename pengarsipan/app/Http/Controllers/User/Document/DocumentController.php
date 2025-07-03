<?php

namespace App\Http\Controllers\User\Document;

use App\Models\Document;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use App\Models\User\Document\DocumentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
  public function index($id)
    {
        $dataBerkas['berkas'] = Document::where("tahun", $id)->get()->toArray();
        $dataBerkas["th"] = $id;
        return view("user.document.index", compact("dataBerkas"));
    }

    public function upload(Request $request)
    {
    
     Log::info('--- UPLOAD CALLED ---');
     Log::info('USER:', ['user' => Auth::user()]);
     Log::info('FILES:', ['files' => $request->file('files')]);

    if($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/documents', $filename);

           Document::create([
            'nama_berkas' => $filename,
            'path' => 'storage/documents/' . $filename,
            'tahun' => $request->year,
            'tanggal' => now(),
            'npp' => Auth::user()->npp, // <== pakai npp
            ]);
        }
    }

    return response()->json(['message' => 'Upload success']);
    }

    public function detail($id)
{
    $file = Document::with('user')->findOrFail($id);

    return response()->json([
        'user' => $file->user->nama_user ?? '-',
        'waktu_upload' => $file->created_at->format('d-m-Y H:i:s'),
        'size' => number_format($file->file_size / 1024 / 1024, 2) . ' MB',
    ]);
}
   public function datatable(Request $request)
{
     $tahun = $request->data;

    Log::info('DATATABLE AJAX CALLED', [
        'tahun' => $tahun
    ]);

    $files = DB::table('documents')
        ->where('tahun', $tahun)
        ->get();

    Log::info('DATATABLE QUERY RESULT', [
        'count' => count($files),
        'data' => $files
    ]);

    return response()->json(['data' => $files]);
}

    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        if (Storage::exists('public/' . $document->path)) {
            Storage::delete('public/' . $document->path);
        }

        $document->delete();

        return response()->json(['success' => 'Berkas berhasil dihapus.']);
    }
    public function show($id)
    {
        
    $file = Document::findOrFail($id);

    // hilangkan storage manual jika path di DB sudah `documents/...`
    $publicPath = 'storage/' . ltrim($file->path, '/');

    return response()->json([
        'nama' => $file->nama_berkas,
        'file' => asset($publicPath)
    ]);
    
    }

}

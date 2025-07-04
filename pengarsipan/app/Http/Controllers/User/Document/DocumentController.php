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
    public function index($id)
    {
        $dataBerkas['berkas'] = Document::where("tahun", $id)->get()->toArray();
        $dataBerkas["th"] = $id;
        return view("user.document.index", compact("dataBerkas"));
    }

    public function upload(Request $request)
    {
        $deviceTime = $request->input('device_time');

    if ($request->hasFile('file')) {
        foreach ($request->file('file') as $file) {
            // hilangkan time() supaya nama file asli
            $filename = $file->getClientOriginalName();
            
            // simpan di storage/app/public/documents/{tahun}
            $path = $file->storeAs('documents/'.$request->tahun, $filename, 'public');

            Document::create([
                'nama_berkas' => $filename,
                'path' => $path,
                'tahun' => $request->tahun,
                'npp' => Auth::user()->npp,
                'created_at' => $deviceTime,
                'updated_at' => $deviceTime
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
        ]);
    }

    public function datatable(Request $request)
    {
        $tahun = $request->data;

        Log::info('DATATABLE AJAX CALLED', ['tahun' => $tahun]);

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

        // path di DB: documents/2025/xxx.pdf
        if (Storage::disk('public')->exists($document->path)) {
            Storage::disk('public')->delete($document->path);
        }

        $document->delete();

        return response()->json(['success' => 'Berkas berhasil dihapus.']);
    }

    public function show($id)
    {
        $file = Document::findOrFail($id);

        // path di DB: documents/2025/xxx.pdf
        $fileUrl = asset('storage/' . $file->path);

        return response()->json([
            'nama' => $file->nama_berkas,
            'file' => $fileUrl
        ]);
    }
}

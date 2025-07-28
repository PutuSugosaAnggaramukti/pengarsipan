<?php

namespace App\Http\Controllers\User\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User\Document\DocumentModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SyDocumentController extends Controller
{
    public function document(Request $request)
    {
        $id = $request->data;
        $dataDetail["data"] = DocumentModel::where("tahun", $id)->get();
        return response()->json($dataDetail);
    }

    public function detail(Request $request)
    {
        $document = \App\Models\User\Document\DocumentModel::withTrashed()
                    ->with('user')
                    ->where('id_document', $request->data)
                    ->first();

        if (!$document) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'nomor' => $document->nomor,
                'tanggal' => $document->tanggal,
                'tahun' => $document->tahun,
                'nama_document' => $document->nama_document,
                'npp' => $document->npp,
                'nama_user' => $document->user->nama_user ?? '-',
                'status' => $document->trashed() ? 'Dihapus' : 'Aktif'
            ]
        ]);
    }

   public function tambahData(Request $request)
{
    Log::info("=== MULAI UPLOAD DOCUMENT ===", $request->all());

    $request->validate([
        'tahun' => 'required|digits:4',
        'documents.*' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:1048576'
    ]);
    Log::info("VALIDATE OK");

    $tahun = $request->tahun;
    $path = "uploads/Document/" . $tahun;

    if (!Storage::disk('public')->exists($path)) {
        Storage::disk('public')->makeDirectory($path);
        Log::info("BUAT DIRECTORY: " . $path);
    }

    $today = now()->toDateString();
    $nowTimestamp = now();

    $npp = session('npp') ?? (Auth::check() ? Auth::user()->npp : null);
    if (!$npp) {
        Log::error('NPP NOT FOUND', ['session' => session()->all(), 'auth_user' => Auth::user()]);
        return back()->with('error', 'NPP tidak ditemukan. Silahkan login ulang.');
    }

    $files = $request->file('documents');
    $successCount = 0;
    $failCount = 0;
    $failedFiles = [];

    foreach ($files as $file) {
        try {
            $fileName = $nowTimestamp->format('Y-m-d_H-i-s') . "_" . $file->getClientOriginalName();
            $filePath = $file->storeAs($path, $fileName, 'public');

            $lastNomor = DocumentModel::where('tahun', $tahun)->max('nomor');
            $nextNomor = $lastNomor ? $lastNomor + 1 : 1;

            DocumentModel::create([
                'nomor' => $nextNomor,
                'tanggal' => $today,
                'tahun' => $tahun,
                'nama_document' => $file->getClientOriginalName(),
                'direktory_document' => $filePath,
                'created_at' => $nowTimestamp,
                'updated_at' => $nowTimestamp,
                'npp' => $npp,
            ]);

            $successCount++;
            Log::info('UPLOAD SUKSES', ['file' => $file->getClientOriginalName()]);
        } catch (\Exception $e) {
            $failCount++;
            $failedFiles[] = $file->getClientOriginalName();
            Log::error('GAGAL UPLOAD', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
        }
    }

    $message = "{$successCount} file berhasil diupload.";
    if ($failCount > 0) {
        $message .= " {$failCount} gagal: " . implode(', ', $failedFiles);
    }

    return redirect()->route('user.page.document')->with('success', $message);
}


    public function preview($tahun, $file)
    {
        $path = "uploads/Document/{$tahun}/{$file}";

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found.');
        }

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function softDelete($id)
    {
        $doc = DocumentModel::find($id);
        if (!$doc) return back()->with('error', 'Data tidak ditemukan.');

        $doc->delete();
        Log::info('SOFT DELETE DOCUMENT', ['id' => $id]);

        return back()->with('success', 'Dokumen berhasil dipindah ke recycle bin.');
    }

    public function hapusBanyak(Request $request)
    {
        $ids = $request->input('ids', []);
        if (count($ids)) {
            $documents = DocumentModel::whereIn('id_document', $ids)->get();

            foreach ($documents as $doc) {
                // Hapus file dari storage
                if (Storage::disk('public')->exists($doc->direktory_document)) {
                    Storage::disk('public')->delete($doc->direktory_document);
                }
                $doc->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'Dokumen berhasil dihapus.'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Tidak ada dokumen yang dipilih.'
        ]);
    }
}

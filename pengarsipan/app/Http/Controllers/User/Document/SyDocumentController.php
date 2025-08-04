<?php

namespace App\Http\Controllers\User\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User\Document\DocumentModel;

class SyDocumentController extends Controller
{
    public function document(Request $request)
    {
        $id = $request->input('data');

        $documents = DocumentModel::where('tahun', $id)->get()->map(function ($item) {
            return [
                'id_document' => $item->id_document,
                'tanggal' => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
                'nama_document' => $item->nama_document,
                'direktory_document' => $item->direktory_document,
            ];
        });

        return response()->json([
            'data' => $documents
        ]);
    }

    public function detail(Request $request)
    {
        $data = \App\Models\User\Document\DocumentModel::withTrashed()
            ->with('user') // pastikan relasi user() ada di model DocumentModel
            ->find($request->data);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'nomor' => $data->nomor,
                'tanggal' => $data->tanggal,
                'tahun' => $data->tahun,
                'nama_document' => $data->nama_document, // konsistensi nama_document
                'npp' => $data->npp,
                'nama_user' => $data->user->nama_user ?? '-', // ambil dari relasi
                'status' => $data->trashed() ? 'Dihapus' : 'Aktif'
            ]
        ]);
    }

  public function tambahData(Request $request)
    {
       $request->validate([
            'tahun' => 'required|digits:4',
            'documents' => 'required|array|min:1',
            'documents.*' => 'file|max:51200|mimes:pdf,doc,docx,jpg,png',
        ]);

        $files = $request->file('documents');

        if (!$files || count($files) === 0) {
            return back()->with('error', 'File dokumen tidak ditemukan.');
        }

        $tahun = $request->tahun;
        $npp = session('npp') ?? (Auth::check() ? Auth::user()->npp : null);
        $path = "uploads/Document/{$tahun}";
        $now = now();
        $today = $now->toDateString();

        if (!$npp) {
            return back()->with('error', 'NPP tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }

        $files = $request->file('documents');
        $success = 0;
        $failed = 0;

        foreach ($files as $file) {
            try {
                $fileName = $now->format('YmdHis_u') . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs($path, $fileName, 'public');

                $lastNomor = DocumentModel::where('tahun', $tahun)->max('nomor');
                $nextNomor = $lastNomor ? $lastNomor + 1 : 1;

                DocumentModel::create([
                    'nomor' => $nextNomor,
                    'tanggal' => $today,
                    'tahun' => $tahun,
                    'nama_document' => $file->getClientOriginalName(),
                    'direktory_document' => $filePath,
                    'npp' => $npp,
                ]);

                $success++;
            } catch (\Exception $e) {
                Log::error('Upload dokumen gagal', [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ]);
                $failed++;
            }
        }

        return redirect()->route('user.page.document')
            ->with('success', "Upload selesai. Berhasil: $success, Gagal: $failed");
    }


    public function preview($tahun, $file)
    {
        $file = urldecode($file);

        $path = "uploads/Document/{$tahun}/{$file}";

        if (!Storage::disk('public')->exists($path)) {
            Log::warning('File tidak ditemukan:', ['path' => $path]);
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function softDelete($id)
    {
        $doc = DocumentModel::find($id);

        if (!$doc) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $doc->delete();
        Log::info('SOFT DELETE DOCUMENT', ['id' => $id]);

        return response()->json(['message' => 'Dokumen berhasil dipindah ke recycle bin.']);
    }

   public function hapusBanyak(Request $request)
    {
        $ids = $request->input('ids', []);

        if (count($ids)) {
            $documents = DocumentModel::whereIn('id_document', $ids)->get();

            foreach ($documents as $doc) {
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

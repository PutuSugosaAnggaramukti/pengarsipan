<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User\Document\DocumentModel;
use App\Models\Agunan\Agunan;


class RecycleBinController extends Controller
{
    public function index()
    {
        $documents = DocumentModel::onlyTrashed()->get();
        $agunans = Agunan::onlyTrashed()->get();

        Log::info('VIEW RECYCLE BIN', [
            'documents' => $documents->count(),
            'agunans' => $agunans->count()
        ]);

        return view('user.recycle', compact('documents', 'agunans'));
    }

    public function restoreDocument($id)
    {
        $doc = DocumentModel::onlyTrashed()->find($id);
        if (!$doc) return back()->with('error', 'Dokumen tidak ditemukan di recycle bin.');

        $doc->restore();
        Log::info('RESTORE DOCUMENT', ['id' => $id]);
        return back()->with('success', 'Dokumen berhasil dikembalikan.');
    }

    public function forceDeleteDocument($id)
    {
        $doc = DocumentModel::onlyTrashed()->find($id);
        if (!$doc) return back()->with('error', 'Dokumen tidak ditemukan.');

        if ($doc->direktory_document && Storage::disk('public')->exists($doc->direktory_document)) {
            Storage::disk('public')->delete($doc->direktory_document);
            Log::info('FILE DOCUMENT DELETED', ['path' => $doc->direktory_document]);
        }

        $doc->forceDelete();
        Log::info('FORCE DELETE DOCUMENT', ['id' => $id]);
        return back()->with('success', 'Dokumen dihapus permanen.');
    }

    public function restoreAgunan($id)
    {
        $agunan = Agunan::onlyTrashed()->find($id);
        if (!$agunan) return back()->with('error', 'Agunan tidak ditemukan.');

        $agunan->restore();
        Log::info('RESTORE AGUNAN', ['id' => $id]);
        return back()->with('success', 'Data agunan berhasil dikembalikan.');
    }

    public function forceDeleteAgunan($id)
    {
        $agunan = Agunan::onlyTrashed()->find($id);
        if (!$agunan) return back()->with('error', 'Agunan tidak ditemukan.');

        if ($agunan->direktori_agunan && Storage::disk('public')->exists($agunan->direktori_agunan)) {
            Storage::disk('public')->delete($agunan->direktori_agunan);
            Log::info('FILE AGUNAN DELETED', ['path' => $agunan->direktori_agunan]);
        }

        $agunan->forceDelete();
        Log::info('FORCE DELETE AGUNAN', ['id' => $id]);
        return back()->with('success', 'Data agunan dihapus permanen.');
    }

        public function bulkRestore(Request $request)
    {
        foreach ($request->selected_ids as $item) {
            [$type, $id] = explode('-', $item);
            if ($type === 'document') {
                DocumentModel::withTrashed()->where('id_document', $id)->restore();
            } elseif ($type === 'agunan') {
                Agunan::withTrashed()->where('id_agunan', $id)->restore();
            }
        }
        return back()->with('success', 'Data berhasil direstore.');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->selected_ids as $item) {
            [$type, $id] = explode('-', $item);
            if ($type === 'document') {
                DocumentModel::withTrashed()->where('id_document', $id)->forceDelete();
            } elseif ($type === 'agunan') {
                Agunan::withTrashed()->where('id_agunan', $id)->forceDelete();
            }
        }
        return back()->with('success', 'Data berhasil dihapus permanen.');
    }


}

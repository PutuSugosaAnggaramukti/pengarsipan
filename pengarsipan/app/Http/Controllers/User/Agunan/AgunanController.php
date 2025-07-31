<?php

namespace App\Http\Controllers\User\Agunan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Agunan\Agunan;


class AgunanController extends Controller
{   
    public function index()
    {
          $dataAgunan = \App\Models\Agunan\Agunan::select('tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

          return view('user.agunan.index', compact('dataAgunan'));   
    }

    public function dataAgunan($id)
    {
        $dataAgunan['berkas'] = Agunan::where("tahun", $id)->get()->toArray();
        $dataAgunan["th"] = $id;
        return view("user.agunan.dataAgunan", compact("dataAgunan"));
    }


    public function agunan(Request $request)
    {
        $id = $request->data;
        $dataDetail["data"] = Agunan::where("tahun", $id)->get();
        return response()->json($dataDetail);
    }

    public function detail(Request $request)
    {
       $agunan = \App\Models\Agunan\Agunan::withTrashed()
                    ->with('user') 
                    ->where('id_agunan', $request->data)
                    ->first();

    if (!$agunan) {
        return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
    }

    return response()->json([
        'status' => true,
        'data' => [
            'nomor' => $agunan->nomor,
            'tanggal' => $agunan->tanggal,
            'tahun' => $agunan->tahun,
            'nama_agunan' => $agunan->nama_agunan,
            'npp' => $agunan->npp,
            'nama_user' => $agunan->user->nama_user ?? '-',
            'status' => $agunan->trashed() ? 'Dihapus' : 'Aktif'
        ]
    ]);
    }

  public function tambahData(Request $request)
{
     $request->validate([
        'tahun' => 'required|digits:4',
        'agunans.*' => 'required|file|max:51200|mimes:pdf,jpg,png,doc,docx'
    ]);

    $tahun = $request->tahun;
    $npp = session('npp') ?? (Auth::check() ? Auth::user()->npp : null);
    $path = "uploads/Agunan/{$tahun}";
    $today = now()->toDateString();

    if (!Storage::disk('public')->exists($path)) {
        Storage::disk('public')->makeDirectory($path);
    }

    $files = $request->file('agunans');
    $success = 0;
    $failed = 0;

    foreach ($files as $file) {
        try {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs($path, $fileName, 'public');

            \App\Models\Agunan\Agunan::create([
                'nomor' => 'AGU.' . uniqid(),
                'tanggal' => $today,
                'tahun' => $tahun,
                'nama_agunan' => $file->getClientOriginalName(),
                'file' => $fileName,
                'direktori_agunan' => $filePath,
                'npp' => $npp,
            ]);

            $success++;
        } catch (\Exception $e) {
            Log::error('Upload agunan gagal', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            $failed++;
        }
    }

    // ✅ Redirect ke halaman agunan dengan pesan sukses
    return redirect()->route('user.page.agunan')
        ->with('success', "Upload selesai. Berhasil: $success, Gagal: $failed");
}

    public function preview($tahun, $file)
    {
        $path = "uploads/Agunan/{$tahun}/{$file}";

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found.');
        }

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function softDelete($id)
    {
        $agunan = Agunan::find($id);
        if (!$agunan) return back()->with('error', 'Data tidak ditemukan.');

        $agunan->delete();
        Log::info('SOFT DELETE AGUNAN', ['id' => $id]);

        return back()->with('success', 'Data Agunan berhasil dipindah ke recycle bin.');
    }

      public function hapusBanyak(Request $request)
{
    $ids = $request->input('ids', []);
    if (count($ids)) {
        $documents = Agunan::whereIn('id_agunan', $ids)->get();

        foreach ($documents as $doc) {
            // Hapus file dari storage
            if (Storage::disk('public')->exists($doc->direktori_agunan)) {
                Storage::disk('public')->delete($doc->direktori_agunan);
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

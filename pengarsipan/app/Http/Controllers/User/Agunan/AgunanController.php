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
        $dataAgunan = Agunan::select('tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        return view('user.agunan.index', compact('dataAgunan'));
    }

    public function dataAgunan($id)
    {
         $dataAgunan = [
            'th' => $id,
        ];
        $tahunList = Agunan::select('tahun')->groupBy('tahun')->orderBy('tahun', 'asc')->get();

        return view('user.agunan.dataAgunan', compact('dataAgunan', 'tahunList'));
    }

    public function agunan(Request $request)
    {
       $request->validate(['data' => 'required|digits:4']);

        $data = Agunan::where('tahun', $request->data)->get()->map(function ($item) {
            return [
                'id_agunan' => $item->id_agunan,
                'tanggal' => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
                'tahun' => $item->tahun,
                'nama_agunan' => $item->nama_agunan,
                'direktori_agunan' => $item->direktori_agunan,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function detail(Request $request)
    {
        $agunan = Agunan::withTrashed()
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
            'agunans' => 'required|array|min:1',
            'agunans.*' => 'required|file|max:51200|mimes:pdf,jpg,png,doc,docx',
        ]);

        $tahun = $request->tahun;
        $npp = session('npp') ?? (Auth::check() ? Auth::user()->npp : null);
        $path = "uploads/Agunan/{$tahun}";
        $today = now()->toDateString();

        Storage::disk('public')->makeDirectory($path, 0777, true, true);

        $files = $request->file('agunans');
        $success = 0;
        $failed = 0;

        foreach ($files as $file) {
            try {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs($path, $fileName, 'public');

                Agunan::create([
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

        return redirect()->route('user.page.agunan')
            ->with('success', "Upload selesai. Berhasil: $success, Gagal: $failed");
    }

    public function preview($tahun, $file)
    {
        $path = "uploads/Agunan/{$tahun}/{$file}";

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function softDelete($id)
    {
        $agunan = Agunan::find($id);

        if (!$agunan) {
            return response()->json([
                'status' => false,
                'message' => 'Data agunan tidak ditemukan.'
            ], 404);
        }

        try {
            $agunan->delete();
            Log::info('SOFT DELETE AGUNAN', ['id' => $id]);

            return response()->json([
                'status' => true,
                'message' => 'Data agunan berhasil dipindah ke recycle bin.'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal soft delete agunan', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus agunan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function hapusBanyak(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!count($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada dokumen yang dipilih.'
            ]);
        }

        $agunans = Agunan::whereIn('id_agunan', $ids)->get();

        foreach ($agunans as $agunan) {
            if (Storage::disk('public')->exists($agunan->direktori_agunan)) {
                Storage::disk('public')->delete($agunan->direktori_agunan);
            }
            $agunan->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Data agunan berhasil dihapus.'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $kategori = Kategori::when($search, function($query, $search) {
                return $query->where('nama', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc') // Ubah ke ID desc agar data terbaru di atas
            ->paginate(10); 

        return response()->json($kategori);
    }

    public function store(KategoriRequest $request)
    {
        $input = $request->validated()['nama'];
        $daftarNama = is_array($input) ? $input : [$input];
        
        $addedCount = 0;
        $ignoredCount = 0;
        $restoredCount = 0;

        foreach ($daftarNama as $n) {
            $namaClean = trim($n);
            if (empty($namaClean)) continue;

            // PENTING: Gunakan whereRaw atau LOWER agar "Batuk" sama dengan "batuk"
            // Ini memastikan pencarian tidak sensitif terhadap huruf besar/kecil
            $kategori = Kategori::withTrashed()
                ->whereRaw('LOWER(nama) = ?', [strtolower($namaClean)])
                ->first();

            if ($kategori) {
                if ($kategori->trashed()) {
                    // Jika ditemukan di tempat sampah, kita restore dan update namanya 
                    // agar sesuai dengan inputan terbaru (misal dari kecil ke besar)
                    $kategori->restore();
                    $kategori->update(['nama' => $namaClean]); 
                    $restoredCount++;
                } else {
                    $ignoredCount++;
                }
            } else {
                Kategori::create(['nama' => $namaClean]);
                $addedCount++;
            }
        }

        $resultMessage = [];
        if ($addedCount > 0) $resultMessage[] = "$addedCount data baru";
        if ($restoredCount > 0) $resultMessage[] = "$restoredCount data dipulihkan";
        if ($ignoredCount > 0) $resultMessage[] = "$ignoredCount duplikat diabaikan";

        return response()->json([
            'message' => implode(', ', $resultMessage) ?: "Tidak ada data baru"
        ], 201);
    }

    public function update(KategoriRequest $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->update($request->validated());

        return response()->json([
            'message' => 'Kategori berhasil diupdate',
            'data' => $kategori
        ]);
    }

    public function destroy($id)
    {
        Kategori::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus'
        ]);
    }

    public function deleteBatch(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Kategori::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Data terpilih berhasil dihapus']);
    }
}

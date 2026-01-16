<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ObatRequest;
use App\Models\Obat;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ObatExport;
use App\Imports\ObatImport;

class ObatController extends Controller
{
    // GET /api/obat
    public function index(Request $request)
    {
        $search = $request->query('search');

        $obat = Obat::with('kategori')
            ->when($search, function($query, $search) {
                return $query->where('nama', 'like', "%{$search}%")
                            ->orWhere('kode', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->paginate(5); // Gunakan paginate

        return response()->json($obat);
    }

    // POST /api/obat
    public function store(ObatRequest $request)
    {
        $obat = Obat::create($request->validated());

        return response()->json([
            'message' => 'Obat berhasil ditambahkan',
            'data' => $obat
        ], 201);
    }

    // PUT /api/obat/{id}
    public function update(ObatRequest $request, $id)
    {
        $obat = Obat::findOrFail($id);
        $obat->update($request->validated());

        return response()->json([
            'message' => 'Obat berhasil diupdate',
            'data' => $obat
        ]);
    }

    // DELETE /api/obat/{id}
    public function destroy($id)
    {
        Obat::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Obat berhasil dihapus'
        ]);
    }
    public function deleteBatch(Request $request)
    {
        $ids = $request->ids;
        Obat::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => count($ids) . ' obat berhasil dihapus'
        ]);
    }
    public function export() 
    {
        return Excel::download(new ObatExport, 'data-obat.xlsx');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new ObatImport, $request->file('file'));

        return response()->json(['message' => 'Data obat berhasil diimport']);
    }

    public function downloadTemplate(Request $request)
    {
        $kategori = \App\Models\Kategori::find($request->query('kategori_id'));
        $namaKat = $kategori ? $kategori->nama : 'Umum';

        $data = [
            ['kode', 'nama_obat', 'kategori', 'harga_beli', 'harga_jual', 'stok', 'stok_minimal', 'expired'],
            ['OB-001', 'Contoh Obat A', $namaKat, 5000, 7000, 100, 10, '2026-12-31'],
            ['OB-002', 'Contoh Obat B', $namaKat, 10000, 12000, 50, 10, '2026-12-31'],
        ];

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection {
            protected $data;
            public function __construct($data) { $this->data = collect($data); }
            public function collection() { return $this->data; }
        }, 'template-obat-'.strtolower($namaKat).'.xlsx');
    }
}

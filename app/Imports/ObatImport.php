<?php

namespace App\Imports;

use App\Models\Obat;
use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ObatImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Cek Kategori (Pastikan kolom di Excel namanya 'kategori')
        $namaKategori = trim($row['kategori'] ?? 'Lain-lain');
        $kategori = Kategori::firstOrCreate(['nama' => $namaKategori]);

        // 2. Handling Tanggal Expired (Penyebab utama Error 500)
        $expiredAt = null;
        if (!empty($row['expired'])) {
            try {
                // Jika user input format tanggal di Excel (angka serial), ubah ke objek tanggal
                if (is_numeric($row['expired'])) {
                    $expiredAt = Date::excelToDateTimeObject($row['expired']);
                } else {
                    $expiredAt = Carbon::parse($row['expired']);
                }
            } catch (\Exception $e) {
                $expiredAt = null;
            }
        }

        return new Obat([
            'kode'         => $row['kode'],
            'nama'         => $row['nama_obat'], // Cek header Excel harus 'nama_obat'
            'kategori_id'  => $kategori->id,
            'harga_beli'   => (int) ($row['harga_beli'] ?? 0),
            'harga_jual'   => (int) ($row['harga_jual'] ?? 0),
            'stok'         => (int) ($row['stok'] ?? 0),
            'stok_minimal' => (int) ($row['stok_minimal'] ?? 10),
            'expired_at'   => $expiredAt,
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Obat;
use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ObatSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID kategori agar relasinya pas
        $analgetik = Kategori::where('nama', 'Analgetik')->first()->id;
        $vitamin = Kategori::where('nama', 'Suplemen & Vitamin')->first()->id;
        $flu = Kategori::where('nama', 'Obat Batuk & Flu')->first()->id;

        $dataObat = [
            [
                'kode' => 'OBT001',
                'nama' => 'Paracetamol 500mg',
                'kategori_id' => $analgetik,
                'harga_beli' => 5000,
                'harga_jual' => 7500,
                'stok' => 100,
                'stok_minimal' => 10,
                'expired_at' => Carbon::now()->addYears(2),
            ],
            [
                'kode' => 'OBT002',
                'nama' => 'Amoxicillin 500mg',
                'kategori_id' => $analgetik, // Bisa disesuaikan ke Antibiotik jika sudah buat
                'harga_beli' => 12000,
                'harga_jual' => 15000,
                'stok' => 50,
                'stok_minimal' => 5,
                'expired_at' => Carbon::now()->addYear(),
            ],
            [
                'kode' => 'OBT003',
                'nama' => 'Neurobion Forte',
                'kategori_id' => $vitamin,
                'harga_beli' => 35000,
                'harga_jual' => 42000,
                'stok' => 30,
                'stok_minimal' => 5,
                'expired_at' => Carbon::now()->addYears(3),
            ],
            [
                'kode' => 'OBT004',
                'nama' => 'Sanaflu Plus Batuk',
                'kategori_id' => $flu,
                'harga_beli' => 10000,
                'harga_jual' => 13500,
                'stok' => 20,
                'stok_minimal' => 10,
                'expired_at' => Carbon::now()->addMonths(18),
            ],
            [
                'kode' => 'OBT005',
                'nama' => 'Vitamin C 1000mg',
                'kategori_id' => $vitamin,
                'harga_beli' => 45000,
                'harga_jual' => 55000,
                'stok' => 40,
                'stok_minimal' => 10,
                'expired_at' => Carbon::now()->addYears(2),
            ],
        ];

        foreach ($dataObat as $obat) {
            Obat::create($obat);
        }
    }
}
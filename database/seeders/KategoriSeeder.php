<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama' => 'Analgetik'],
            ['nama' => 'Antibiotik'],
            ['nama' => 'Antihistamin'],
            ['nama' => 'Suplemen & Vitamin'],
            ['nama' => 'Obat Batuk & Flu'],
        ];

        foreach ($categories as $cat) {
            Kategori::create($cat);
        }
    }
}
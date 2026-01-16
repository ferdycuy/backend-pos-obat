<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,      // Harus pertama karena User & Menu butuh Role
            UserSeeder::class,      // Butuh Role
            KategoriSeeder::class,  // Harus sebelum Obat
            ObatSeeder::class,      // Butuh Kategori
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $karyawanRole = Role::where('name', 'karyawan')->first();

        // Admin
        User::create([
            'name' => 'Admin POS',
            'email' => 'admin@apotek',
            'password' => Hash::make('apotek2026'),
            'role_id' => $adminRole->id,
        ]);

        // Karyawan
        User::create([
            'name' => 'Karyawan Toko',
            'email' => 'karyawan@apotek',
            'password' => Hash::make('karyawan123'),
            'role_id' => $karyawanRole->id,
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user(); // Ambil user yang sedang login dari token

        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'oldPassword' => 'nullable|required_with:newPassword',
            'newPassword' => ['nullable', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // 2. Logika Ganti Password (jika diisi)
        if ($request->filled('newPassword')) {
            // Cek password lama cocok ga sama di database
            if (!Hash::check($request->oldPassword, $user->password)) {
                return response()->json(['message' => 'Password lama salah!'], 400);
            }
            
            $user->password = Hash::make($request->newPassword);
        }

        // 3. Update Nama & Email
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui!',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role, // Kirim role lagi buat update localStorage
            ]
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KaryawanRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $karyawanRole = Role::where('name', 'karyawan')->firstOrFail();

        $query = User::with('role')
            ->where('role_id', $karyawanRole->id);

        // Fitur Pencarian (Search)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Fitur Paginasi (Gunakan paginate agar meta data muncul di React)
        $users = $query->latest()->paginate(10);

        return response()->json($users);
    }

    public function store(KaryawanRequest $request)
    {
        $roleKaryawan = Role::where('name', 'karyawan')->firstOrFail();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleKaryawan->id,
        ]);

        return response()->json([
            'message' => 'Karyawan berhasil dibuat',
            'data' => $user
        ], 201);
    }

    public function update(KaryawanRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Karyawan berhasil diupdate',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Karyawan berhasil dihapus'
        ]);
    }

    public function deleteBatch(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id'
        ]);

        User::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Karyawan terpilih berhasil dihapus'
        ]);
    }
}
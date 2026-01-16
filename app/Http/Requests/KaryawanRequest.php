<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ambil ID dari parameter route (misal: /api/karyawan/{id})
        $userId = $this->route('karyawan'); 

        return [
            'name'     => 'required|string|max:100',
            'email'    => [
                'required',
                'email',
                // Rule ini akan mengabaikan user ID yang sedang di-update
                Rule::unique('users', 'email')->ignore($userId),
            ],
            // Password hanya wajib saat tambah (POST), saat update (PUT) boleh kosong
            'password' => $this->isMethod('post') ? 'required|string|min:6' : 'nullable|string|min:6'
        ];
    }
}
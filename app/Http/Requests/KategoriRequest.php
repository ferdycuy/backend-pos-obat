<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Role sudah dijaga di middleware
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.string'   => 'Nama kategori harus berupa teks',
            'nama.max'      => 'Nama kategori maksimal 100 karakter',
        ];
    }
}

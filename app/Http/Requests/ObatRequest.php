<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObatRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Role sudah dijaga middleware
        return true;
    }

    public function rules(): array
    {
        // Mengambil ID dari parameter route apa pun namanya (obat atau id)
        $obatId = $this->route('obat') ?: $this->route('id');

        return [
            'kode'          => 'required|string|max:50|unique:obat,kode,' . $obatId,
            'nama'          => 'required|string|max:150',
            'kategori_id'   => 'required|exists:kategori,id',
            'harga_beli'    => 'required|integer|min:0',
            'harga_jual'    => 'required|integer|min:0',
            'stok'          => 'required|integer|min:0',
            'stok_minimal'  => 'required|integer|min:0',
            'expired_at'    => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required'        => 'Kode obat wajib diisi',
            'kode.unique'          => 'Kode obat sudah digunakan',
            'kategori_id.exists'   => 'Kategori tidak valid',
            'harga_beli.integer'   => 'Harga beli harus angka',
            'harga_jual.integer'   => 'Harga jual harus angka',
            'stok.integer'         => 'Stok harus angka',
            'stok_minimal.integer' => 'Stok minimal harus angka',
        ];
    }
}

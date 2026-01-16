<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.obat_id' => 'required|exists:obat,id',
            'items.*.qty'     => 'required|integer|min:1',
            
            // Tambahkan aturan ini agar tidak Error 400/422
            'bayar'             => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:cash,transfer',
            'diskon_tipe'       => 'nullable|in:nominal,persen',
            'diskon_value'      => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Item transaksi wajib diisi',
            'items.*.qty.min' => 'Qty minimal 1',
            'bayar.required' => 'Nominal bayar wajib diisi',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih',
        ];
    }
}

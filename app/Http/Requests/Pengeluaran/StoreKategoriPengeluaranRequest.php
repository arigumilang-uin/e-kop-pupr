<?php

namespace App\Http\Requests\Pengeluaran;

use Illuminate\Foundation\Http\FormRequest;

class StoreKategoriPengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100|unique:kategori_pengeluaran,nama',
            'deskripsi' => 'nullable|string',
        ];
    }
}

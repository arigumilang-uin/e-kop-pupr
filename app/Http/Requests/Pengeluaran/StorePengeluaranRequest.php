<?php

namespace App\Http\Requests\Pengeluaran;

use Illuminate\Foundation\Http\FormRequest;

class StorePengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nominal.min' => 'Nominal harus minimal Rp 1.',
            'keterangan.required' => 'Keterangan pengeluaran wajib diisi.',
        ];
    }
}

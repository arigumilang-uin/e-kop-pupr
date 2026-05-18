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
            'tanggal' => 'required|date',
            'pengeluaran' => 'required|array|min:1',
            'pengeluaran.*.kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
            'pengeluaran.*.nominal' => 'required|numeric|min:1',
            'pengeluaran.*.sumber_dana' => 'required|in:brk,kas',
            'pengeluaran.*.keterangan' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'pengeluaran.*.nominal.min' => 'Nominal harus minimal Rp 1.',
            'pengeluaran.*.keterangan.required' => 'Keterangan pengeluaran wajib diisi.',
            'pengeluaran.*.kategori_pengeluaran_id.required' => 'Kategori wajib dipilih.',
        ];
    }
}

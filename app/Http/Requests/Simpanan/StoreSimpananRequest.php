<?php

namespace App\Http\Requests\Simpanan;

use Illuminate\Foundation\Http\FormRequest;

class StoreSimpananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'anggota_id' => ['required', 'exists:anggota,id'],
            'jenis_simpanan_id' => ['required', 'exists:jenis_simpanan,id'],
            'nominal' => ['required', 'numeric', 'min:1000'],
            'tanggal' => ['required', 'date'],
            'bulan_untuk' => ['nullable', 'integer', 'min:1', 'max:12'],
            'tahun_untuk' => ['nullable', 'integer', 'min:2000'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}

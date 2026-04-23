<?php

namespace App\Http\Requests\Anggota;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', 'max:50', 'unique:anggota,nip'],
            'nama' => ['required', 'string', 'max:150'],
            'bidang_id' => ['required', 'exists:bidang,id'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'tanggal_masuk' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'bidang_id.required' => 'Bidang wajib dipilih.',
            'bidang_id.exists' => 'Bidang tidak valid.',
        ];
    }
}

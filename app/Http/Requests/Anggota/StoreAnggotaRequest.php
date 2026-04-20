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
            'nip' => ['required', 'string', 'max:20', 'unique:anggota,nip'],
            'nama' => ['required', 'string', 'max:100'],
            'golongan' => ['required', 'string', 'max:10'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'bidang_id' => ['required', 'exists:bidang,id'],
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string', 'max:15'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'golongan.required' => 'Golongan wajib diisi.',
            'bidang_id.required' => 'Bidang wajib dipilih.',
            'bidang_id.exists' => 'Bidang tidak valid.',
        ];
    }
}

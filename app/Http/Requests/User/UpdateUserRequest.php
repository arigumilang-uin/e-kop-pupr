<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;
        $hasNip = !empty($this->input('nip'));

        return [
            'nama' => $hasNip ? 'nullable|string|max:150' : 'required|string|max:150',
            'nip' => [
                'nullable',
                'string',
                'max:50',
                $hasNip ? Rule::unique('users', 'nip')->ignore($userId) : '',
                $hasNip ? Rule::exists('anggota', 'nip') : '',
            ],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => ['required', 'string', Rule::exists('roles', 'name')],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'      => 'Nama wajib diisi jika NIP tidak diisi.',
            'nip.exists'         => 'NIP tidak ditemukan di data anggota koperasi.',
            'nip.unique'         => 'NIP ini sudah terkait dengan akun lain.',
            'username.unique'    => 'Username sudah digunakan.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 6 karakter.',
            'role.exists'        => 'Role yang dipilih tidak valid.',
        ];
    }
}

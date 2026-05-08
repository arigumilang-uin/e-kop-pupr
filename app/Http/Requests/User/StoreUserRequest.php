<?php

namespace App\Http\Requests\User;

use App\Enums\RoleUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'nip'),
                Rule::exists('anggota', 'nip'),
            ],
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', new Enum(RoleUser::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nip.exists' => 'NIP tidak ditemukan di data anggota koperasi.',
            'nip.unique' => 'NIP ini sudah terkait dengan akun lain.',
            'username.unique' => 'Username sudah digunakan.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ];
    }
}

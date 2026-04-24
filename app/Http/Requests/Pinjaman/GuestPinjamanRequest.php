<?php

namespace App\Http\Requests\Pinjaman;

use Illuminate\Foundation\Http\FormRequest;

class GuestPinjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Guest form — tidak perlu auth
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', 'max:50'],
            'nama_bank' => ['required', 'string', 'max:100'],
            'nama_rekening' => ['required', 'string', 'max:100'],
            'no_rekening' => ['required', 'string', 'max:30'],
            'nominal_pinjaman' => ['required', 'numeric', 'min:100000'],
            'tenor_bulan' => ['required', 'integer', 'min:1'],
            'confirm_override' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nama_bank.required' => 'Nama bank wajib diisi.',
            'nama_rekening.required' => 'Nama rekening wajib diisi.',
            'no_rekening.required' => 'Nomor rekening wajib diisi.',
            'nominal_pinjaman.required' => 'Nominal pinjaman wajib diisi.',
            'nominal_pinjaman.min' => 'Nominal pinjaman minimal Rp 100.000.',
            'tenor_bulan.required' => 'Tenor wajib diisi.',
            'tenor_bulan.min' => 'Tenor minimal 1 bulan.',
        ];
    }
}

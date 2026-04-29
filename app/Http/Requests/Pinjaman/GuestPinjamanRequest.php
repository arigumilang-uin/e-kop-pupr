<?php

namespace App\Http\Requests\Pinjaman;

use App\Models\PeriodePinjaman;
use Illuminate\Foundation\Http\FormRequest;

class GuestPinjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Guest form — tidak perlu auth
    }

    public function rules(): array
    {
        $periode = PeriodePinjaman::where('token', $this->route('token'))->first();
        $nominalMin = $periode?->nominal_min ?? 100000;
        $nominalMax = $periode?->limit_per_anggota ?? 50000000;

        return [
            'nip' => ['required', 'string', 'max:50'],
            'nama_bank' => ['required', 'string', 'max:100'],
            'nama_rekening' => ['required', 'string', 'max:100'],
            'no_rekening' => ['required', 'string', 'max:30'],
            'nominal_pinjaman' => ['required', 'numeric', "min:{$nominalMin}", "max:{$nominalMax}"],
            'tenor_bulan' => ['required', 'integer', 'min:1'],
            'confirm_override' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        $periode = PeriodePinjaman::where('token', $this->route('token'))->first();
        $nominalMin = $periode?->nominal_min ?? 100000;

        return [
            'nip.required' => 'NIP wajib diisi.',
            'nama_bank.required' => 'Nama bank wajib diisi.',
            'nama_rekening.required' => 'Nama rekening wajib diisi.',
            'no_rekening.required' => 'Nomor rekening wajib diisi.',
            'nominal_pinjaman.required' => 'Nominal pinjaman wajib diisi.',
            'nominal_pinjaman.min' => 'Nominal pinjaman minimal Rp ' . number_format($nominalMin, 0, ',', '.') . '.',
            'nominal_pinjaman.max' => 'Nominal pinjaman melebihi limit maksimum periode ini.',
            'tenor_bulan.required' => 'Tenor wajib diisi.',
            'tenor_bulan.min' => 'Tenor minimal 1 bulan.',
        ];
    }
}

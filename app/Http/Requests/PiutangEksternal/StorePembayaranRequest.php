<?php

namespace App\Http\Requests\PiutangEksternal;

use Illuminate\Foundation\Http\FormRequest;

class StorePembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sumber_dana' => ['nullable', 'in:brk,kas'],
            'nominal' => ['required', 'numeric', 'min:1000'],
            'tanggal_bayar' => ['required', 'date'],
            'bukti_bayar' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nominal.required' => 'Nominal pembayaran wajib diisi.',
            'nominal.min' => 'Nominal pembayaran minimal Rp 1.000.',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi.',
            'bukti_bayar.required' => 'Bukti pembayaran wajib dilampirkan.',
            'bukti_bayar.mimes' => 'Format bukti pembayaran harus JPG, PNG, atau PDF.',
            'bukti_bayar.max' => 'Ukuran file bukti maksimal 5MB.',
        ];
    }
}

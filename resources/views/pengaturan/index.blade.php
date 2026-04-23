@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('subtitle', 'Konfigurasi parameter operasional Koperasi PUPR Riau')

@section('content')
<div class="space-y-6">

    {{-- Info Banner --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-amber-800">
            <p class="font-bold mb-1">Kebijakan Efektivitas Perubahan Konfigurasi</p>
            <ul class="list-disc pl-4 space-y-1 text-xs text-amber-700">
                <li><strong>Simpanan Pokok & Wajib:</strong> Nilai baru berlaku mulai proses <em>Potongan TPP bulan berikutnya</em>. Bulan berjalan tetap menggunakan nilai lama.</li>
                <li><strong>Bunga, Potongan SWP, Dana Resiko, Biaya Admin:</strong> Berlaku untuk <em>pinjaman baru yang diajukan</em> setelah perubahan. Pinjaman aktif tetap menggunakan konfigurasi saat pinjaman tersebut dibuat.</li>
                <li><strong>Tenor & Batas Bulan Pelunasan:</strong> Berlaku untuk <em>pengajuan pinjaman baru</em> setelah perubahan.</li>
                <li><strong>Pengaturan Teknis (Login):</strong> Berlaku <em>langsung/segera</em> setelah disimpan.</li>
            </ul>
        </div>
    </div>

    @php
        $hints = [
            'simpanan_pokok' => 'Berlaku di proses TPP bulan berikutnya.',
            'simpanan_wajib' => 'Berlaku di proses TPP bulan berikutnya.',
            'bunga_pinjaman_persen' => 'Berlaku untuk pinjaman baru. Pinjaman aktif tidak terpengaruh.',
            'potongan_swp_persen' => 'Berlaku untuk pinjaman baru. Pinjaman aktif tidak terpengaruh.',
            'potongan_dana_resiko_persen' => 'Berlaku untuk pinjaman baru. Pinjaman aktif tidak terpengaruh.',
            'potongan_biaya_admin_persen' => 'Berlaku untuk pinjaman baru. Pinjaman aktif tidak terpengaruh.',
            'tenor_minimal' => 'Berlaku untuk pengajuan pinjaman baru.',
            'batas_bulan_pelunasan_default' => 'Berlaku untuk pengajuan pinjaman baru.',
            'maks_percobaan_login' => 'Berlaku segera.',
            'durasi_kunci_akun_menit' => 'Berlaku segera.',
        ];
    @endphp

    @foreach($grouped as $kategori => $items)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Header Kategori --}}
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                @if($kategori === 'Keuangan')
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                @else
                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                @endif
                Kategori: {{ $kategori }}
            </h3>
        </div>

        {{-- Daftar Pengaturan --}}
        <div class="divide-y divide-slate-100">
            @foreach($items as $pengaturan)
            <form action="{{ route('pengaturan.update', $pengaturan->id) }}" method="POST" class="px-6 py-5 hover:bg-slate-50/50 transition-colors">
                @csrf
                @method('PATCH')
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-slate-800">{{ $pengaturan->deskripsi }}</h4>
                        <p class="text-xs text-slate-400 font-mono mt-1">key: {{ $pengaturan->key }}</p>
                        @if(isset($hints[$pengaturan->key]))
                        <p class="text-[11px] text-blue-500 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $hints[$pengaturan->key] }}
                        </p>
                        @endif
                    </div>

                    {{-- Input --}}
                    <div class="flex items-center gap-3 shrink-0">
                        <input type="text" name="value" value="{{ $pengaturan->value }}"
                               class="w-40 px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono text-right focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm whitespace-nowrap">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($grouped->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <p class="text-slate-500 text-sm">Belum ada data pengaturan yang tersedia di database.</p>
    </div>
    @endif

</div>
@endsection

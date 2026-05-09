@extends('layouts.app')

@section('title', 'Reaktivasi Anggota')
@section('subtitle', 'Konfirmasi pengaktifan kembali keanggotaan koperasi')

@section('actions')
<x-back-button fallback="{{ route('anggota.index') }}">
    <span class="hidden sm:inline">← Kembali</span>
    <span class="sm:hidden">Batal</span>
</x-back-button>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header Anggota --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden relative p-6">
        <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-stone-50 to-transparent pointer-events-none"></div>
        <div class="flex items-center gap-5 relative z-0">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#043d2e] to-emerald-900 flex items-center justify-center text-white text-2xl font-bold shadow-lg ring-4 ring-[#043d2e]/10 shrink-0">
                {{ strtoupper(substr($anggota->nama, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-xl font-bold text-stone-800 tracking-tight">{{ $anggota->nama }}</h3>
                <div class="flex items-center gap-3 mt-1.5 text-sm text-stone-500">
                    <span class="font-mono text-stone-600 font-medium tracking-wide">NIP. <x-nip-display :value="$anggota->nip" /></span>
                    <span class="text-stone-300">•</span>
                    <span class="font-medium px-2.5 py-0.5 rounded bg-stone-100 text-stone-600 border border-stone-200 text-[11px] uppercase tracking-wider">{{ $anggota->bidang->nama_bidang ?? 'Belum diset' }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($arsipKeluar)
    {{-- Arsip Keluar — Detail rincian --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-stone-50 border-b border-stone-200">
            <h4 class="text-sm font-bold text-stone-700 flex items-center gap-2.5 tracking-tight">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-amber-100/50 text-amber-600 border border-amber-200/50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
                Arsip Keluar Terakhir — <span class="text-amber-700">{{ $arsipKeluar->tanggal_keluar->translatedFormat('d F Y') }}</span>
            </h4>
        </div>
        <div class="p-6">
            <p class="text-sm text-stone-600 mb-5 leading-relaxed">Anggota ini statusnya keluar dari koperasi pada <strong class="text-stone-800">{{ $arsipKeluar->tanggal_keluar->translatedFormat('d F Y') }}</strong> dan telah menerima restrukturisasi / pengembalian dana penuh dengan rincian berikut:</p>

            <div class="space-y-3 mb-6">
                @foreach($arsipKeluar->rincian_simpanan as $r)
                <div class="flex justify-between items-center text-[13px] border-b border-dashed border-stone-200 pb-3">
                    <span class="text-stone-600 font-bold uppercase tracking-wider text-[11px]">{{ $r['nama'] }}</span>
                    <span class="font-mono font-bold text-stone-800 bg-stone-50 px-2 py-0.5 rounded border border-stone-100">{{ format_rupiah($r['nominal']) }}</span>
                </div>
                @endforeach
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 flex justify-between items-center">
                <div>
                    <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Kewajiban Setor Daftar Ulang</span>
                    <p class="text-[11px] text-amber-600 mt-1 font-medium">Dana simpanan harus dipulihkan (disetor) kembali agar aktif</p>
                </div>
                <span class="text-2xl font-black text-amber-700 font-mono tracking-tight bg-white px-3 py-1.5 rounded-lg border border-amber-200/50 shadow-sm">{{ format_rupiah($arsipKeluar->nominal_wajib_setor_ulang) }}</span>
            </div>
        </div>
    </div>

    {{-- Konfirmasi --}}
    <div class="bg-white rounded-2xl border border-stone-200 border-t-4 border-t-[#043d2e] shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 bg-white border-b border-stone-100">
            <h4 class="text-sm font-bold text-stone-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#043d2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Konfirmasi Reaktivasi & Mutasi Keuangan
            </h4>
        </div>
        <div class="p-6">
            <div class="bg-[#043d2e]/5 border border-[#043d2e]/10 rounded-xl p-5 mb-6 text-sm text-[#043d2e] space-y-2.5">
                <p class="font-bold">Skema Pencatatan Otomatis:</p>
                <ul class="list-disc pl-4 text-xs space-y-1.5 font-medium opacity-90">
                    <li>Status keanggotaan akan dipulihkan sepenuhnya menjadi <strong class="font-bold">Aktif</strong>.</li>
                    <li>Sistem akan <strong class="font-bold">menyetorkan nominal simpanan</strong> secara proporsional sesuai saldo simpanan secara otomatis (Tanpa input log manual).</li>
                    <li>Saldo kas koperasi riil akan <strong class="font-bold">meningkat</strong> sebesar {{ format_rupiah($arsipKeluar->nominal_wajib_setor_ulang) }}.</li>
                </ul>
            </div>

            <form action="{{ route('anggota.reaktivasi.proses', $anggota) }}" method="POST" onsubmit="return confirm('SAH DAN VALID: Konfirmasi reaktivasi {{ $anggota->nama }}? Kas koperasi akan ditambahkan sesuai dengan kewajiban setor.')">
                @csrf
                <div class="space-y-6">
                    <div class="flex items-start gap-3 bg-stone-50 border border-stone-200 p-4 rounded-xl">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="konfirmasi" id="konfirmasi" value="1" required class="w-4 h-4 rounded border-stone-300 text-[#043d2e] focus:ring-[#043d2e] cursor-pointer">
                        </div>
                        <label for="konfirmasi" class="text-[13px] text-stone-700 font-medium pt-0.5 cursor-pointer leading-tight">
                            Saya menjamin bahwa anggota <strong>{{ $anggota->nama }}</strong> telah lunas menyetorkan kewajiban daftar ulang sebesar {{ format_rupiah($arsipKeluar->nominal_wajib_setor_ulang) }} dan mutasi keanggotaan bisa diaktifkan.
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white font-bold rounded-xl text-sm transition-all shadow-md active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        AKTIFKAN KEMBALI & TERIMA SETORAN
                    </button>
                </div>
            </form>
        </div>
    </div>

    @else
    {{-- Tidak ada arsip keluar (anggota di-nonaktifkan tanpa proses keuangan) --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 text-center">
        <div class="w-16 h-16 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="text-sm text-stone-600 mb-6 max-w-md mx-auto">
            <p class="font-medium">Tidak ditemukan arsip keluar/keuangan untuk anggota ini. Anggota diturunkan ke status <strong>Nonaktif</strong> secara manual.</p>
        </div>
        <form action="{{ route('anggota.reaktivasi.proses', $anggota) }}" method="POST" onsubmit="return confirm('Peringatan: Aktifkan kembali {{ $anggota->nama }} tanpa mutasi keuangan?')">
            @csrf
            <input type="hidden" name="konfirmasi" value="1">
            <button type="submit" class="w-full sm:w-auto px-8 mx-auto py-3 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                Aktifkan Kembali Anggota (Override)
            </button>
        </form>
    </div>
    @endif

</div>
@endsection

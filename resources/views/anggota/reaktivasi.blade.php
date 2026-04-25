@extends('layouts.app')

@section('title', 'Reaktivasi Anggota')
@section('subtitle', 'Konfirmasi pengaktifan kembali keanggotaan koperasi')

@section('actions')
<a href="{{ route('anggota.index') }}" class="py-2.5 px-4 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors">
    ← Kembali
</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header Anggota --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white text-xl font-bold shadow-lg">
                {{ strtoupper(substr($anggota->nama, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">{{ $anggota->nama }}</h3>
                <div class="flex items-center gap-3 text-sm text-slate-500">
                    <span>NIP: {{ $anggota->nip }}</span>
                    <span>•</span>
                    <span>{{ $anggota->bidang->nama_bidang ?? '-' }}</span>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200 mt-1">
                    {{ $anggota->status->label() }}
                </span>
            </div>
        </div>
    </div>

    @if($arsipKeluar)
    {{-- Arsip Keluar — Detail rincian --}}
    <div class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-amber-50 border-b border-amber-200">
            <h4 class="text-sm font-bold text-amber-700 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Arsip Keluar Terakhir — {{ $arsipKeluar->tanggal_keluar->translatedFormat('d F Y') }}
            </h4>
        </div>
        <div class="p-6">
            <p class="text-sm text-slate-600 mb-4">Anggota ini keluar dari koperasi pada <strong>{{ $arsipKeluar->tanggal_keluar->translatedFormat('d F Y') }}</strong> dan telah menerima pengembalian simpanan berikut:</p>

            <div class="space-y-2 mb-4">
                @foreach($arsipKeluar->rincian_simpanan as $r)
                <div class="flex justify-between items-center text-sm border-b border-dashed border-slate-200 pb-2">
                    <span class="text-slate-600 font-medium">{{ $r['nama'] }}</span>
                    <span class="font-mono font-bold text-slate-800">{{ format_rupiah($r['nominal']) }}</span>
                </div>
                @endforeach
            </div>

            <div class="bg-amber-100 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <span class="text-sm font-bold text-amber-800 uppercase">Wajib Setor Ulang</span>
                    <p class="text-[10px] text-amber-600">Harus disetor agar bisa aktif kembali</p>
                </div>
                <span class="text-2xl font-black text-amber-900 font-mono">{{ format_rupiah($arsipKeluar->nominal_wajib_setor_ulang) }}</span>
            </div>
        </div>
    </div>

    {{-- Konfirmasi --}}
    <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-200">
            <h4 class="text-sm font-bold text-emerald-700 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Konfirmasi Reaktivasi
            </h4>
        </div>
        <div class="p-6">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-sm text-blue-700 space-y-2">
                <p class="font-bold">ℹ️ Apa yang terjadi saat dikonfirmasi:</p>
                <ul class="list-disc pl-4 text-xs space-y-1">
                    <li>Status anggota diubah kembali menjadi <strong>Aktif</strong>.</li>
                    <li>Sistem <strong>otomatis mencatat simpanan</strong> sesuai rincian di atas sebagai setoran wajib pendaftar ulang — <strong>tanpa perlu input manual</strong>.</li>
                    <li>Saldo kas koperasi <strong>bertambah</strong> sebesar {{ format_rupiah($arsipKeluar->nominal_wajib_setor_ulang) }}.</li>
                    <li>Anggota ditandai sebagai <strong>Pendaftar Ulang</strong> di sistem.</li>
                </ul>
            </div>

            <form action="{{ route('anggota.reaktivasi.proses', $anggota) }}" method="POST" onsubmit="return confirm('Konfirmasi reaktivasi {{ $anggota->nama }}? Simpanan akan otomatis dicatat.')">
                @csrf
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="konfirmasi" id="konfirmasi" value="1" required class="w-4 h-4 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="konfirmasi" class="text-sm text-slate-700 font-medium">
                            Saya mengkonfirmasi bahwa anggota <strong>{{ $anggota->nama }}</strong> telah menyetor {{ format_rupiah($arsipKeluar->nominal_wajib_setor_ulang) }}
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-colors shadow-lg shadow-emerald-600/20">
                        ✓ Aktifkan Kembali & Catat Simpanan Otomatis
                    </button>
                </div>
            </form>
        </div>
    </div>

    @else
    {{-- Tidak ada arsip keluar (anggota di-nonaktifkan tanpa proses keuangan) --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 text-sm text-slate-600">
            <p>Tidak ditemukan arsip keluar untuk anggota ini. Anggota kemungkinan di-nonaktifkan secara manual tanpa proses pengembalian simpanan.</p>
        </div>
        <form action="{{ route('anggota.reaktivasi.proses', $anggota) }}" method="POST" onsubmit="return confirm('Aktifkan kembali {{ $anggota->nama }}?')">
            @csrf
            <input type="hidden" name="konfirmasi" value="1">
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-colors">
                Aktifkan Kembali Anggota
            </button>
        </form>
    </div>
    @endif

</div>
@endsection

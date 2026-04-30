@extends('layouts.app')

@section('title', 'Proses Keluar Anggota')
@section('subtitle', 'Analisis & validasi pengeluaran anggota dari koperasi')

@section('actions')
<a href="{{ route('anggota.show', $anggota) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-stone-200 text-stone-600 rounded-xl text-sm font-medium hover:bg-stone-50 transition-colors shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Kembali ke Profil
</a>
@endsection

@section('content')
<div class="space-y-6 max-w-7xl">

    {{-- Header Anggota --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 overflow-hidden relative">
        <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-stone-50 to-transparent pointer-events-none"></div>
        <div class="flex items-center gap-5 relative z-0">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#043d2e] to-emerald-900 flex items-center justify-center text-white text-2xl font-bold shadow-lg ring-4 ring-[#043d2e]/10">
                {{ strtoupper(substr($anggota->nama, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-xl font-bold text-stone-800 tracking-tight">{{ $anggota->nama }}</h3>
                <div class="flex items-center gap-3 mt-1.5 text-sm text-stone-500">
                    <span class="font-mono text-stone-600 font-medium tracking-wide">NIP. <x-nip-display :value="$anggota->nip" /></span>
                    <span class="text-stone-300">•</span>
                    <span class="font-medium px-2.5 py-0.5 rounded bg-stone-100 text-stone-600 border border-stone-200 text-[11px] uppercase tracking-wider">{{ $anggota->bidang->nama_bidang ?? 'Belum diset' }}</span>
                    <span class="text-stone-300">•</span>
                    <span class="font-medium text-[13px]">Bergabung: {{ $anggota->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ========================= --}}
        {{-- STEP 1: Cek Pinjaman Aktif --}}
        {{-- ========================= --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border {{ $analisis['has_pinjaman_aktif'] ? 'border-red-200 ring-1 ring-red-100' : 'border-stone-200' }}">
            <div class="px-6 py-4 border-b flex items-center justify-between {{ $analisis['has_pinjaman_aktif'] ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-100' }}">
                <h4 class="text-sm font-bold flex items-center gap-2.5 tracking-tight {{ $analisis['has_pinjaman_aktif'] ? 'text-red-700' : 'text-emerald-700' }}">
                    @if($analisis['has_pinjaman_aktif'])
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    STEP 1: Pinjaman Aktif Terdeteksi
                    @else
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    STEP 1: Bebas Pinjaman
                    @endif
                </h4>
            </div>
            <div class="p-6">
                @if($analisis['has_pinjaman_aktif'])
                <div class="text-sm text-red-700 mb-4 flex items-start gap-2">
                    <p class="font-bold">Anggota masih memiliki <span class="bg-red-100 px-1.5 rounded">{{ $analisis['pinjaman_aktif']->count() }} pinjaman</span> aktif:</p>
                </div>
                <div class="space-y-3">
                    @foreach($analisis['pinjaman_aktif'] as $p)
                    <div class="bg-white border border-red-200 rounded-xl p-4 shadow-sm relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500"></div>
                        <div class="flex justify-between items-center text-sm pl-2">
                            <div>
                                <span class="font-mono font-bold text-red-800 block mb-0.5">{{ $p->no_referensi }}</span>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-red-500 bg-red-50 px-1.5 py-0.5 rounded">{{ ucfirst($p->status->value ?? $p->status) }}</span>
                            </div>
                            <span class="font-mono font-black text-red-700 text-base">{{ format_rupiah($p->nominal_pinjaman) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="bg-red-50 rounded-xl p-4 mt-5 text-[13px] text-red-800 border border-red-100 flex gap-3">
                    <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p><strong>Proses keluar tidak dapat dilanjutkan.</strong> Pinjaman aktif harus dilunasi secara menyeluruh terlebih dahulu.</p>
                </div>
                @else
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-[13px] font-medium text-stone-600">Anggota bersih dari kewajiban hutang pinjaman koperasi. <br> <span class="text-emerald-600 font-bold">Proses dapat dilanjutkan ke STEP 2.</span></p>
                </div>
                @endif
            </div>
        </div>

        {{-- ========================= --}}
        {{-- STEP 2: Rincian Simpanan    --}}
        {{-- ========================= --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-stone-50 border-b border-stone-200">
                <h4 class="text-sm font-bold text-stone-700 flex items-center gap-2.5 tracking-tight">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-[#043d2e]/10 text-[#043d2e]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </span>
                    STEP 2: Rincian Pengembalian
                </h4>
            </div>
            <div class="p-6">
                <div class="space-y-3 mb-6">
                    @foreach($analisis['rincian_simpanan'] as $item)
                    <div class="flex justify-between items-center text-[13px] border-b border-dashed border-stone-200 pb-3">
                        <div>
                            <span class="font-bold text-stone-700 uppercase tracking-wider text-[11px] block">{{ $item['nama'] }}</span>
                            @if($item['sudah_ditarik'] > 0)
                            <p class="text-[10px] text-stone-400 mt-0.5 font-medium">Bruto: {{ format_rupiah($item['bruto']) }} − Ditarik: <span class="text-red-500">{{ format_rupiah($item['sudah_ditarik']) }}</span></p>
                            @endif
                        </div>
                        <span class="font-mono font-bold text-stone-800 text-sm bg-stone-50 px-2 py-1 rounded border border-stone-100">{{ format_rupiah($item['neto']) }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="bg-gradient-to-br from-[#043d2e] to-[#032e22] rounded-xl p-5 flex justify-between items-center text-white shadow-md">
                    <span class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Total Dikembalikan</span>
                    <span class="text-2xl font-black font-mono tracking-tight">{{ format_rupiah($analisis['total_dikembalikan']) }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ========================= --}}
    {{-- STEP 3: Cek Saldo Kas      --}}
    {{-- ========================= --}}
    @if(!$analisis['has_pinjaman_aktif'])
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border {{ $analisis['saldo_cukup'] ? 'border-stone-200 hover:border-emerald-200 transition-colors' : 'border-amber-200 ring-1 ring-amber-100' }}">
        <div class="px-6 py-4 border-b {{ $analisis['saldo_cukup'] ? 'bg-stone-50 border-stone-200' : 'bg-amber-50 border-amber-200' }}">
            <h4 class="text-sm font-bold flex items-center gap-2.5 tracking-tight {{ $analisis['saldo_cukup'] ? 'text-stone-700' : 'text-amber-700' }}">
                @if($analisis['saldo_cukup'])
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                STEP 3: Saldo Kas Koperasi Mencukupi ✓
                @else
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-amber-100 text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                STEP 3: Saldo Kas Koperasi Defisit / Tidak Mencukupi
                @endif
            </h4>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div class="bg-stone-50 border border-stone-100 rounded-xl p-5 text-center shadow-sm">
                    <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Saldo Kas Aktif
                    </p>
                    <p class="text-xl font-black font-mono text-stone-800">{{ format_rupiah($analisis['saldo_kas']) }}</p>
                </div>
                <div class="bg-stone-50 border border-stone-100 rounded-xl p-5 text-center shadow-sm">
                    <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Dikembalikan (-$)
                    </p>
                    <p class="text-xl font-black font-mono text-stone-800">{{ format_rupiah($analisis['total_dikembalikan']) }}</p>
                </div>
                <div class="rounded-xl p-5 text-center shadow-sm {{ $analisis['saldo_cukup'] ? 'bg-emerald-50 border border-emerald-100' : 'bg-red-50 border border-red-100' }}">
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-1.5 flex items-center justify-center gap-1.5 {{ $analisis['saldo_cukup'] ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $analisis['saldo_cukup'] ? 'Sisa Saldo Baru →' : 'Kekurangan/Defisit ⚠' }}
                    </p>
                    <p class="text-xl font-black font-mono {{ $analisis['saldo_cukup'] ? 'text-emerald-800' : 'text-red-800' }}">
                        {{ $analisis['saldo_cukup'] ? format_rupiah($analisis['saldo_kas'] - $analisis['total_dikembalikan']) : format_rupiah($analisis['defisit']) }}
                    </p>
                </div>
            </div>

            @if(!$analisis['saldo_cukup'])
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 flex gap-4">
                <svg class="w-6 h-6 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="text-sm text-amber-900 font-bold mb-2">Kas operasional tidak mencukupi untuk melunasi pengembalian simpanan penuh anggota ini.</p>
                    <p class="text-[13px] text-amber-800 mb-4">Kas kurang sebesar <strong class="font-mono bg-amber-200/50 px-1 rounded">{{ format_rupiah($analisis['defisit']) }}</strong>. Proses ini ditangguhkan otomatis oleh sistem untuk menghindari kerusakan pencatatan neraca.</p>
                    <a href="{{ route('keuangan.simulasi') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Keuangan & Proyeksi
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ========================= --}}
    {{-- STEP 4: Konfirmasi & Proses --}}
    {{-- ========================= --}}
    @if($analisis['bisa_proses'])
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden ring-2 ring-red-500/20 border border-red-200">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex items-center justify-between">
            <h4 class="text-sm font-bold text-white flex items-center gap-2.5 tracking-tight">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20 text-white">
                    4
                </span>
                Tahap Finalisasi: Konfirmasi Eksekusi
            </h4>
        </div>
        <div class="p-6">
            <div class="bg-red-50 border border-red-100 rounded-xl p-5 mb-6">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 shrink-0 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="font-bold text-red-800 text-sm mb-2">Tindakan ini permanen & mendisrupsi kas secara real-time!</p>
                        <ul class="list-disc pl-4 text-[13px] text-red-700 space-y-1.5 font-medium">
                            <li><strong class="font-bold">Kas berkurang</strong> {{ format_rupiah($analisis['total_dikembalikan']) }} pada jurnal simpanan keluar.</li>
                            <li>Status akan <strong class="font-bold">Nonaktif</strong>. NIP ini tidak bisa mengajukan peminjaman apapaun lagi.</li>
                            <li>Sistem akan menyegel riwayat sebagai <strong class="font-bold">Arsip Keluar</strong>. Bila kelak mendaftar lagi, wajib menyetor simpanan sebesar {{ format_rupiah($analisis['total_dikembalikan']) }}.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form action="{{ route('anggota.keluar.proses', $anggota) }}" method="POST" onsubmit="return confirm('MOHON DIPERHATIKAN: Anda akan menghapus NIP {{ $anggota->nip }} dari partisipasi aktif koperasi. Seluruh simpanan akan ditarik. Lanjutkan?')">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-[13px] font-bold text-stone-700 mb-1.5">Catatan Keluar / Alasan (Opsional)</label>
                        <textarea name="catatan" rows="2" class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none" placeholder="Isi alasan mutasi jabatan, pensiun, dsb..."></textarea>
                    </div>

                    <div class="flex items-start gap-3 bg-white p-4 rounded-xl border border-stone-200">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="konfirmasi" id="konfirmasi" value="1" required class="w-4 h-4 rounded border-stone-300 text-red-600 focus:ring-red-500 cursor-pointer">
                        </div>
                        <label for="konfirmasi" class="text-[13px] text-stone-700 font-medium cursor-pointer leading-tight pt-0.5">
                            Saya, pengurus koperasi dengan ini mengonfirmasi tindakan pengeluaran <strong>{{ $anggota->nama }}</strong> dan mengesahkan pencairan simpanan terkait.
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition-all shadow-md active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        SETUJUI & PROSES PENGELUARAN ANGGOTA
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Arsip Keluar Sebelumnya (Kalau Ada) --}}
    @if($analisis['arsip_sebelumnya'])
    <div class="bg-stone-50 border border-stone-200 rounded-2xl p-6 shadow-sm">
        <h4 class="text-sm font-bold text-stone-600 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Riwayat Riwayat Keluar Sebelumnya
        </h4>
        <div class="bg-white rounded-xl p-5 border border-stone-200 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-0">
                <div class="flex flex-col text-sm border-b sm:border-b-0 sm:border-r border-stone-100 pb-3 sm:pb-0 sm:pr-4">
                    <span class="text-[10px] uppercase font-bold text-stone-400 tracking-wider mb-1">Tanggal Keluar (Mutasi)</span>
                    <span class="font-semibold text-stone-800">{{ $analisis['arsip_sebelumnya']->tanggal_keluar->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex flex-col text-sm border-b sm:border-b-0 sm:border-r border-stone-100 py-3 sm:py-0 sm:px-4">
                    <span class="text-[10px] uppercase font-bold text-stone-400 tracking-wider mb-1">Total Dikembalikan Saat Itu</span>
                    <span class="font-mono font-bold text-stone-800">{{ format_rupiah($analisis['arsip_sebelumnya']->total_simpanan_dikembalikan) }}</span>
                </div>
                <div class="flex flex-col text-sm pt-3 sm:pt-0 sm:pl-4">
                    <span class="text-[10px] uppercase font-bold text-amber-600 tracking-wider mb-1">Daftar Ulang Wajib Setor</span>
                    <span class="font-mono font-black text-amber-700 bg-amber-50 px-2 py-0.5 rounded self-start">{{ format_rupiah($analisis['arsip_sebelumnya']->nominal_wajib_setor_ulang) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

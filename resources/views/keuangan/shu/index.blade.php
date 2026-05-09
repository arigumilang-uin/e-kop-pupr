@extends('layouts.app')

@section('title', 'Simulasi S.H.U')
@section('subtitle', 'Sisa Hasil Usaha — Konfigurasi komponen dan distribusi dapat diatur oleh Pengurus')

@section('content')
<div class="space-y-6">

    {{-- Filter Tahun --}}
    <div class="flex items-center justify-between bg-white px-5 py-4 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Tahun Buku</h3>
                <p class="text-xs text-slate-500">Pilih tahun periode perhitungan</p>
            </div>
        </div>

        <form action="{{ route('keuangan.shu') }}" method="GET" class="flex items-center gap-2">
            <select name="tahun" class="px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-semibold" onchange="this.form.submit()">
                @foreach($tahunTersedia as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ======================== --}}
        {{-- Penyusun Nilai SHU       --}}
        {{-- ======================== --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 flex items-center gap-2 mb-6">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Penyusun Nilai S.H.U
                </h3>

                <div class="space-y-4">
                    {{-- Pendapatan --}}
                    <div>
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-2">Pendapatan (+)</p>
                        <div class="space-y-2">
                            @foreach($shu['pendapatan_items'] as $item)
                            <div class="flex justify-between items-center text-sm border-b border-dashed border-slate-200 pb-1">
                                <span class="text-slate-500">{{ $item['nama'] }}</span>
                                <span class="font-medium text-slate-700 font-mono">{{ format_rupiah($item['nominal']) }}</span>
                            </div>
                            @endforeach
                            @if(empty($shu['pendapatan_items']))
                            <p class="text-xs text-slate-400 italic">Belum ada komponen pendapatan yang dikonfigurasi.</p>
                            @endif
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold text-emerald-600 mt-2 bg-emerald-50 px-3 py-2 rounded-lg">
                            <span>TOTAL PENDAPATAN</span>
                            <span>{{ format_rupiah($shu['total_pendapatan']) }}</span>
                        </div>
                    </div>

                    {{-- Beban --}}
                    <div class="pt-2">
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-2">Beban / Pengeluaran (−)</p>
                        <div class="space-y-2">
                            @foreach($shu['beban_items'] as $item)
                            <div class="flex justify-between items-center text-sm border-b border-dashed border-slate-200 pb-1">
                                <span class="text-slate-500">{{ $item['nama'] }}</span>
                                <span class="font-medium text-red-600 font-mono">{{ format_rupiah($item['nominal']) }}</span>
                            </div>
                            @endforeach
                            @if(empty($shu['beban_items']))
                            <p class="text-xs text-slate-400 italic">Belum ada komponen beban yang dikonfigurasi.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t-2 border-slate-800 pt-4">
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase">SISA HASIL USAHA (SHU) BERSIH</p>
                        <p class="text-[10px] text-slate-400">Total Pendapatan − Total Beban</p>
                    </div>
                    <p class="text-3xl font-black {{ $shu['shu_bersih'] >= 0 ? 'text-slate-800' : 'text-red-500' }}">
                        {{ format_rupiah($shu['shu_bersih']) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ======================== --}}
        {{-- Distribusi SHU           --}}
        {{-- ======================== --}}
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 rounded-2xl p-6 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Simulasi Pembagian SHU
                    </h3>
                </div>

                @if($shu['shu_bersih'] <= 0)
                <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-4 text-center">
                    <p class="text-amber-400 text-sm font-medium mb-1">SHU Negatif atau 0</p>
                    <p class="text-slate-400 text-xs">Tidak ada Sisa Hasil Usaha yang dapat dibagikan pada tahun {{ $tahun }}.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($shu['distribusi_items'] as $d)
                    <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-3 flex justify-between items-center hover:bg-slate-800 transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-slate-200">{{ $d['nama'] }}</p>
                            @if($d['deskripsi'])
                            <p class="text-[10px] text-slate-400">{{ $d['deskripsi'] }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-white">{{ format_rupiah($d['nominal']) }}</p>
                            <p class="text-xs font-mono text-amber-400">{{ number_format($d['persen'], 1) }}%</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-t border-slate-700 flex justify-between items-center">
                    <span class="text-xs text-slate-400">Total Alokasi</span>
                    <span class="text-sm font-bold {{ abs($shu['total_persen_distribusi'] - 100) < 0.01 ? 'text-emerald-400' : 'text-amber-400' }}">
                        {{ number_format($shu['total_persen_distribusi'], 1) }}%
                        @if(abs($shu['total_persen_distribusi'] - 100) > 0.01)
                        <span class="text-[10px] text-amber-400/70">(belum 100%)</span>
                        @endif
                    </span>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ============================================== --}}
    {{-- PANEL KONFIGURASI: Komponen & Distribusi SHU   --}}
    {{-- ============================================== --}}
    @can('shu.manage')
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Panel Konfigurasi SHU (Pengurus)
            </h3>
            <p class="text-xs text-slate-500 mt-1">Tambah, hapus, atau ubah parameter komponen pendapatan/beban dan alokasi distribusi SHU. Konfigurasi ini bersifat fleksibel dan dapat disesuaikan setiap tahun sesuai hasil Rapat Anggota Tahunan (RAT).</p>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ======================== --}}
            {{-- Komponen SHU             --}}
            {{-- ======================== --}}
            <div>
                <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Komponen Pendapatan & Beban</h4>

                <div class="space-y-2 mb-4">
                    @foreach($komponenAll as $k)
                    <div class="border border-slate-200 rounded-xl p-3 {{ !$k->is_aktif ? 'opacity-50 bg-slate-50' : 'bg-white' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase
                                        {{ $k->tipe === 'pendapatan' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $k->tipe }}
                                    </span>
                                    <span class="text-sm font-semibold text-slate-700 truncate">{{ $k->nama }}</span>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-0.5 font-mono">sumber: {{ $k->sumber_data }}</p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                {{-- Toggle Aktif/Nonaktif --}}
                                <form action="{{ route('shu.komponen.update', $k->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="nama" value="{{ $k->nama }}">
                                    <input type="hidden" name="tipe" value="{{ $k->tipe }}">
                                    <input type="hidden" name="sumber_data" value="{{ $k->sumber_data }}">
                                    <input type="hidden" name="is_aktif" value="{{ $k->is_aktif ? '0' : '1' }}">
                                    <button type="submit" class="p-1 rounded-lg hover:bg-slate-100 transition-colors" title="{{ $k->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        @if($k->is_aktif)
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        @endif
                                    </button>
                                </form>
                                {{-- Hapus --}}
                                <form action="{{ route('shu.komponen.destroy', $k->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus komponen ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1 rounded-lg hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Tambah Komponen Baru --}}
                <form action="{{ route('shu.komponen.store') }}" method="POST" class="border-2 border-dashed border-slate-200 rounded-xl p-4 bg-slate-50/50">
                    @csrf
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">+ Tambah Komponen Baru</p>
                    <div class="space-y-2">
                        <input type="text" name="nama" placeholder="Nama komponen" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <div class="grid grid-cols-2 gap-2">
                            <select name="tipe" required class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                <option value="pendapatan">Pendapatan</option>
                                <option value="beban">Beban</option>
                            </select>
                            <select name="sumber_data" required class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                @foreach($sumberTersedia as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="text" name="deskripsi" placeholder="Deskripsi (opsional)" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                            Tambah Komponen
                        </button>
                    </div>
                </form>
            </div>

            {{-- ======================== --}}
            {{-- Distribusi / Alokasi SHU --}}
            {{-- ======================== --}}
            <div>
                <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Alokasi Distribusi SHU</h4>

                <div class="space-y-2 mb-4">
                    @foreach($distribusiAll as $d)
                    <form action="{{ route('shu.distribusi.update', $d->id) }}" method="POST"
                          class="border border-slate-200 rounded-xl p-3 {{ !$d->is_aktif ? 'opacity-50 bg-slate-50' : 'bg-white' }}">
                        @csrf @method('PATCH')
                        <div class="flex items-center gap-2">
                            <div class="flex-1 min-w-0">
                                <input type="text" name="nama" value="{{ $d->nama }}" class="w-full px-2 py-1 border border-transparent hover:border-slate-300 focus:border-blue-500 rounded text-sm font-semibold text-slate-700 bg-transparent focus:bg-white transition-all focus:ring-2 focus:ring-blue-500/20">
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <input type="number" name="persen" value="{{ $d->persen }}" step="0.01" min="0" max="100"
                                       class="w-20 px-2 py-1 border border-slate-300 rounded-lg text-sm font-mono text-right focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                <span class="text-xs text-slate-500">%</span>
                                <input type="hidden" name="deskripsi" value="{{ $d->deskripsi }}">
                                <button type="submit" class="p-1 rounded-lg hover:bg-blue-50 transition-colors" title="Simpan">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </div>
                            {{-- Hapus --}}
                            <div class="shrink-0">
                                <button type="button" onclick="if(confirm('Hapus alokasi ini?')) { this.closest('form').action='{{ route('shu.distribusi.destroy', $d->id) }}'; this.closest('form').querySelector('[name=_method]').value='DELETE'; this.closest('form').submit(); }"
                                        class="p-1 rounded-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </form>
                    @endforeach
                </div>

                {{-- Total Persen --}}
                @php $totalPersen = $distribusiAll->where('is_aktif', true)->sum('persen'); @endphp
                <div class="flex justify-between items-center text-sm mb-4 px-3 py-2 rounded-lg {{ abs($totalPersen - 100) < 0.01 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                    <span class="font-semibold">Total Alokasi Aktif</span>
                    <span class="font-bold font-mono">{{ number_format($totalPersen, 1) }}%
                        @if(abs($totalPersen - 100) > 0.01)
                        <span class="text-xs font-normal">(harus 100%)</span>
                        @endif
                    </span>
                </div>

                {{-- Tambah Distribusi Baru --}}
                <form action="{{ route('shu.distribusi.store') }}" method="POST" class="border-2 border-dashed border-slate-200 rounded-xl p-4 bg-slate-50/50">
                    @csrf
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">+ Tambah Alokasi Baru</p>
                    <div class="space-y-2">
                        <input type="text" name="nama" placeholder="Nama alokasi (misal: Dana Sosial)" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="persen" placeholder="Persentase" step="0.01" min="0" max="100" required class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <input type="text" name="deskripsi" placeholder="Deskripsi (opsional)" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                            Tambah Alokasi
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    @endcan

    {{-- ============================================== --}}
    {{-- PAYOUT STATUS & PRORATA PER ANGGOTA            --}}
    {{-- ============================================== --}}
    @if($payoutTahunIni)
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-emerald-800">SHU Tahun {{ $tahun }} Sudah Didistribusikan</h3>
                <p class="text-sm text-emerald-700 mt-1">
                    Dilaksanakan pada {{ $payoutTahunIni->created_at->translatedFormat('d F Y, H:i') }} WIB
                    oleh {{ $payoutTahunIni->eksekutor->nama ?? 'Sistem' }}.
                </p>
                <div class="flex flex-wrap gap-4 mt-3 text-sm">
                    <div>
                        <span class="text-emerald-600 font-medium">Total Didistribusikan:</span>
                        <span class="font-bold font-mono text-emerald-800">{{ format_rupiah($payoutTahunIni->total_terdistribusi) }}</span>
                    </div>
                    <div>
                        <span class="text-emerald-600 font-medium">Penerima:</span>
                        <span class="font-bold text-emerald-800">{{ $payoutTahunIni->jumlah_penerima }} anggota</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($prorata && $prorata['detail']->isNotEmpty())
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Distribusi Prorata per Anggota — Tahun {{ $tahun }}
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    Jasa Modal: <span class="font-mono font-semibold">{{ format_rupiah($danaJasaModal) }}</span> ·
                    Jasa Usaha: <span class="font-mono font-semibold">{{ format_rupiah($danaJasaUsaha) }}</span> ·
                    {{ $prorata['ringkasan']['jumlah_penerima'] }} penerima
                </p>
            </div>

            {{-- One-Click Payout Button --}}
            @can('shu.manage')
            @if(!$payoutTahunIni)
            <form action="{{ route('shu.payout') }}" method="POST" class="shrink-0"
                  onsubmit="return confirm('⚠️ PERHATIAN: Anda akan mendistribusikan SHU tahun {{ $tahun }} ke Simpanan Sukarela seluruh anggota.\n\nTotal: {{ format_rupiah($prorata['ringkasan']['total_terdistribusi']) }} untuk {{ $prorata['ringkasan']['jumlah_penerima'] }} anggota.\n\nAksi ini TIDAK BISA DIBATALKAN. Lanjutkan?')">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Eksekusi Distribusi SHU {{ $tahun }}
                </button>
            </form>
            @endif
            @endcan
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">NIP</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Nama Anggota</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Simpanan Neto</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Jasa Modal</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Bunga Dibayar</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Jasa Usaha</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Total SHU</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($prorata['detail'] as $idx => $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                        <td class="px-4 py-3 font-mono text-slate-600 whitespace-nowrap">{{ $item->nip }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">{{ $item->nama }}</td>
                        <td class="px-4 py-3 font-mono text-slate-700 text-right whitespace-nowrap">{{ format_rupiah($item->simpanan) }}</td>
                        <td class="px-4 py-3 font-mono text-emerald-600 font-semibold text-right whitespace-nowrap">
                            {{ format_rupiah($item->jasa_modal) }}
                            <span class="text-[10px] text-slate-400 ml-1">({{ $item->proporsi_modal }}%)</span>
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-700 text-right whitespace-nowrap">{{ format_rupiah($item->bunga_dibayar) }}</td>
                        <td class="px-4 py-3 font-mono text-blue-600 font-semibold text-right whitespace-nowrap">
                            {{ format_rupiah($item->jasa_usaha) }}
                            <span class="text-[10px] text-slate-400 ml-1">({{ $item->proporsi_usaha }}%)</span>
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 text-right whitespace-nowrap">{{ format_rupiah($item->total_shu) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-xs font-bold text-slate-600 uppercase">Total Distribusi</td>
                        <td class="px-4 py-3 font-mono font-bold text-emerald-700 text-right">{{ format_rupiah($prorata['detail']->sum('jasa_modal')) }}</td>
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3 font-mono font-bold text-blue-700 text-right">{{ format_rupiah($prorata['detail']->sum('jasa_usaha')) }}</td>
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 text-right text-base">{{ format_rupiah($prorata['ringkasan']['total_terdistribusi']) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @elseif($shu['shu_bersih'] > 0 && !$payoutTahunIni)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
        <p class="text-sm text-amber-800 font-medium">
            SHU Bersih {{ format_rupiah($shu['shu_bersih']) }} tersedia, namun tidak ditemukan pos distribusi "Jasa Modal" atau "Jasa Anggota" yang aktif, atau belum ada data simpanan/pinjaman anggota.
        </p>
    </div>
    @endif

</div>
@endsection

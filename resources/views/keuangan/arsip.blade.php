@extends('layouts.app')

@section('title', 'Arsip Transaksi Keuangan')
@section('subtitle', 'Riwayat seluruh transaksi yang telah dieksekusi dalam rentang waktu tertentu')

@section('actions')
<a href="{{ route('keuangan.laporan') }}" class="py-2.5 px-4 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-sm font-medium transition-colors">
    ← Laporan Keuangan
</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ============================== --}}
    {{-- FILTER BAR                     --}}
    {{-- ============================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <form method="GET" action="{{ route('keuangan.arsip') }}" id="filterForm">
            <input type="hidden" name="tab" value="{{ $tab }}">

            {{-- Preset Buttons --}}
            <div class="flex flex-wrap gap-2 mb-4">
                @php
                    $presets = [
                        'minggu_ini' => 'Minggu Ini',
                        'bulan_ini' => 'Bulan Ini',
                        '3_bulan' => '3 Bulan Terakhir',
                        '6_bulan' => '6 Bulan Terakhir',
                        'tahun_ini' => 'Tahun Ini',
                        'custom' => 'Kustom',
                    ];
                @endphp
                @foreach($presets as $key => $label)
                <button type="submit" name="preset" value="{{ $key }}"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $preset === $key ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Custom Date Range --}}
            <div class="flex flex-wrap items-end gap-3 {{ $preset !== 'custom' ? 'opacity-50' : '' }}" id="customRange">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Dari Tanggal</label>
                    <x-datepicker name="dari" :value="$dari->format('Y-m-d')" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Sampai Tanggal</label>
                    <x-datepicker name="sampai" :value="$sampai->format('Y-m-d')" />
                </div>
                <button type="submit" name="preset" value="custom"
                    class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                    Terapkan
                </button>
            </div>

            <p class="text-xs text-slate-400 mt-3">
                Menampilkan data dari <strong class="text-slate-600">{{ $dari->translatedFormat('d F Y') }}</strong>
                s/d <strong class="text-slate-600">{{ $sampai->translatedFormat('d F Y') }}</strong>
            </p>
        </form>
    </div>

    {{-- ============================== --}}
    {{-- RINGKASAN KARTU                --}}
    {{-- ============================== --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">Simpanan Masuk</p>
            <p class="text-lg font-bold font-mono text-violet-600 truncate">{{ format_rupiah($ringkasan['totalSimpanan']) }}</p>
            <p class="text-slate-400 text-[9px] mt-1">{{ $simpanans->count() }} transaksi</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">Angsuran Masuk</p>
            <p class="text-lg font-bold font-mono text-emerald-600 truncate">{{ format_rupiah($ringkasan['totalAngsuran']) }}</p>
            <p class="text-slate-400 text-[9px] mt-1">{{ $angsurans->count() }} lunas</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">Pinjaman Cair</p>
            <p class="text-lg font-bold font-mono text-amber-600 truncate">{{ format_rupiah($ringkasan['totalPinjamanCair']) }}</p>
            <p class="text-slate-400 text-[9px] mt-1">{{ $pinjamans->count() }} pinjaman</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">Pengeluaran Kas</p>
            <p class="text-lg font-bold font-mono text-red-500 truncate">{{ format_rupiah($ringkasan['totalPengeluaranKas']) }}</p>
            <p class="text-slate-400 text-[9px] mt-1">{{ $pengeluarans->count() }} bayar beban</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">Penarikan (Keluar)</p>
            <p class="text-lg font-bold font-mono text-red-600 truncate">{{ format_rupiah($ringkasan['totalPenarikanSimpanan']) }}</p>
            <p class="text-slate-400 text-[9px] mt-1">{{ $penarikans->count() }} penarikan</p>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white shadow-lg shadow-blue-600/20">
            <p class="text-blue-200 text-xs font-medium mb-1">Volume Transaksi</p>
            <p class="text-xl font-bold font-mono">{{ format_rupiah($ringkasan['grandTotal']) }}</p>
            <p class="text-blue-200 text-[10px] mt-1">Total seluruh aktivitas</p>
        </div>
    </div>

    {{-- ============================== --}}
    {{-- TAB NAVIGATION                 --}}
    {{-- ============================== --}}
    @php
        $tabs = [
            'simpanan' => ['label' => 'Simpanan (' . $simpanans->count() . ')', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
            'tarik' => ['label' => 'Penarikan Simpanan (' . $penarikans->count() . ')', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
            'angsuran' => ['label' => 'Angsuran Lunas (' . $angsurans->count() . ')', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            'pinjaman' => ['label' => 'Pinjaman Dicairkan (' . $pinjamans->count() . ')', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            'pengeluaran' => ['label' => 'Pengeluaran Manual (' . $pengeluarans->count() . ')', 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
        ];
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-1.5 flex flex-wrap gap-1">
        @foreach($tabs as $key => $t)
        <a href="{{ route('keuangan.arsip', array_merge(request()->all(), ['tab' => $key])) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all {{ $tab === $key ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/></svg>
            {{ $t['label'] }}
        </a>
        @endforeach
    </div>

    {{-- ============================== --}}
    {{-- TAB: SIMPANAN                  --}}
    {{-- ============================== --}}
    @if($tab === 'simpanan')

    {{-- Summary per Jenis --}}
    @if($simpananPerJenis->isNotEmpty())
    <div class="flex flex-wrap gap-3">
        @foreach($simpananPerJenis as $sj)
        <div class="bg-violet-50 border border-violet-200 rounded-xl px-4 py-2.5 text-sm">
            <span class="font-semibold text-violet-800">{{ $sj['nama'] }}</span>
            <span class="text-violet-600 font-mono ml-2">{{ format_rupiah($sj['total']) }}</span>
            <span class="text-violet-400 text-xs ml-1">({{ $sj['count'] }}x)</span>
        </div>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No Ref</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Nominal</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Periode</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($simpanans as $s)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-blue-600 text-xs font-semibold whitespace-nowrap">{{ $s->no_referensi }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="text-sm font-medium text-slate-800">{{ $s->anggota->nama ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $s->anggota->nip ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $jenisColors = ['POKOK' => 'blue', 'WAJIB' => 'violet', 'SWP' => 'amber', 'SUKARELA' => 'emerald'];
                                $kode = $s->jenisSimpanan->kode ?? '';
                                $c = $jenisColors[$kode] ?? 'slate';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-{{ $c }}-50 text-{{ $c }}-700 border border-{{ $c }}-200">
                                {{ $s->jenisSimpanan->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-slate-700 whitespace-nowrap">{{ format_rupiah($s->nominal) }}</td>
                        <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $s->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">
                            @if($s->bulan_untuk)
                            {{ \Carbon\Carbon::create(null, $s->bulan_untuk, 1)->translatedFormat('F') }} {{ $s->tahun_untuk }}
                            @else
                            <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-500 text-xs max-w-[200px] truncate">{{ $s->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            Tidak ada transaksi simpanan pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($simpanans->isNotEmpty())
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-xs font-bold text-slate-700 uppercase">Total {{ $simpanans->count() }} Transaksi</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-violet-700">{{ format_rupiah($ringkasan['totalSimpanan']) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif

    {{-- ============================== --}}
    {{-- TAB: ANGSURAN LUNAS            --}}
    {{-- ============================== --}}
    @if($tab === 'angsuran')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Ref Pinjaman</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Ke-</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Pokok</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Bunga</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Total</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($angsurans as $a)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-blue-600 text-xs font-semibold whitespace-nowrap">{{ $a->pinjaman->no_referensi ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="text-sm font-medium text-slate-800">{{ $a->pinjaman->anggota->nama ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $a->pinjaman->anggota->nip ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                                {{ $a->angsuran_ke }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-slate-600 whitespace-nowrap">{{ format_rupiah($a->nominal_pokok) }}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-600 whitespace-nowrap">{{ format_rupiah($a->nominal_bunga) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-emerald-700 whitespace-nowrap">{{ format_rupiah($a->nominal_total) }}</td>
                        <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $a->tanggal_jatuh_tempo->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ $a->tanggal_bayar->translatedFormat('d M Y') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                            Tidak ada angsuran yang dilunasi pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($angsurans->isNotEmpty())
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-xs font-bold text-slate-700 uppercase">Total {{ $angsurans->count() }} Angsuran</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-700">{{ format_rupiah($ringkasan['totalAngsuranPokok']) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-700">{{ format_rupiah($ringkasan['totalAngsuranBunga']) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700">{{ format_rupiah($ringkasan['totalAngsuran']) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif

    {{-- ============================== --}}
    {{-- TAB: PINJAMAN DICAIRKAN        --}}
    {{-- ============================== --}}
    @if($tab === 'pinjaman')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No Ref</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Nominal Pinjaman</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Total Potongan</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Dana Diterima</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Tenor</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Approval</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pinjamans as $p)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('pinjaman.show', $p->id) }}" class="font-mono text-blue-600 text-xs font-semibold hover:underline">{{ $p->no_referensi }}</a>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="text-sm font-medium text-slate-800">{{ $p->anggota->nama ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $p->anggota->nip ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-slate-700 whitespace-nowrap">{{ format_rupiah($p->nominal_pinjaman) }}</td>
                        <td class="px-4 py-3 text-right font-mono text-red-500 whitespace-nowrap">-{{ format_rupiah($p->total_potongan) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-emerald-700 whitespace-nowrap">{{ format_rupiah($p->dana_diterima) }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ $p->tenor_bulan }} bln</td>
                        <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $p->tanggal_approval->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if($p->status->value === 'berjalan')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Berjalan</span>
                            @elseif($p->status->value === 'lunas')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">Lunas</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200 capitalize">{{ $p->status->label() }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                            Tidak ada pinjaman yang dicairkan pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($pinjamans->isNotEmpty())
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-xs font-bold text-slate-700 uppercase">Total {{ $pinjamans->count() }} Pinjaman</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-700">{{ format_rupiah($ringkasan['totalPinjamanCair']) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-red-500">-{{ format_rupiah($pinjamans->sum('total_potongan')) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700">{{ format_rupiah($ringkasan['totalPinjamanDiterima']) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif

    {{-- ============================== --}}
    {{-- TAB: PENARIKAN                 --}}
    {{-- ============================== --}}
    @if($tab === 'tarik')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No Ref</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Nominal</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemroses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penarikans as $p)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-red-600 text-xs font-semibold whitespace-nowrap">{{ $p->no_referensi }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="text-sm font-medium text-slate-800">{{ $p->anggota->nama ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $p->anggota->nip ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $jenisColors = ['POKOK' => 'blue', 'WAJIB' => 'violet', 'SWP' => 'amber', 'SUKARELA' => 'emerald'];
                                $kode = $p->jenisSimpanan->kode ?? '';
                                $c = $jenisColors[$kode] ?? 'slate';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-{{ $c }}-50 text-{{ $c }}-700 border border-{{ $c }}-200">
                                {{ $p->jenisSimpanan->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-red-600 whitespace-nowrap">{{ format_rupiah($p->nominal) }}</td>
                        <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $p->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 text-slate-700 text-xs max-w-[200px] truncate" title="{{ $p->keterangan }}">{{ $p->keterangan ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $p->pemroses->nama ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            Tidak ada penarikan simpanan pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($penarikans->isNotEmpty())
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-xs font-bold text-slate-700 uppercase">Total {{ $penarikans->count() }} Penarikan</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-red-600">{{ format_rupiah($ringkasan['totalPenarikanSimpanan']) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif

    {{-- ============================== --}}
    {{-- TAB: PENGELUARAN               --}}
    {{-- ============================== --}}
    @if($tab === 'pengeluaran')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No Ref</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Nominal</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pencatat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pengeluarans as $p)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-red-600 text-xs font-semibold whitespace-nowrap">{{ $p->no_referensi }}</td>
                        <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $p->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                {{ $p->kategori->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700 text-xs">{{ $p->keterangan }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-red-600 whitespace-nowrap">{{ format_rupiah($p->nominal) }}</td>
                        <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $p->pencatat->nama ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                            Tidak ada pengeluaran kas pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($pengeluarans->isNotEmpty())
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-xs font-bold text-slate-700 uppercase">Total {{ $pengeluarans->count() }} Pengeluaran</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-red-600">{{ format_rupiah($ringkasan['totalPengeluaranKas']) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif

</div>
@endsection

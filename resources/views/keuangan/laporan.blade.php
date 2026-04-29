@extends('layouts.app')

@section('title', 'Laporan Keuangan Detail')
@section('subtitle', 'Rincian lengkap kondisi keuangan Koperasi PUPR Riau')

@section('actions')
<a href="{{ route('dashboard') }}" class="py-2.5 px-4 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-sm font-medium transition-colors">
    ← Dashboard
</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-1.5 flex flex-wrap gap-1">
        @php
            $tabs = [
                'ringkasan' => ['label' => 'Ringkasan Aset', 'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
                'piutang' => ['label' => 'Piutang Koperasi', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                'simpanan' => ['label' => 'Simpanan Anggota', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                'kas' => ['label' => 'Arus Kas', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($tabs as $key => $t)
        <a href="{{ route('keuangan.laporan', ['tab' => $key]) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all {{ $tab === $key ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/></svg>
            {{ $t['label'] }}
        </a>
        @endforeach
    </div>

    {{-- ======================== --}}
    {{-- TAB: RINGKASAN ASET     --}}
    {{-- ======================== --}}
    @if($tab === 'ringkasan')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white shadow-lg shadow-blue-600/20">
            <p class="text-blue-200 text-sm font-medium mb-1">Kas Saldo Tersedia</p>
            <p class="text-2xl font-bold font-mono">{{ format_rupiah($ringkasan['saldoKoperasi']) }}</p>
            <p class="text-blue-200 text-xs mt-2">Dana liquid di kas koperasi</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-sm font-medium mb-1">Piutang Koperasi</p>
            <p class="text-2xl font-bold font-mono text-amber-600">{{ format_rupiah($ringkasan['piutangBerjalan']) }}</p>
            <p class="text-slate-400 text-xs mt-2">Sisa pokok pinjaman berjalan</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-sm font-medium mb-1">Simpanan Anggota</p>
            <p class="text-2xl font-bold font-mono text-violet-600">{{ format_rupiah($ringkasan['simpananBersih']) }}</p>
            <p class="text-slate-400 text-xs mt-2">Titipan dana anggota (neto)</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-sm font-medium mb-1">Estimasi Total Aset</p>
            <p class="text-2xl font-bold font-mono text-emerald-600">{{ format_rupiah($ringkasan['totalAset']) }}</p>
            <p class="text-slate-400 text-xs mt-2">Kas + Piutang</p>
        </div>
    </div>

    {{-- Komposisi Aset --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-5 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                </div>
                Komposisi Total Aset
            </h3>
            @php
                $asetTotal = $ringkasan['totalAset'] > 0 ? $ringkasan['totalAset'] : 1;
                $pctKas = round(($ringkasan['saldoKoperasi'] / $asetTotal) * 100, 1);
                $pctPiutang = round(($ringkasan['piutangBerjalan'] / $asetTotal) * 100, 1);
            @endphp
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600 font-medium">Kas Saldo (Liquid)</span>
                        <span class="font-mono font-semibold text-blue-600">{{ format_rupiah($ringkasan['saldoKoperasi']) }} <span class="text-slate-400 text-xs">({{ $pctKas }}%)</span></span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full transition-all" style="width: {{ $pctKas }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600 font-medium">Piutang Pokok Beredar</span>
                        <span class="font-mono font-semibold text-amber-600">{{ format_rupiah($ringkasan['piutangBerjalan']) }} <span class="text-slate-400 text-xs">({{ $pctPiutang }}%)</span></span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3">
                        <div class="bg-amber-500 h-3 rounded-full transition-all" style="width: {{ $pctPiutang }}%"></div>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                    <span class="font-bold text-slate-800">Total Kekayaan Bersih</span>
                    <span class="font-mono text-lg font-bold text-emerald-600">{{ format_rupiah($ringkasan['totalAset']) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-5 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                Komposisi Simpanan per Jenis
            </h3>
            <div class="space-y-3">
                @foreach($ringkasan['simpananPerJenis'] as $sim)
                @php $pct = $ringkasan['simpananBersih'] > 0 ? round(($sim->total / $ringkasan['simpananBersih']) * 100, 1) : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600 font-medium">{{ $sim->nama }} <span class="text-[10px] text-slate-400 font-mono">({{ $sim->kode }})</span></span>
                        <span class="font-mono font-semibold text-slate-700">{{ format_rupiah($sim->total) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5">
                        <div class="bg-violet-500 h-2.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                    <span class="font-bold text-slate-800">Total Simpanan Neto</span>
                    <span class="font-mono font-bold text-violet-600">{{ format_rupiah($ringkasan['simpananBersih']) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ======================== --}}
    {{-- TAB: PIUTANG KOPERASI   --}}
    {{-- ======================== --}}
    @if($tab === 'piutang')
    @php $totalPiutangAll = $piutangAnggota->sum('total_belum'); @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-xs font-medium mb-1">Total Piutang Beredar</p>
            <p class="text-xl font-bold font-mono text-amber-600">{{ format_rupiah($totalPiutangAll) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-xs font-medium mb-1">Jumlah Peminjam Aktif</p>
            <p class="text-xl font-bold text-slate-800">{{ $piutangGrouped->count() }} <span class="text-sm font-normal text-slate-400">anggota</span></p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-xs font-medium mb-1">Total Pinjaman Berjalan</p>
            <p class="text-xl font-bold text-slate-800">{{ $piutangAnggota->count() }} <span class="text-sm font-normal text-slate-400">pinjaman</span></p>
        </div>
    </div>

    {{-- Detail per Anggota --}}
    @foreach($piutangGrouped as $anggotaId => $loans)
    @php $anggota = $loans->first()->anggota; @endphp
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 font-bold text-sm">
                    {{ strtoupper(substr($anggota->nama, 0, 2)) }}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800">{{ $anggota->nama }}</h4>
                    <p class="text-xs text-slate-500">NIP: {{ $anggota->nip }} · {{ $anggota->bidang->nama_bidang ?? '-' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500">Total Sisa Utang</p>
                <p class="text-lg font-bold font-mono text-amber-600">{{ format_rupiah($loans->sum('total_belum')) }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">
                    Pokok {{ format_rupiah($loans->sum('total_belum_pokok')) }} + 
                    Bunga {{ format_rupiah($loans->sum('total_belum_bunga')) }}
                </p>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach($loans as $loan)
            <div class="px-6 py-5">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4 mb-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-400">Ref: <span class="font-mono text-blue-600 font-semibold">{{ $loan->pinjaman->no_referensi }}</span></p>
                        <p class="text-sm text-slate-700">Pokok: <span class="font-mono font-semibold">{{ format_rupiah($loan->pinjaman->nominal_pinjaman) }}</span> · Tenor: {{ $loan->tenor }} bulan · Bunga: {{ number_format($loan->pinjaman->bunga_persen, 1) }}%</p>
                    </div>
                    <div class="text-right text-xs text-slate-500 space-y-0.5 shrink-0">
                        <p>Mulai: <span class="text-slate-700 font-medium">{{ $loan->mulai ? $loan->mulai->format('d M Y') : '-' }}</span></p>
                        <p>Est. Lunas: <span class="text-slate-700 font-medium">{{ $loan->estimasi_lunas ? \Carbon\Carbon::parse($loan->estimasi_lunas)->format('d M Y') : '-' }}</span></p>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mb-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-500">Progress: {{ $loan->angsuran_lunas }}/{{ $loan->tenor }} angsuran lunas</span>
                        <span class="font-semibold text-slate-700">{{ $loan->progress }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: {{ $loan->progress }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider mb-1">Total Tagihan</p>
                        <p class="text-sm font-bold font-mono text-slate-700">{{ format_rupiah($loan->total_tagihan) }}</p>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-xl">
                        <p class="text-[10px] text-emerald-600 uppercase tracking-wider mb-1">Sudah Dibayar</p>
                        <p class="text-sm font-bold font-mono text-emerald-700">{{ format_rupiah($loan->total_dibayar) }}</p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-xl relative group">
                        <p class="text-[10px] text-amber-600 uppercase tracking-wider mb-1">Belum Dibayar</p>
                        <p class="text-sm font-bold font-mono text-amber-700">{{ format_rupiah($loan->total_belum) }}</p>
                        <p class="text-[9px] text-amber-600/70 mt-1 leading-tight">
                            P: {{ format_rupiah($loan->total_belum_pokok) }}<br>
                            B: {{ format_rupiah($loan->total_belum_bunga) }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($piutangGrouped->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-slate-500 text-sm">Tidak ada pinjaman yang sedang berjalan. Piutang koperasi saat ini Rp 0.</p>
    </div>
    @endif
    @endif

    {{-- ======================== --}}
    {{-- TAB: SIMPANAN ANGGOTA   --}}
    {{-- ======================== --}}
    @if($tab === 'simpanan')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Detail Simpanan per Anggota</h3>
            <p class="text-xs text-slate-400">Total {{ $simpananGrouped->count() }} anggota yang tercatat memiliki simpanan</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider sticky left-0 bg-slate-50/80">No</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider sticky left-10 bg-slate-50/80">Anggota</th>
                        @foreach($jenisKodes as $kode => $nama)
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right whitespace-nowrap">{{ $nama }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-xs font-bold text-slate-700 uppercase tracking-wider text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @foreach($simpananGrouped as $data)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 text-xs text-slate-400 sticky left-0 bg-white">{{ $no++ }}</td>
                        <td class="px-4 py-3 sticky left-10 bg-white">
                            <p class="text-sm font-semibold text-slate-800">{{ $data->nama }}</p>
                            <p class="text-xs text-slate-400 font-mono">{{ $data->nip }}</p>
                        </td>
                        @foreach($jenisKodes as $kode => $nama)
                        <td class="px-4 py-3 text-right font-mono text-sm whitespace-nowrap {{ isset($data->detail[$kode]) ? 'text-slate-700' : 'text-slate-300' }}">
                            {{ isset($data->detail[$kode]) ? format_rupiah($data->detail[$kode]['total']) : '-' }}
                        </td>
                        @endforeach
                        <td class="px-4 py-3 text-right font-mono text-sm font-bold text-violet-700 whitespace-nowrap">
                            {{ format_rupiah($data->grand_total) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-xs font-bold text-slate-700 uppercase">Grand Total</td>
                        @foreach($jenisKodes as $kode => $nama)
                        @php
                            $colTotal = $simpananGrouped->sum(fn($d) => isset($d->detail[$kode]) ? $d->detail[$kode]['total'] : 0);
                        @endphp
                        <td class="px-4 py-3 text-right font-mono text-sm font-bold text-slate-700 whitespace-nowrap">{{ format_rupiah($colTotal) }}</td>
                        @endforeach
                        <td class="px-4 py-3 text-right font-mono text-sm font-bold text-violet-700 whitespace-nowrap">{{ format_rupiah($simpananGrouped->sum('grand_total')) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- ======================== --}}
    {{-- TAB: ARUS KAS           --}}
    {{-- ======================== --}}
    @if($tab === 'kas')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Dana Masuk --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-emerald-700 uppercase tracking-wider mb-5 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
                Dana Masuk (Pemasukan)
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-emerald-50/50 rounded-xl border border-emerald-100">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Setoran Simpanan (Pokok+Wajib+SWP+Sukarela)</p>
                        <p class="text-[11px] text-slate-400">Seluruh simpanan anggota yang masuk ke kas</p>
                    </div>
                    <span class="font-mono font-bold text-emerald-700">{{ format_rupiah($ringkasan['masukSimpanan']) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-emerald-50/50 rounded-xl border border-emerald-100">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Pembayaran Angsuran Pinjaman</p>
                        <p class="text-[11px] text-slate-400">Pokok + bunga dari angsuran yang sudah lunas</p>
                    </div>
                    <span class="font-mono font-bold text-emerald-700">{{ format_rupiah($ringkasan['masukAngsuran']) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-emerald-50/50 rounded-xl border border-emerald-100">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Pendapatan Fee (Dana Resiko + Admin)</p>
                        <p class="text-[11px] text-slate-400">Potongan dari pencairan pinjaman</p>
                    </div>
                    <span class="font-mono font-bold text-emerald-700">{{ format_rupiah($ringkasan['masukFee']) }}</span>
                </div>
                <div class="pt-3 border-t-2 border-emerald-200 flex justify-between items-center">
                    <span class="font-bold text-emerald-800">Total Pemasukan</span>
                    <span class="font-mono text-lg font-bold text-emerald-700">{{ format_rupiah($ringkasan['masukSimpanan'] + $ringkasan['masukAngsuran'] + $ringkasan['masukFee']) }}</span>
                </div>
            </div>
        </div>

        {{-- Dana Keluar --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-red-700 uppercase tracking-wider mb-5 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
                Dana Keluar (Pengeluaran)
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-red-50/50 rounded-xl border border-red-100">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Pencairan Pinjaman (Bruto)</p>
                        <p class="text-[11px] text-slate-400">Nominal pinjaman bruto sebelum potongan 5%</p>
                    </div>
                    <span class="font-mono font-bold text-red-600">{{ format_rupiah($ringkasan['keluarPinjaman']) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-red-50/50 rounded-xl border border-red-100">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Penarikan Simpanan Anggota</p>
                        <p class="text-[11px] text-slate-400">Penarikan dari rekening simpanan anggota</p>
                    </div>
                    <span class="font-mono font-bold text-red-600">{{ format_rupiah($ringkasan['keluarTarik']) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-red-50/50 rounded-xl border border-red-100">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Pengeluaran Kas (Beban Manual)</p>
                        <p class="text-[11px] text-slate-400">Total belanja dan beban operasional</p>
                    </div>
                    <span class="font-mono font-bold text-red-600">{{ format_rupiah($ringkasan['keluarPengeluaranKas']) }}</span>
                </div>
                <div class="pt-3 border-t-2 border-red-200 flex justify-between items-center">
                    <span class="font-bold text-red-800">Total Pengeluaran</span>
                    <span class="font-mono text-lg font-bold text-red-600">{{ format_rupiah($ringkasan['keluarPinjaman'] + $ringkasan['keluarTarik'] + $ringkasan['keluarPengeluaranKas']) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Net Summary --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 text-white shadow-lg shadow-blue-600/20">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-blue-200 text-sm font-medium mb-1">Saldo Bersih Kas Koperasi</p>
                <p class="text-xs text-blue-300">Total Pemasukan − Total Pengeluaran = Kas yang tersedia di rekening koperasi saat ini</p>
            </div>
            <p class="text-3xl font-bold font-mono">{{ format_rupiah($ringkasan['saldoKoperasi']) }}</p>
        </div>
    </div>
    @endif

</div>
@endsection

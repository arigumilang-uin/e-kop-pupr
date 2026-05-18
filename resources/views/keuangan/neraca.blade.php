@extends('layouts.app')

@section('title', 'Neraca Keuangan')
@section('subtitle', 'Laporan Posisi Keuangan Koperasi Simpan Pinjam Konsumen Tirta Bina Karya')

@section('actions')
<div class="flex flex-wrap items-center gap-3 justify-end" x-data="{ open: false }">
    <form method="GET" class="flex items-center gap-2">
        <select name="tahun" class="w-28 py-2.5 px-3 border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] font-bold text-stone-700 bg-white text-sm outline-none">
            @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="h-10 w-10 bg-white border border-stone-200 hover:bg-stone-50 text-[#043d2e] rounded-xl flex items-center justify-center transition-colors shadow-sm flex-shrink-0" title="Terapkan Filter">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </button>
    </form>

    <div class="relative">
        <button @click="open = !open" @click.away="open = false" 
                class="inline-flex items-center gap-2 py-2.5 px-4 rounded-xl bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 hover:text-stone-900 text-sm font-bold transition-all shadow-sm">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Neraca
            <svg class="w-4 h-4 text-stone-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-stone-200 rounded-xl shadow-lg z-50 overflow-hidden" style="display: none;">
            <a href="{{ route('keuangan.neraca.export.excel', ['tahun' => $tahun]) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50 text-stone-700 transition-colors border-b border-stone-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                </div>
                <div><p class="text-sm font-bold">Excel</p><p class="text-[10px] text-stone-500">Format .xlsx</p></div>
            </a>
            <a href="{{ route('keuangan.neraca.export.pdf', ['tahun' => $tahun]) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50 text-stone-700 transition-colors" target="_blank">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div><p class="text-sm font-bold">PDF</p><p class="text-[10px] text-stone-500">Format Dokumen</p></div>
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">

    {{-- Header Dokumen Formal --}}
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden relative">
        <div class="absolute top-0 inset-x-0 h-1 bg-[#043d2e]"></div>
        
        <div class="bg-[#043d2e] px-8 py-10 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-[#022a20]/50 blur-3xl"></div>
            
            <div class="relative z-10">
                <p class="text-amber-400 text-[10px] font-black uppercase tracking-[0.4em] mb-2 opacity-90">Koperasi Simpan Pinjam Konsumen</p>
                <h1 class="text-2xl md:text-3xl font-black text-white tracking-wide mb-4 drop-shadow-md">TIRTA BINA KARYA <br> DINAS PUPRPKPP PROVINSI RIAU</h1>
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-[1px] bg-stone-500/50"></div>
                    <div class="w-2 h-2 rotate-45 bg-amber-400"></div>
                    <div class="w-12 h-[1px] bg-stone-500/50"></div>
                </div>
                <h2 class="text-xl md:text-2xl font-serif font-bold text-white uppercase tracking-[0.2em] mb-2">NERACA</h2>
                <p class="text-stone-300 text-sm font-medium">Per {{ \Carbon\Carbon::parse($neraca['tanggal'])->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <div class="p-6 md:p-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-10">

                {{-- ======================== --}}
                {{--  AKTIVA (ASET)           --}}
                {{-- ======================== --}}
                <div class="flex flex-col h-full">
                    <div class="flex items-center gap-3 border-b-2 border-[#043d2e] pb-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-[#043d2e]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#043d2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-stone-800 uppercase tracking-widest">A K T I V A</h3>
                    </div>

                    <div class="flex-grow space-y-6">
                        @foreach (\App\Models\ParameterNeraca::posisiAktiva() as $posisi)
                            @if(isset($neraca['sections'][$posisi]) && (count($neraca['sections'][$posisi]['items']) > 0))
                                <div>
                                    <div class="bg-stone-50 px-3 py-2 rounded-lg mb-3 border border-stone-100">
                                        <p class="text-[11px] font-black text-stone-500 uppercase tracking-widest">{{ $neraca['sections'][$posisi]['label'] }}</p>
                                    </div>
                                    <div class="space-y-1 px-1">
                                        @foreach($neraca['sections'][$posisi]['items'] as $item)
                                            @include('keuangan.partials._neraca-row', [
                                                'label' => $item['nama'] . ($item['is_pengurang'] ? ' (Pengurang)' : ''), 
                                                'value' => $item['is_pengurang'] ? -$item['nominal'] : $item['nominal']
                                            ])
                                        @endforeach
                                    </div>
                                    @include('keuangan.partials._neraca-subtotal', [
                                        'label' => 'Jumlah ' . ltrim(substr($neraca['sections'][$posisi]['label'], strpos($neraca['sections'][$posisi]['label'], ' ') + 1)), 
                                        'value' => $neraca['sections'][$posisi]['total']
                                    ])
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Total Aktiva — Glassmorphism Card --}}
                    <div class="mt-8 p-5 rounded-2xl shadow-lg border border-white/20 relative overflow-hidden"
                         style="background: linear-gradient(135deg, #043d2e 0%, #065a45 50%, #087a5e 100%);">
                        <div class="absolute inset-0 backdrop-blur-sm bg-white/5"></div>
                        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-emerald-400/10 blur-2xl"></div>
                        <div class="relative z-10 flex justify-between items-center">
                            <span class="text-[13px] font-black uppercase tracking-widest text-emerald-100">TOTAL AKTIVA</span>
                            <span class="text-xl font-mono font-black text-white drop-shadow-sm">{{ format_rupiah($neraca['aktiva_total']) }}</span>
                        </div>
                    </div>
                </div>

                {{-- ======================== --}}
                {{--  PASIVA                  --}}
                {{-- ======================== --}}
                <div class="flex flex-col h-full">
                    <div class="flex items-center gap-3 border-b-2 border-[#043d2e] pb-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-[#043d2e]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#043d2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-stone-800 uppercase tracking-widest">P A S I V A</h3>
                    </div>

                    <div class="flex-grow space-y-6">
                        @foreach (\App\Models\ParameterNeraca::posisiPasiva() as $posisi)
                            @if(isset($neraca['sections'][$posisi]) && (count($neraca['sections'][$posisi]['items']) > 0))
                                <div>
                                    <div class="bg-stone-50 px-3 py-2 rounded-lg mb-3 border border-stone-100">
                                        <p class="text-[11px] font-black text-stone-500 uppercase tracking-widest">{{ $neraca['sections'][$posisi]['label'] }}</p>
                                    </div>
                                    <div class="space-y-1 px-1">
                                        @foreach($neraca['sections'][$posisi]['items'] as $item)
                                            @include('keuangan.partials._neraca-row', [
                                                'label' => $item['nama'] . ($item['is_pengurang'] ? ' (Pengurang)' : ''), 
                                                'value' => $item['is_pengurang'] ? -$item['nominal'] : $item['nominal']
                                            ])
                                        @endforeach
                                    </div>
                                    @include('keuangan.partials._neraca-subtotal', [
                                        'label' => 'Jumlah ' . ltrim(substr($neraca['sections'][$posisi]['label'], strpos($neraca['sections'][$posisi]['label'], ' ') + 1)), 
                                        'value' => $neraca['sections'][$posisi]['total']
                                    ])
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Total Pasiva — Glassmorphism Card --}}
                    <div class="mt-8 p-5 rounded-2xl shadow-lg border border-white/20 relative overflow-hidden"
                         style="background: linear-gradient(135deg, #043d2e 0%, #065a45 50%, #087a5e 100%);">
                        <div class="absolute inset-0 backdrop-blur-sm bg-white/5"></div>
                        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-emerald-400/10 blur-2xl"></div>
                        <div class="relative z-10 flex justify-between items-center">
                            <span class="text-[13px] font-black uppercase tracking-widest text-emerald-100">TOTAL PASIVA</span>
                            <span class="text-xl font-mono font-black text-white drop-shadow-sm">{{ format_rupiah($neraca['pasiva_total']) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Balance Check Footer --}}
        <div class="border-t-4 {{ $neraca['is_balance'] ? 'border-emerald-500 bg-emerald-50' : 'border-red-500 bg-red-50' }} px-8 py-6 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 opacity-[0.03] {{ $neraca['is_balance'] ? 'text-emerald-900' : 'text-red-900' }} pointer-events-none">
                <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24">
                    @if($neraca['is_balance'])
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    @else
                    <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>
                    @endif
                </svg>
            </div>
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                <div class="flex items-center gap-4">
                    @if($neraca['is_balance'])
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center border border-emerald-200 shadow-sm shrink-0">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div>
                        <p class="text-base font-black text-emerald-800 tracking-wide">NERACA SEIMBANG (BALANCED)</p>
                        <p class="text-sm text-emerald-600/90 font-medium mt-0.5">Total Aktiva dan Pasiva telah sesuai tanpa ada selisih.</p>
                    </div>
                    @else
                    <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center border border-red-200 shadow-sm shrink-0 animate-pulse">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-base font-black text-red-800 tracking-wide">NERACA TIDAK SEIMBANG</p>
                        <p class="text-sm text-red-600/90 font-medium mt-0.5">Terdapat selisih sebesar <strong class="font-bold text-red-700 bg-red-100 px-1 rounded">{{ format_rupiah($neraca['selisih']) }}</strong> — Perlu investigasi lebih lanjut.</p>
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-4 text-sm font-mono bg-white/60 p-3 rounded-xl border {{ $neraca['is_balance'] ? 'border-emerald-200' : 'border-red-200' }} backdrop-blur-sm">
                    <div class="text-right">
                        <p class="text-[10px] text-stone-500 font-sans font-bold uppercase tracking-widest mb-1">AKTIVA</p>
                        <p class="font-black text-stone-800 text-base">{{ format_rupiah($neraca['aktiva_total']) }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-full {{ $neraca['is_balance'] ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center font-black text-lg">=</div>
                    <div class="text-left">
                        <p class="text-[10px] text-stone-500 font-sans font-bold uppercase tracking-widest mb-1">PASIVA</p>
                        <p class="font-black text-stone-800 text-base">{{ format_rupiah($neraca['pasiva_total']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Catatan Kaki --}}
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center border border-amber-100">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h4 class="text-sm font-black text-stone-800 uppercase tracking-widest">Catatan atas Laporan Posisi Keuangan</h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-[13px] text-stone-600 leading-relaxed">
            <div class="space-y-4">
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-stone-100 flex items-center justify-center text-[11px] font-bold text-stone-600 mt-0.5">1</span>
                    <p><strong class="text-stone-800 font-bold">Kas & Bank</strong><br>Saldo kas tunai dan rekening bank koperasi per tanggal laporan. Data kas dihitung secara hybrid menyesuaikan parameter pada tahun buku {{ $neraca['tahun'] }}.</p>
                </div>
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-stone-100 flex items-center justify-center text-[11px] font-bold text-stone-600 mt-0.5">2</span>
                    <p><strong class="text-stone-800 font-bold">Piutang Legacy</strong><br>Piutang dari berbagai periode pengurus yang masih aktif, termasuk rincian per individu dan akumulasi gelondongan.</p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-stone-100 flex items-center justify-center text-[11px] font-bold text-stone-600 mt-0.5">3</span>
                    <p><strong class="text-stone-800 font-bold">Kewajiban Jk. Pendek</strong><br>Meliputi simpanan sukarela, dana alokasi SHU (Pendidikan, Sosial, Pemdaker), dana resiko, dan hutang pajak.</p>
                </div>
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-stone-100 flex items-center justify-center text-[11px] font-bold text-stone-600 mt-0.5">4</span>
                    <p><strong class="text-stone-800 font-bold">Modal Sendiri</strong><br>Terdiri dari simpanan anggota (Pokok, Wajib, SWP), donasi, cadangan, dan SHU tahun berjalan.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Lembar Pengesahan --}}
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-10 relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.02] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-10 text-center text-[14px] text-stone-800">
            <div class="flex flex-col items-center justify-end">
                <p class="font-medium text-stone-500 mb-20">Dibuat oleh,</p>
                <div class="w-48 border-b-2 border-stone-800 pb-1"><p class="font-bold text-stone-800">Bendahara</p></div>
                <p class="text-xs text-stone-500 mt-1 uppercase tracking-wider">Koperasi Tirta Bina Karya</p>
            </div>
            <div class="flex flex-col items-center justify-end">
                <p class="font-medium text-stone-500 mb-20">Diperiksa oleh,</p>
                <div class="w-48 border-b-2 border-stone-800 pb-1"><p class="font-bold text-stone-800">Pengawas</p></div>
                <p class="text-xs text-stone-500 mt-1 uppercase tracking-wider">Koperasi Tirta Bina Karya</p>
            </div>
            <div class="flex flex-col items-center justify-end">
                <p class="text-xs text-stone-500 mb-1 text-right w-full pr-6">Pekanbaru, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-medium text-stone-500 mb-16">Disetujui oleh,</p>
                <div class="w-48 border-b-2 border-stone-800 pb-1"><p class="font-bold text-stone-800">Ketua Koperasi</p></div>
                <p class="text-xs text-stone-500 mt-1 uppercase tracking-wider">Koperasi Tirta Bina Karya</p>
            </div>
        </div>
    </div>

</div>
@endsection

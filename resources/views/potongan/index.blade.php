@extends('layouts.app')

@section('title', 'Potongan TPP')
@section('subtitle', 'Rekapitulasi beban potongan per anggota sesuai parameter bulan dan jenis.')

@section('actions')
    <x-export-dropdown 
        excelRoute="{{ route('potongan.export.excel', request()->all()) }}" 
        pdfRoute="{{ route('potongan.export.pdf', request()->all()) }}" 
    />

    @if($totalKeseluruhan > 0)
    <form action="{{ route('potongan.proses') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membukukan seluruh potongan TPP periode ini? Tindakan ini tidak dapat dibatalkan.')">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-all flex items-center gap-2 shadow-sm shadow-[#043d2e]/20">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Simpan & Bukukan
        </button>
    </form>
    @endif
@endsection

@section('content')
<div x-data="{ 
    activePotongan: null,
    formatRp(val) {
        if (!val) return 'Rp0';
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
    }
}">
<form action="{{ route('potongan.index') }}" method="GET" id="filterForm" class="contents">
    <div class="flex flex-col gap-5">

        {{-- Filter Bar Component wrapped in Form for synchronous submit --}}
        <x-filter-bar searchPlaceholder="Cari NIP atau Nama Anggota..." name="q" value="{{ request('q') }}" onchange="document.getElementById('filterForm').submit()">
            
            <x-slot name="indicator">
                @if(request('bidang') || request('jenis'))
                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full shadow-sm"></span>
                @endif
            </x-slot>

            <x-slot name="filters">
                {{-- Filter Jenis Potongan --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Jenis Potongan</label>
                    <select name="jenis" onchange="document.getElementById('filterForm').submit()" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                        <option value="">Semua Potongan TPP</option>
                        <option value="pokok" {{ request('jenis') == 'pokok' ? 'selected' : '' }}>Potongan Pokok</option>
                        <option value="wajib" {{ request('jenis') == 'wajib' ? 'selected' : '' }}>Potongan Wajib</option>
                        <option value="pinjaman" {{ request('jenis') == 'pinjaman' ? 'selected' : '' }}>Angsuran Pinjaman</option>
                    </select>
                </div>

                {{-- Filter Bidang --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Bidang Dinas</label>
                    <select name="bidang" onchange="document.getElementById('filterForm').submit()" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                        <option value="">Semua Bidang</option>
                        @foreach($bidangs as $b)
                            <option value="{{ $b->id }}" {{ request('bidang') == $b->id ? 'selected' : '' }}>{{ $b->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Bulan dan Tahun --}}
                <div class="flex gap-2">
                    <div class="flex flex-col gap-1.5 flex-1">
                        <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Bulan Tagihan</label>
                        <select name="month" onchange="document.getElementById('filterForm').submit()" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                            @foreach($months as $num => $nm)
                                <option value="{{ $num }}" {{ request('month', now()->month) == $num ? 'selected' : '' }}>{{ $nm }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5 flex-1">
                        <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Tahun</label>
                        <select name="year" onchange="document.getElementById('filterForm').submit()" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ request('year', now()->year) == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-slot>
        </x-filter-bar>

        {{-- Main Table Area --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm flex flex-col">
            <div class="relative flex-1 flex flex-col">
                <x-table>
                    <x-table.thead :sticky="true" class="top-[168px]">
                        <x-table.th>NIP & Nama Anggota</x-table.th>
                        <x-table.th>Bidang</x-table.th>
                        
                        @if(request('jenis') == 'pinjaman')
                            <x-table.th class="text-right">Pokok Pinjaman</x-table.th>
                            <x-table.th class="text-right">Bunga</x-table.th>
                            <x-table.th class="text-right text-amber-800 bg-amber-50/90 backdrop-blur-sm border-l border-stone-200/60">Total Angsuran</x-table.th>
                        @else
                            @if(!request('jenis') || request('jenis') == 'pokok')
                            <x-table.th class="text-right">Pot. Pokok</x-table.th>
                            @endif
                            
                            @if(!request('jenis') || request('jenis') == 'wajib')
                            <x-table.th class="text-right">Pot. Wajib</x-table.th>
                            @endif

                            @if(!request('jenis'))
                            <x-table.th class="text-right">Angsuran Pinjaman</x-table.th>
                            @endif
                            
                            <x-table.th class="text-right text-red-800 bg-red-50/90 backdrop-blur-sm border-l border-stone-200/60">Total Potongan TPP</x-table.th>
                        @endif
                    </x-table.thead>
                    
                    <x-table.tbody>
                        {{-- Row Akumulasi Total --}}
                        @if($dataPotongan->count() > 0)
                        <tr class="bg-stone-100 border-b-2 border-stone-200 shadow-sm divide-x divide-stone-200">
                            <td class="px-5 py-2.5 font-bold text-[11px] uppercase tracking-widest text-stone-800">TOTAL SELURUH ANGGOTA</td>
                            <td class="px-5 py-2.5 text-center"><span class="text-[11px] font-bold text-stone-400">-</span></td>
                            
                            @if(request('jenis') == 'pinjaman')
                                <td class="px-5 py-2.5 text-right"><span class="font-mono text-[13px] font-bold text-stone-800">{{ format_rupiah($dataPotongan->sum('pinjaman_pokok')) }}</span></td>
                                <td class="px-5 py-2.5 text-right"><span class="font-mono text-[13px] font-bold text-stone-800">{{ format_rupiah($dataPotongan->sum('pinjaman_bunga')) }}</span></td>
                                <td class="px-6 py-2.5 text-right bg-amber-100/30">
                                    <span class="font-mono text-[14px] font-black text-amber-700">{{ format_rupiah($totalKeseluruhan) }}</span>
                                </td>
                            @else
                                @if(!request('jenis') || request('jenis') == 'pokok')
                                <td class="px-5 py-2.5 text-right"><span class="font-mono text-[13px] font-bold text-stone-800">{{ format_rupiah($totalPokok) }}</span></td>
                                @endif
                                
                                @if(!request('jenis') || request('jenis') == 'wajib')
                                <td class="px-5 py-2.5 text-right"><span class="font-mono text-[13px] font-bold text-stone-800">{{ format_rupiah($totalWajib) }}</span></td>
                                @endif
                                
                                @if(!request('jenis'))
                                <td class="px-5 py-2.5 text-right"><span class="font-mono text-[13px] font-bold text-stone-800">{{ format_rupiah($totalPinjaman) }}</span></td>
                                @endif
                                
                                <td class="px-6 py-2.5 text-right bg-red-100/30">
                                    <span class="font-mono text-[14px] font-black text-red-600">{{ format_rupiah($totalKeseluruhan) }}</span>
                                </td>
                            @endif
                        </tr>
                        @endif

                        @forelse($paginatedData as $item)
                        <x-table.tr>
                            <x-table.td>
                                <div class="flex flex-col">
                                    <a href="#" @click.prevent="activePotongan = {{ json_encode($item) }}" class="font-bold text-stone-800 hover:text-[#043d2e] transition-colors flex items-center gap-1.5" title="Lihat Detail Potongan">
                                        {{ $item->anggota->nama }}
                                        <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[11px] text-stone-500 font-mono font-medium tracking-wide">NIP. <x-nip-display :value="$item->anggota->nip" /></span>
                                        @if($item->anggota->golongan_asn)
                                            <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-{{ $item->anggota->golongan_asn->color() }}-50 text-{{ $item->anggota->golongan_asn->color() }}-600 border border-{{ $item->anggota->golongan_asn->color() }}-200">{{ $item->anggota->golongan_asn->label() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-stone-600 font-medium text-[12px] uppercase">{{ $item->anggota->bidang ? $item->anggota->bidang->nama_bidang : '-' }}</span>
                            </x-table.td>

                            @if(request('jenis') == 'pinjaman')
                                <x-table.td class="text-right">
                                    <span class="font-mono text-[13px] {{ $item->pinjaman_pokok > 0 ? 'font-medium text-stone-700' : 'text-stone-300' }}">{{ $item->pinjaman_pokok > 0 ? format_rupiah($item->pinjaman_pokok) : '-' }}</span>
                                </x-table.td>
                                <x-table.td class="text-right">
                                    <span class="font-mono text-[13px] {{ $item->pinjaman_bunga > 0 ? 'font-medium text-stone-700' : 'text-stone-300' }}">{{ $item->pinjaman_bunga > 0 ? format_rupiah($item->pinjaman_bunga) : '-' }}</span>
                                </x-table.td>
                                <x-table.td class="text-right bg-amber-50/20 border-l border-stone-200/60">
                                    <div class="flex justify-end">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-stone-200 shadow-sm font-mono text-[13px] font-bold {{ $item->potongan_pinjaman > 0 ? 'text-amber-600' : 'text-stone-400' }}">
                                            {{ format_rupiah($item->potongan_pinjaman) }}
                                        </span>
                                    </div>
                                </x-table.td>
                            @else
                                @if(!request('jenis') || request('jenis') == 'pokok')
                                <x-table.td class="text-right">
                                    <span class="font-mono text-[13px] {{ $item->potongan_pokok > 0 ? 'font-medium text-stone-700' : 'text-stone-300' }}">{{ $item->potongan_pokok > 0 ? format_rupiah($item->potongan_pokok) : '-' }}</span>
                                </x-table.td>
                                @endif

                                @if(!request('jenis') || request('jenis') == 'wajib')
                                <x-table.td class="text-right">
                                    <span class="font-mono text-[13px] {{ $item->potongan_wajib > 0 ? 'font-medium text-stone-700' : 'text-stone-300' }}">{{ $item->potongan_wajib > 0 ? format_rupiah($item->potongan_wajib) : '-' }}</span>
                                </x-table.td>
                                @endif

                                @if(!request('jenis'))
                                <x-table.td class="text-right">
                                    <span class="font-mono text-[13px] {{ $item->potongan_pinjaman > 0 ? 'font-bold text-amber-600' : 'text-stone-300' }}">{{ $item->potongan_pinjaman > 0 ? format_rupiah($item->potongan_pinjaman) : '-' }}</span>
                                </x-table.td>
                                @endif

                                <x-table.td class="text-right bg-red-50/20 border-l border-stone-200/60">
                                    <div class="flex justify-end">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-stone-200 shadow-sm font-mono text-[13px] font-bold {{ $item->total_potongan > 0 ? 'text-red-600' : 'text-stone-400' }}">
                                            {{ format_rupiah($item->total_potongan) }}
                                        </span>
                                    </div>
                                </x-table.td>
                            @endif
                        </x-table.tr>
                        @empty
                        <tr>
                            <td colspan="{{ request('jenis') == 'pinjaman' ? 5 : (!request('jenis') ? 5 : 4) }}" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-2xl bg-stone-50 border border-stone-200 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-stone-800 font-bold mb-1">Data Tidak Ditemukan</h4>
                                    <p class="text-sm text-stone-500">Tidak ada tagihan potongan yang ditemukan untuk filter terkait.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </x-table.tbody>
                </x-table>
            </div>
            @if($paginatedData->hasPages())
                <div class="px-5 py-4 border-t border-stone-200 bg-stone-50 rounded-b-2xl">
                    {{ $paginatedData->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>
</form>

    {{-- Detail Modal --}}
    <template x-teleport="body">
        <div x-show="activePotongan" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0" style="display: none;">
            <div x-show="activePotongan" x-transition.opacity class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm" @click="activePotongan = null"></div>
            
            <div x-show="activePotongan" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white rounded-2xl border border-stone-200 shadow-2xl w-full max-w-lg overflow-hidden z-10 flex flex-col max-h-[90vh]">
                
                <div class="px-6 py-4 bg-stone-50 border-b border-stone-200 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="font-bold text-stone-800 text-[15px]">Perincian Tagihan Anggota</h3>
                        <p class="text-[11.5px] font-medium text-stone-500 mt-0.5" x-text="activePotongan?.anggota?.nama"></p>
                    </div>
                    <button @click="activePotongan = null" type="button" class="w-8 h-8 flex items-center justify-center rounded-xl text-stone-400 hover:text-stone-600 hover:bg-stone-200/50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 flex flex-col gap-6">
                    
                    {{-- Simpanan Pokok --}}
                    <template x-if="activePotongan?.potongan_pokok > 0">
                        <div class="p-4 rounded-xl border border-stone-200 bg-white shadow-sm flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-stone-800">Simpanan Pokok (Belum Dibayar)</p>
                                <p class="text-[11px] text-stone-500 font-medium">Tagihan awal untuk anggota yang belum melunasi iuran pokok.</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="font-mono font-bold text-orange-600" x-text="formatRp(activePotongan.potongan_pokok)"></p>
                            </div>
                        </div>
                    </template>

                    {{-- Simpanan Wajib --}}
                    <template x-if="activePotongan?.potongan_wajib > 0">
                        <div class="p-4 rounded-xl border border-stone-200 bg-white shadow-sm flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-stone-800">Simpanan Wajib Bulanan</p>
                                <p class="text-[11px] text-stone-500 font-medium">Kewajiban reguler potongan per bulan berdasar aturan.</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="font-mono font-bold text-stone-700" x-text="formatRp(activePotongan.potongan_wajib)"></p>
                            </div>
                        </div>
                    </template>

                    {{-- Angsuran Pinjaman Detail --}}
                    <template x-if="activePotongan?.detail_pinjaman && activePotongan.detail_pinjaman.length > 0">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="text-[12px] font-bold text-stone-400 uppercase tracking-widest">Detail Angsuran Pinjaman</h4>
                                <div class="flex-1 h-px bg-stone-200"></div>
                            </div>
                            
                            <template x-for="(pinj, index) in activePotongan.detail_pinjaman" :key="index">
                                <div class="p-4 rounded-xl border border-stone-200 bg-stone-50/50 shadow-sm flex flex-col gap-3">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-bold text-stone-800" x-text="pinj.nama_pinjaman"></p>
                                            <p class="text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 mt-1 inline-block" x-text="'Angsuran Ke-' + pinj.urutan_angsuran + ' dari ' + pinj.lama_angsuran"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-mono text-[14px] font-black text-amber-600" x-text="formatRp(pinj.nominal_total)"></p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-stone-200/80">
                                        <div>
                                            <p class="text-[10px] uppercase font-bold tracking-wider text-stone-400">Pokok Pinjaman</p>
                                            <p class="font-mono text-xs font-semibold text-stone-700 mt-0.5" x-text="formatRp(pinj.nominal_pokok)"></p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase font-bold tracking-wider text-stone-400">Jasa / Bunga</p>
                                            <p class="font-mono text-xs font-semibold text-stone-700 mt-0.5" x-text="formatRp(pinj.nominal_bunga)"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Footer Summary --}}
                <div class="px-6 py-4 bg-stone-50 border-t border-stone-200 flex justify-between items-center shrink-0">
                    <p class="text-xs font-bold text-stone-500 uppercase tracking-widest">Grand Total</p>
                    <p class="font-mono text-xl font-black text-red-600" x-text="formatRp(activePotongan?.total_potongan)"></p>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

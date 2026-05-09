@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('subtitle', 'Konfigurasi parameter operasional Koperasi PUPR Riau')

@section('content')
<div class="space-y-6">

    {{-- Info Banner --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-3 shadow-sm">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-amber-800">
            <p class="font-bold mb-1.5">Kebijakan Efektivitas Perubahan Konfigurasi</p>
            <ul class="list-disc pl-4 space-y-1.5 text-xs text-amber-700/90 font-medium">
                <li><strong>Simpanan Pokok & Wajib:</strong> Nilai baru berlaku mulai proses <em class="font-semibold text-amber-800">Potongan TPP bulan berikutnya</em>. Bulan berjalan tetap menggunakan nilai lama.</li>
                <li><strong>Bunga, Potongan SWP, Dana Resiko, Biaya Admin:</strong> Berlaku untuk <em class="font-semibold text-amber-800">pinjaman baru yang diajukan</em> setelah perubahan. Pinjaman aktif tetap menggunakan konfigurasi saat pinjaman tersebut dibuat.</li>
                <li><strong>Tenor & Batas Bulan Pelunasan:</strong> Berlaku untuk <em class="font-semibold text-amber-800">pengajuan pinjaman baru</em> setelah perubahan.</li>
                <li><strong>Pengaturan Teknis (Login):</strong> Berlaku <em class="font-semibold text-amber-800">langsung/segera</em> setelah disimpan.</li>
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
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        {{-- Header Kategori --}}
        <div class="px-6 py-4 bg-stone-50/50 border-b border-stone-100 flex items-center gap-3">
            @if($kategori === 'Keuangan')
            <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            @else
            <div class="w-8 h-8 rounded-xl bg-violet-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            @endif
            <div>
                <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest leading-none mb-0.5">Kategori Pengaturan</p>
                <h3 class="text-sm font-bold text-stone-700 leading-none">{{ $kategori }}</h3>
            </div>
        </div>

        {{-- Daftar Pengaturan --}}
        <div class="divide-y divide-stone-100">
            @foreach($items as $pengaturan)
            <form action="{{ route('pengaturan.update', $pengaturan->id) }}" method="POST" class="px-6 py-5 hover:bg-stone-50/30 transition-colors">
                @csrf
                @method('PATCH')
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-stone-800">{{ $pengaturan->deskripsi }}</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-stone-100 border border-stone-200 text-[10px] font-mono text-stone-500 font-medium">
                                {{ $pengaturan->key }}
                            </span>
                            @if(isset($hints[$pengaturan->key]))
                            <span class="text-[11px] text-stone-500 font-medium flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $hints[$pengaturan->key] }}
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Input --}}
                    <div class="flex items-center gap-3 shrink-0">
                        @can('pengaturan.edit')
                        <input type="text" name="value" value="{{ $pengaturan->value }}"
                               class="w-40 px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-mono font-medium text-right focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 transition-all shadow-sm">
                        <button type="submit" class="px-5 py-2.5 bg-[#043d2e] hover:bg-[#032e22] text-white rounded-xl text-sm font-bold transition-all shadow-sm whitespace-nowrap">
                            Simpan
                        </button>
                        @else
                        <span class="w-40 px-3 py-2.5 bg-stone-100 border border-stone-200 rounded-xl text-sm font-mono font-medium text-right text-stone-500 cursor-not-allowed">{{ $pengaturan->value }}</span>
                        @endcan
                    </div>
                </div>
            </form>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($grouped->isEmpty())
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-12 text-center flex flex-col items-center">
        <div class="w-16 h-16 bg-stone-50 rounded-2xl border border-stone-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <p class="text-stone-500 font-medium text-sm">Belum ada data pengaturan yang tersedia di database.</p>
    </div>
    @endif

    {{-- Pengaturan Simpanan Wajib Bulanan (Khusus) --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mt-8">
        <div class="px-6 py-4 bg-stone-50/50 border-b border-stone-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-stone-700 leading-none mb-1">Pengaturan Simpanan Wajib Bulanan (Khusus)</h3>
                    <p class="text-[11px] text-stone-500 font-medium">Atur nominal potongan simpanan wajib spesifik untuk bulan tertentu. Mengesampingkan pengaturan global.</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            @can('pengaturan.edit')
            <form action="{{ route('pengaturan.khusus.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3 mb-6 items-end bg-stone-50/50 p-4 rounded-xl border border-stone-200/60">
                @csrf
                <div class="flex-1 w-full">
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Bulan</label>
                    <select name="bulan" class="w-full px-3 py-2.5 bg-white border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700" required>
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Tahun</label>
                    <select name="tahun" class="w-full px-3 py-2.5 bg-white border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700" required>
                        @for($y=date('Y')-1; $y<=date('Y')+5; $y++)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nominal Khusus (Rp)</label>
                    <input type="number" name="value" class="w-full px-3 py-2.5 bg-white border border-stone-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700" required min="0" step="1000">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-[#043d2e] hover:bg-[#032e22] text-white rounded-xl text-sm font-bold transition-all shadow-sm w-full sm:w-auto h-[42px]">
                    Tambahkan
                </button>
            </form>
            @endcan

            <div class="border border-stone-200 rounded-xl overflow-hidden">
                <x-table>
                    <x-table.thead :sticky="false">
                        <x-table.th>Bulan & Tahun</x-table.th>
                        <x-table.th class="text-right">Nominal Simpanan Wajib</x-table.th>
                        @can('pengaturan.edit')
                        <x-table.th class="w-16 text-center">Aksi</x-table.th>
                        @endcan
                    </x-table.thead>
                    <x-table.tbody>
                        @forelse($khusus as $k)
                        <x-table.tr>
                            <x-table.td>
                                <span class="font-bold text-stone-700">{{ date('F', mktime(0,0,0,$k->bulan,1)) }} {{ $k->tahun }}</span>
                            </x-table.td>
                            <x-table.td class="text-right">
                                <span class="font-mono font-bold text-[#043d2e] bg-emerald-50 px-2.5 py-1 rounded-lg">{{ format_rupiah($k->value) }}</span>
                            </x-table.td>
                            @can('pengaturan.edit')
                            <x-table.td class="text-center">
                                <form action="{{ route('pengaturan.khusus.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus pengaturan khusus untuk periode ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </x-table.td>
                            @endcan
                        </x-table.tr>
                        @empty
                        <x-table.tr>
                            <x-table.td colspan="3" class="text-center text-stone-500 py-6 text-sm">Belum ada pengaturan nominal simpanan wajib khusus per bulan.</x-table.td>
                        </x-table.tr>
                        @endforelse
                    </x-table.tbody>
                </x-table>
            </div>
        </div>
    </div>

</div>
@endsection

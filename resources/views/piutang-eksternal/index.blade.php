@extends('layouts.app')

@section('title', 'Piutang Lain-Lain')
@section('subtitle', 'Daftar piutang legacy (pengurus lama) dan pihak ketiga')

@section('actions')
    @can('piutang_eksternal.create')
    <button @click="$dispatch('open-modal', 'modal-tambah-piutang')" class="py-2.5 px-4 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Catat Piutang Baru</span>
    </button>
    @endcan
@endsection

@section('content')
<div class="flex flex-col gap-6">
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <x-stat-card 
            title="Total Piutang Aktif" 
            :value="format_rupiah($totalAktif)" 
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            color="amber"
            :subtitle="'Dari ' . $countAktif . ' catatan piutang aktif'"
        />
        <x-stat-card 
            title="Total Piutang Lunas" 
            :value="format_rupiah($totalLunas)" 
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            color="emerald"
            subtitle="Seluruh piutang yang sudah diselesaikan"
        />
        <x-stat-card 
            title="Piutang Terbayar" 
            :value="format_rupiah($piutangs->sum('nominal_terbayar'))" 
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'
            color="blue"
            subtitle="Akumulasi pembayaran yang sudah masuk"
        />
    </div>

    {{-- Filter Bar --}}
    <x-filter-bar searchPlaceholder="Cari nama peminjam, jabatan, atau no. referensi..." :searchValue="request('q')" name="q">
        <x-slot name="filters">
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Status</label>
                <select name="status" @change="$el.form.submit()" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Kategori</label>
                <select name="kategori" @change="$el.form.submit()" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriOptions as $kat)
                        <option value="{{ $kat->value }}" {{ request('kategori') == $kat->value ? 'selected' : '' }}>{{ $kat->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Tahun Pinjam</label>
                <select name="tahun" @change="$el.form.submit()" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none cursor-pointer">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunOptions as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            
            @if(request()->anyFilled(['status', 'kategori', 'tahun', 'q']))
                <a href="{{ route('piutang-eksternal.index') }}" class="mt-2 w-full py-2.5 px-4 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-sm font-bold transition-colors text-center">
                    Reset Semua Filter
                </a>
            @endif
        </x-slot>
    </x-filter-bar>

    {{-- Main Table Area --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden text-stone-800">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-stone-50/50 border-b border-stone-200/60 transition-colors">
                    <th class="px-6 py-4 text-[13px] font-bold text-stone-600 uppercase tracking-wider">Ref / Peminjam</th>
                    <th class="px-6 py-4 text-[13px] font-bold text-stone-600 uppercase tracking-wider">Tahun / Kategori</th>
                    <th class="px-6 py-4 text-[13px] font-bold text-stone-600 uppercase tracking-wider">Nominal Awal</th>
                    <th class="px-6 py-4 text-[13px] font-bold text-stone-600 uppercase tracking-wider">Sisa Piutang</th>
                    <th class="px-6 py-4 text-[13px] font-bold text-stone-600 uppercase tracking-wider">Status / Progress</th>
                    <th class="px-6 py-4 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($piutangs as $p)
                    <tr class="hover:bg-stone-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[11px] font-black font-mono text-stone-400 group-hover:text-[#043d2e] transition-colors tracking-tight">{{ $p->no_referensi }}</span>
                                <span class="text-[14.5px] font-bold text-stone-800">{{ $p->nama_peminjam }}</span>
                                <span class="text-[12px] text-stone-500 font-medium">{{ $p->jabatan_peminjam ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1.5">
                                <span class="px-2.5 py-0.5 rounded-full bg-stone-100 text-stone-700 text-[11px] font-black w-fit border border-stone-200 leading-normal">{{ $p->tahun_pinjam }}</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-{{ $p->kategori_peminjam->color() }}-50 text-{{ $p->kategori_peminjam->color() }}-700 text-[11px] font-black w-fit border border-{{ $p->kategori_peminjam->color() }}-200 shadow-sm leading-normal">
                                    {{ strtoupper($p->kategori_peminjam->label()) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-[14px] font-mono font-bold text-stone-700">{{ format_rupiah($p->nominal_awal) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-[14.5px] font-mono font-black text-stone-900">{{ format_rupiah($p->sisa_piutang) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-2 min-w-[120px]">
                                @if($p->status === 'lunas')
                                    <div class="flex items-center gap-1.5 text-emerald-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-[12px] font-black uppercase">Lunas</span>
                                    </div>
                                @else
                                    <div class="w-full h-1.5 bg-stone-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ $p->progressPersen() }}%"></div>
                                    </div>
                                    <span class="text-[11px] font-bold text-stone-500 italic uppercase">Terbayar {{ $p->progressPersen() }}%</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('piutang-eksternal.show', $p->id) }}" class="p-2.5 rounded-xl bg-stone-100 text-stone-500 hover:bg-stone-200 hover:text-stone-800 transition-all group/btn">
                                    <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @can('piutang_eksternal.create')
                                <button @click="$dispatch('open-modal', 'modal-edit-piutang-{{ $p->id }}')" class="p-2.5 rounded-xl bg-stone-100 text-stone-500 hover:bg-stone-200 hover:text-stone-800 transition-all group/btn">
                                    <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3 opacity-30">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                                <span class="text-[16px] font-bold tracking-tight">Tidak ada data piutang ditemukan</span>
                            </div>
                        </td>
                    @endforelse
            </tbody>
        </table>

        @if($piutangs->hasPages())
        <div class="px-6 py-4 bg-stone-50 border-t border-stone-100">
            {{ $piutangs->links() }}
        </div>
        @endif
    </div>

    {{-- Breakdown Per Tahun Card --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6">
        <h3 class="text-sm font-black text-stone-400 uppercase tracking-widest mb-4">Ringkasan Sisa Piutang Per Tahun</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($sisaPerTahun as $item)
                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <p class="text-[10px] font-black text-stone-400 uppercase tracking-wider mb-1">Piutang Tahun {{ $item->tahun_pinjam }}</p>
                    <p class="text-[16px] font-black text-stone-900 font-mono">{{ format_rupiah($item->total_sisa) }}</p>
                    <p class="text-[11px] text-stone-500 font-medium mt-0.5">{{ $item->jumlah }} Catatan Piutang</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Modal Tambah Piutang --}}
<x-modal name="modal-tambah-piutang" title="Catat Piutang Lain-Lain Baru" maxWidth="xl">
    <form action="{{ route('piutang-eksternal.store') }}" method="POST" class="p-6">
        @csrf
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <x-label for="nama_peminjam" value="Nama Peminjam" required />
                    <x-input id="nama_peminjam" name="nama_peminjam" placeholder="Contoh: Pak Budi" required />
                </div>
                <div class="flex flex-col gap-1.5">
                    <x-label for="jabatan_peminjam" value="Jabatan (Opsional)" />
                    <x-input id="jabatan_peminjam" name="jabatan_peminjam" placeholder="Contoh: Mantan Ketua 2024" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <x-label for="kategori_peminjam" value="Kategori Peminjam" required />
                    <select id="kategori_peminjam" name="kategori_peminjam" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e]" required>
                        @foreach($kategoriOptions as $kat)
                            <option value="{{ $kat->value }}">{{ $kat->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <x-label for="tahun_pinjam" value="Tahun Pinjam" required />
                    <select id="tahun_pinjam" name="tahun_pinjam" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e]" required>
                        @for($i = now()->year; $i >= 2015; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <x-label for="nominal_awal" value="Total Nominal Piutang" required />
                <x-currency-input id="nominal_awal" name="nominal_awal" placeholder="0" required />
                <p class="text-[11px] text-amber-600 font-medium">Input nominal total piutang yang tercatat (sebelum pengembalian).</p>
            </div>

            <div class="flex flex-col gap-1.5">
                <x-label for="keterangan" value="Keterangan / Alasan Piutang" />
                <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e]" placeholder="Penjelasan singkat mengenai sumber piutang ini..."></textarea>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end gap-3">
            <button type="button" @click="show = false" class="py-2.5 px-4 rounded-xl text-sm font-bold text-stone-500 hover:bg-stone-100 transition-colors">Batal</button>
            <button type="submit" class="py-2.5 px-6 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold shadow-sm transition-all">Simpan Catatan</button>
        </div>
    </form>
</x-modal>

{{-- Modal Edit Piutang --}}
@foreach($piutangs as $p)
@can('piutang_eksternal.create')
<x-modal name="modal-edit-piutang-{{ $p->id }}" title="Edit Data Piutang" maxWidth="xl">
    <form action="{{ route('piutang-eksternal.update', $p->id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <x-label for="nama_peminjam_{{ $p->id }}" value="Nama Peminjam" required />
                    <x-input id="nama_peminjam_{{ $p->id }}" name="nama_peminjam" value="{{ $p->nama_peminjam }}" required />
                </div>
                <div class="flex flex-col gap-1.5">
                    <x-label for="jabatan_peminjam_{{ $p->id }}" value="Jabatan (Opsional)" />
                    <x-input id="jabatan_peminjam_{{ $p->id }}" name="jabatan_peminjam" value="{{ $p->jabatan_peminjam }}" />
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <x-label for="kategori_peminjam_{{ $p->id }}" value="Kategori Peminjam" required />
                <select id="kategori_peminjam_{{ $p->id }}" name="kategori_peminjam" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e]" required>
                    @foreach($kategoriOptions as $kat)
                        <option value="{{ $kat->value }}" {{ $p->kategori_peminjam == $kat ? 'selected' : '' }}>{{ $kat->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1.5 opacity-60">
                <x-label value="Informasi Keuangan (Tidak dapat diubah)" />
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-stone-100 rounded-xl border border-stone-200">
                        <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Awal: {{ format_rupiah($p->nominal_awal) }}</p>
                    </div>
                    <div class="p-3 bg-stone-100 rounded-xl border border-stone-200">
                        <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Sisa: {{ format_rupiah($p->sisa_piutang) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <x-label for="keterangan_{{ $p->id }}" value="Keterangan" />
                <textarea id="keterangan_{{ $p->id }}" name="keterangan" rows="3" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e]">{{ $p->keterangan }}</textarea>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end gap-3">
            <button type="button" @click="show = false" class="py-2.5 px-4 rounded-xl text-sm font-bold text-stone-500 hover:bg-stone-100 transition-colors">Batal</button>
            <button type="submit" class="py-2.5 px-6 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold shadow-sm transition-all">Simpan Perubahan</button>
        </div>
    </form>
</x-modal>
@endcan
@endforeach

@endsection

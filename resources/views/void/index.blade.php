@extends('layouts.app')

@section('title', 'Jurnal Umum & Pembatalan')
@section('subtitle', 'Jurnal umum koperasi (buku besar) dan otorisasi pembatalan transaksi')

@section('actions')
    @can('void.request')
    <a href="#" onclick="document.getElementById('void-help-info').classList.toggle('hidden'); return false;" class="px-4 py-2.5 bg-white border border-stone-200 text-stone-700 rounded-xl text-sm font-bold hover:bg-stone-50 transition-colors shadow-sm flex items-center justify-center gap-2">
        <svg class="w-4 h-4 text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Panduan Pembatalan</span>
    </a>
    @endcan
@endsection

@section('content')
<div class="space-y-5">

    {{-- Info Banner --}}
    <div id="void-help-info" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-2xl">
        <div class="flex gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-sm text-blue-800">
                <p class="font-bold mb-1">Cara Membatalkan Transaksi</p>
                <ol class="list-decimal list-inside space-y-0.5 text-blue-700 text-[13px]">
                    <li>Cari transaksi yang salah di tab <strong>Jurnal Umum</strong></li>
                    <li>Klik tombol <strong>"Batalkan"</strong> pada baris transaksi</li>
                    <li>Isi alasan pembatalan (wajib) dan kirim pengajuan</li>
                    <li>Pimpinan akan memvalidasi (menyetujui/menolak) di tab <strong>Otorisasi Pembatalan</strong></li>
                    <li>Jika disetujui, sistem akan membuat <strong>jurnal pembalik</strong> otomatis</li>
                </ol>
                <p class="mt-2 text-[12px] text-blue-600 italic">⚡ Data asli tidak pernah dihapus — hanya ditambahkan entry pembalik (jurnal koreksi).</p>
            </div>
        </div>
    </div>

    {{-- Tab Content: Jurnal Ledger --}}
    @if($tab === 'ledger')
    <div class="space-y-4">
        {{-- Filters & Sticky Navigation --}}
        <form class="contents" x-data="{
                submitDelay: null,
                autoSubmit() {
                    clearTimeout(this.submitDelay);
                    this.submitDelay = setTimeout(() => { this.$el.submit() }, 400);
                }
            }" 
            method="GET" action="{{ route('void.index') }}" 
            @change="autoSubmit()"
            @input.debounce.500ms="if($event.target.type === 'text') autoSubmit()"
        >
                <input type="hidden" name="tab" value="ledger">
                <x-filter-bar 
                    name="q" 
                    value="{{ request('q') }}" 
                    searchPlaceholder="Cari referensi, deskripsi, NIP, atau nama..."
                >
                    <x-slot name="header">
                        <div class="flex gap-1 p-1 bg-stone-100 rounded-2xl">
                            <a href="{{ route('void.index', ['tab' => 'ledger']) }}" 
                               class="flex-1 px-4 py-3 text-center text-sm font-bold rounded-xl transition-all bg-white text-stone-900 shadow-sm">
                                <svg class="w-4 h-4 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Jurnal Umum
                            </a>
                            <a href="{{ route('void.index', ['tab' => 'void']) }}" 
                               class="flex-1 px-4 py-3 text-center text-sm font-bold rounded-xl transition-all relative text-stone-500 hover:text-stone-700 hover:bg-white/50">
                                <svg class="w-4 h-4 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Otorisasi Pembatalan
                                @if($pendingCount > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-sm">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </div>
                    </x-slot>
                    <x-slot name="filters">
                        <div>
                            <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Tipe Jurnal</label>
                            <select name="tipe" class="w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e]">
                                <option value="">Semua Tipe</option>
                                <option value="kredit" {{ request('tipe') === 'kredit' ? 'selected' : '' }}>Kredit (Masuk)</option>
                                <option value="debit" {{ request('tipe') === 'debit' ? 'selected' : '' }}>Debit (Keluar)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Kategori</label>
                            <select name="kategori" class="w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e]">
                                <option value="">Semua Kategori</option>
                                @foreach(\App\Enums\KategoriLedger::cases() as $kat)
                                <option value="{{ $kat->value }}" {{ request('kategori') === $kat->value ? 'selected' : '' }}>{{ $kat->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                            <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                            <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e]">
                        </div>
                        
                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-[#043d2e] text-white rounded-xl text-sm font-bold shadow-sm hover:bg-[#043d2e]/90">Terapkan</button>
                            @if(request()->hasAny(['tipe', 'kategori', 'dari_tanggal', 'sampai_tanggal']))
                            <a href="{{ route('void.index', ['tab' => 'ledger', 'q' => request('q')]) }}" class="flex-1 px-4 py-2 bg-stone-100 text-stone-600 rounded-xl text-sm font-bold text-center hover:bg-stone-200">Reset</a>
                            @endif
                        </div>
                    </x-slot>

                    @if(request()->hasAny(['tipe', 'kategori', 'dari_tanggal', 'sampai_tanggal']))
                        <x-slot name="indicator">
                            <span class="absolute top-1.5 right-1.5 lg:top-2.5 lg:right-2.5 w-2 h-2 rounded-full bg-red-500"></span>
                        </x-slot>
                    @endif
                </x-filter-bar>
        </form>

        {{-- Ledger Table --}}
        <div class="relative">
            <x-table>
                <x-table.thead :sticky="true" class="lg:top-[232px]">
                    <tr>
                    <x-table.th>Tanggal</x-table.th>
                    <x-table.th>No. Referensi</x-table.th>
                    <x-table.th>Tipe</x-table.th>
                    <x-table.th>Kategori</x-table.th>
                    <x-table.th>Deskripsi</x-table.th>
                    <x-table.th>Anggota</x-table.th>
                    <x-table.th class="text-right">Nominal</x-table.th>
                    <x-table.th class="text-right">Aksi</x-table.th>
                </tr>
            </x-table.thead>
            <x-table.tbody>
                @forelse($ledgerEntries as $entry)
                <x-table.tr class="{{ $entry->kategori === \App\Enums\KategoriLedger::Void ? 'bg-red-50/50' : '' }} hover:bg-stone-50/50 transition-colors cursor-pointer group" x-data @click="$dispatch('open-modal', 'detail-ledger-{{ $entry->id }}')">
                    <x-table.td class="align-top whitespace-nowrap">
                        <span class="text-[13px] font-bold text-stone-800">{{ $entry->tanggal_efektif->translatedFormat('d M Y') }}</span>
                        <span class="block text-[11px] text-stone-400">{{ $entry->created_at?->diffForHumans() }}</span>
                    </x-table.td>
                    <x-table.td class="align-top">
                        <span class="font-mono text-[12px] font-bold text-stone-600">{{ $entry->no_referensi }}</span>
                    </x-table.td>
                    <x-table.td class="align-top">
                        @if($entry->tipe === \App\Enums\TipeLedger::Kredit)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            Kredit
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-red-50 text-red-700 border border-red-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            Debit
                        </span>
                        @endif
                    </x-table.td>
                    <x-table.td class="align-top">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-{{ $entry->kategori->color() }}-50 text-{{ $entry->kategori->color() }}-700 border border-{{ $entry->kategori->color() }}-200">
                            {{ $entry->kategori->label() }}
                        </span>
                        @if($entry->void_of_id)
                        <span class="block text-[10px] text-red-500 mt-0.5 font-medium">↩ Reversal dari #{{ $entry->void_of_id }}</span>
                        @endif
                    </x-table.td>
                    <x-table.td class="align-top text-[13px] text-stone-600 font-medium max-w-xs truncate group-hover:text-[#043d2e] transition-colors" title="{{ $entry->deskripsi }}">
                        {{ \Illuminate\Support\Str::limit($entry->deskripsi, 60) }}
                    </x-table.td>
                    <x-table.td class="align-top text-[13px] text-stone-600">
                        {{ $entry->anggota?->nama ?? '-' }}
                    </x-table.td>
                    <x-table.td class="align-top text-right font-mono font-bold whitespace-nowrap text-[13px] {{ $entry->tipe === \App\Enums\TipeLedger::Kredit ? 'text-emerald-700' : 'text-red-600' }}">
                        {{ $entry->tipe === \App\Enums\TipeLedger::Kredit ? '+' : '-' }} {{ format_rupiah($entry->nominal) }}
                    </x-table.td>
                    <x-table.td class="align-top text-right whitespace-nowrap">
                        @if($entry->kategori !== \App\Enums\KategoriLedger::Void && !$entry->is_voided && !$entry->has_pending_void)
                            @can('void.request')
                            @if(in_array($entry->kategori, [\App\Enums\KategoriLedger::Simpanan, \App\Enums\KategoriLedger::Angsuran, \App\Enums\KategoriLedger::Pengeluaran]))
                            <button @click.stop="$dispatch('open-modal', 'request-void-{{ $entry->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-[12px] font-bold border border-red-200 hover:bg-red-100 transition-colors relative z-10">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                Batalkan
                            </button>
                            <div @click.stop>
                            <x-modal name="request-void-{{ $entry->id }}" title="Formulir Pengajuan Pembatalan" subtitle="Ref: {{ $entry->no_referensi }}" maxWidth="md">
                                <form action="{{ route('void.store') }}" method="POST" class="space-y-5 text-left">
                                    @csrf
                                    <input type="hidden" name="ledger_id" value="{{ $entry->id }}">
                                    <input type="hidden" name="ledger_id" value="{{ $entry->id }}">

                                    <div class="bg-stone-50 border border-stone-200 rounded-xl p-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-[12px] font-bold text-stone-500 uppercase">Nominal Transaksi</span>
                                            <span class="text-lg font-black font-mono {{ $entry->tipe === \App\Enums\TipeLedger::Kredit ? 'text-emerald-700' : 'text-red-700' }}">
                                                {{ format_rupiah($entry->nominal) }}
                                            </span>
                                        </div>
                                        <p class="text-[13px] text-stone-600 mt-2 break-words leading-relaxed">{{ $entry->deskripsi }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">
                                            Alasan Pembatalan <span class="text-red-500">*</span>
                                        </label>
                                        <textarea 
                                            name="alasan" 
                                            rows="4" 
                                            required 
                                            minlength="10"
                                            class="w-full px-4 py-3 bg-white border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-400 text-stone-700 outline-none transition-all shadow-sm resize-none"
                                            placeholder="Jelaskan mengapa transaksi ini perlu dibatalkan..."
                                        >{{ old('alasan') }}</textarea>
                                        <p class="text-[11px] text-stone-400 mt-1">Minimal 10 karakter.</p>
                                    </div>

                                    <div class="flex items-center justify-end gap-3 pt-2">
                                        <button type="button" @click="$dispatch('close-modal', 'request-void-{{ $entry->id }}')" class="px-4 py-2.5 bg-white border border-stone-200 text-stone-700 rounded-xl text-sm font-bold hover:bg-stone-50 transition-colors">
                                            Batal
                                        </button>
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengajukan pembatalan?')" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95 flex items-center gap-2">
                                            Ajukan Pembatalan
                                        </button>
                                    </div>
                                </form>
                            </x-modal>
                            </div>
                            @endif
                            @endcan
                        @elseif($entry->is_voided)
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-stone-100 text-stone-500 rounded-lg text-[11px] font-bold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Dibatalkan
                            </span>
                        @elseif($entry->has_pending_void)
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-50 text-amber-600 rounded-lg text-[11px] font-bold border border-amber-200">
                                <svg class="w-3 h-3 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Menunggu
                            </span>
                        @endif

                        {{-- Modals Wrapper (Prevents bubbling up to the tr) --}}
                        <div @click.stop>
                        {{-- Modal Rincian Jurnal --}}
                        <x-modal name="detail-ledger-{{ $entry->id }}" title="Rincian Transaksi Jurnal" subtitle="No Ref: {{ $entry->no_referensi }}" maxWidth="md">
                            <div class="space-y-5 text-left">
                                <div class="bg-stone-50 border border-stone-200 rounded-xl p-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-[12px] font-bold text-stone-500 uppercase">Nominal Transaksi</span>
                                        <span class="text-xl font-black font-mono {{ $entry->tipe === \App\Enums\TipeLedger::Kredit ? 'text-emerald-700' : 'text-red-700' }}">
                                            {{ $entry->tipe === \App\Enums\TipeLedger::Kredit ? '+' : '-' }} {{ format_rupiah($entry->nominal) }}
                                        </span>
                                    </div>
                                    <div class="space-y-2.5">
                                        <div class="flex justify-between items-center pb-2 border-b border-stone-200/60">
                                            <span class="text-[12px] text-stone-500">Tanggal</span>
                                            <span class="text-[13px] font-bold text-stone-700">{{ $entry->tanggal_efektif->translatedFormat('d F Y') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-stone-200/60">
                                            <span class="text-[12px] text-stone-500">Kategori</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-{{ $entry->kategori->color() }}-50 text-{{ $entry->kategori->color() }}-700 border border-{{ $entry->kategori->color() }}-200">
                                                {{ $entry->kategori->label() }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-stone-200/60">
                                            <span class="text-[12px] text-stone-500">Tipe</span>
                                            <span class="text-[12px] font-bold {{ $entry->tipe === \App\Enums\TipeLedger::Kredit ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $entry->tipe->label() }}
                                            </span>
                                        </div>
                                        @if($entry->anggota)
                                        <div class="flex justify-between items-center">
                                            <span class="text-[12px] text-stone-500">Anggota Terkait</span>
                                            <span class="text-[13px] font-bold text-stone-700">{{ $entry->anggota->nama }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div>
                                    <span class="text-[12px] font-bold text-stone-500 uppercase block mb-0.5">Deskripsi Transaksi</span>
                                    <p class="text-[14px] text-stone-800 leading-relaxed break-words whitespace-pre-wrap">{{ $entry->deskripsi }}</p>
                                </div>

                                @if($entry->is_voided)
                                <div class="p-3 bg-stone-100 border border-stone-200 rounded-xl flex items-center gap-2">
                                    <svg class="w-5 h-5 text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <p class="text-[13px] font-bold text-stone-600">Status: Dibatalkan (Voided)</p>
                                </div>
                                @elseif($entry->has_pending_void)
                                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-500 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-[13px] font-bold text-amber-700">Status: Menunggu Otorisasi Pembatalan</p>
                                </div>
                                @endif

                                <div class="flex justify-end pt-2">
                                    <button type="button" @click="$dispatch('close-modal', 'detail-ledger-{{ $entry->id }}')" class="px-6 py-2.5 bg-stone-100 text-stone-700 rounded-xl text-sm font-bold hover:bg-stone-200 transition-colors">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </x-modal>
                        </div>
                    </x-table.td>
                </x-table.tr>
                @empty
                <x-table.tr>
                    <x-table.td colspan="8" class="px-6 py-12 text-center text-stone-500 font-medium text-sm">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Belum ada jurnal umum. Entry akan muncul saat transaksi keuangan dilakukan.
                        </div>
                    </x-table.td>
                </x-table.tr>
                @endforelse
            </x-table.tbody>
        </x-table>
        </div>

        @if($ledgerEntries->hasPages())
        <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
            {{ $ledgerEntries->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- Tab Content: Void Requests --}}
    @if($tab === 'void')
    <div class="space-y-4">
        {{-- Filter & Sticky Navigation --}}
        <form class="contents" x-data="{
                submitDelay: null,
                autoSubmit() {
                    clearTimeout(this.submitDelay);
                    this.submitDelay = setTimeout(() => { this.$el.submit() }, 400);
                }
            }" 
            method="GET" action="{{ route('void.index') }}" 
            @change="autoSubmit()"
            @input.debounce.500ms="if($event.target.type === 'text') autoSubmit()"
        >
                <input type="hidden" name="tab" value="void">
                <x-filter-bar 
                    name="q" 
                    value="{{ request('q') }}" 
                    searchPlaceholder="Cari alasan, referensi, NIP, atau nama..."
                >
                    <x-slot name="header">
                        <div class="flex gap-1 p-1 bg-stone-100 rounded-2xl">
                            <a href="{{ route('void.index', ['tab' => 'ledger']) }}" 
                               class="flex-1 px-4 py-3 text-center text-sm font-bold rounded-xl transition-all text-stone-500 hover:text-stone-700 hover:bg-white/50">
                                <svg class="w-4 h-4 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Jurnal Umum
                            </a>
                            <a href="{{ route('void.index', ['tab' => 'void']) }}" 
                               class="flex-1 px-4 py-3 text-center text-sm font-bold rounded-xl transition-all relative bg-white text-stone-900 shadow-sm">
                                <svg class="w-4 h-4 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Otorisasi Pembatalan
                                @if($pendingCount > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-sm">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </div>
                    </x-slot>
                    <x-slot name="filters">
                        <div>
                            <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Status Persetujuan</label>
                            <select name="void_status" class="w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e]">
                                <option value="">Semua Status</option>
                                @foreach(\App\Enums\StatusVoid::cases() as $sv)
                                <option value="{{ $sv->value }}" {{ request('void_status') === $sv->value ? 'selected' : '' }}>{{ $sv->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-[#043d2e] text-white rounded-xl text-sm font-bold shadow-sm hover:bg-[#043d2e]/90">Terapkan</button>
                            @if(request()->filled('void_status'))
                            <a href="{{ route('void.index', ['tab' => 'void', 'q' => request('q')]) }}" class="flex-1 px-4 py-2 bg-stone-100 text-stone-600 rounded-xl text-sm font-bold text-center hover:bg-stone-200">Reset</a>
                            @endif
                        </div>
                    </x-slot>
                    
                    @if(request()->filled('void_status'))
                        <x-slot name="indicator">
                            <span class="absolute top-1.5 right-1.5 lg:top-2.5 lg:right-2.5 w-2 h-2 rounded-full bg-red-500"></span>
                        </x-slot>
                    @endif
                </x-filter-bar>
        </form>

        {{-- Void Request Cards --}}
        <div class="space-y-3">
            @forelse($voidRequests as $vr)
            <div class="bg-white border border-stone-200 rounded-2xl p-5 hover:shadow-md transition-shadow {{ $vr->status === \App\Enums\StatusVoid::Menunggu ? 'border-l-4 border-l-amber-400' : '' }}">
                <div class="flex flex-col md:flex-row md:items-start gap-4">
                    {{-- Left: Info --}}
                    <div class="flex-1 space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-{{ $vr->status->color() }}-50 text-{{ $vr->status->color() }}-700 border border-{{ $vr->status->color() }}-200">
                                {{ $vr->status->label() }}
                            </span>
                            <span class="font-mono text-[12px] text-stone-500">{{ $vr->ledger->no_referensi ?? '-' }}</span>
                            <span class="text-[12px] text-stone-400">•</span>
                            <span class="text-[12px] text-stone-500">{{ $vr->tanggal_permintaan->translatedFormat('d M Y H:i') }}</span>
                        </div>
                        <p class="text-[14px] font-bold text-stone-800 break-words whitespace-pre-wrap">{{ $vr->ledger->deskripsi ?? '-' }}</p>
                        <div class="flex items-center gap-4 text-[13px] text-stone-600">
                            <span>Nominal: <strong class="text-stone-800">{{ format_rupiah($vr->ledger->nominal ?? 0) }}</strong></span>
                            <span>Anggota: <strong>{{ $vr->ledger->anggota->nama ?? '-' }}</strong></span>
                        </div>
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl mt-2">
                            <p class="text-[12px] font-bold text-amber-700 mb-0.5">Alasan Pembatalan:</p>
                            <p class="text-[13px] text-amber-800 break-words whitespace-pre-wrap">{{ $vr->alasan }}</p>
                        </div>
                        <p class="text-[12px] text-stone-500">Diajukan oleh: <strong>{{ $vr->pemohon->nama ?? '-' }}</strong></p>

                        @if($vr->status !== \App\Enums\StatusVoid::Menunggu && $vr->catatan_keputusan)
                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl">
                            <p class="text-[12px] font-bold text-stone-600 mb-0.5">Catatan Keputusan ({{ $vr->pemutus->nama ?? '-' }}):</p>
                            <p class="text-[13px] text-stone-700 break-words whitespace-pre-wrap">{{ $vr->catatan_keputusan }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Right: Actions --}}
                    @if($vr->status === \App\Enums\StatusVoid::Menunggu)
                    @can('void.approve')
                    <div class="flex md:flex-col gap-2 shrink-0">
                        <button x-data @click="$dispatch('open-modal', 'review-void-{{ $vr->id }}')" class="px-4 py-2.5 bg-blue-50 text-blue-700 rounded-xl text-[13px] font-bold border border-blue-200 hover:bg-blue-100 transition-colors text-center">
                            Validasi
                        </button>
                        
                        <x-modal name="review-void-{{ $vr->id }}" title="Validasi Pengajuan Pembatalan" subtitle="Ref: {{ $vr->ledger->no_referensi }}" maxWidth="md">
                            <div x-data="{ action: null }" class="space-y-5 text-left">
                                <div class="bg-stone-50 border border-stone-200 rounded-xl p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-[12px] font-bold text-stone-500 uppercase">Nominal Transaksi</span>
                                        <span class="text-lg font-black font-mono {{ $vr->ledger->tipe === \App\Enums\TipeLedger::Kredit ? 'text-emerald-700' : 'text-red-700' }}">
                                            {{ format_rupiah($vr->ledger->nominal ?? 0) }}
                                        </span>
                                    </div>
                                    <p class="text-[13px] text-stone-600 leading-relaxed break-words whitespace-pre-wrap"><span class="font-bold">Alasan Pembatalan:</span> {{ $vr->alasan }}</p>
                                </div>

                                <div class="flex gap-3">
                                    <button type="button" @click="action = 'approve'" :class="action === 'approve' ? 'bg-emerald-600 text-white border-emerald-600 ring-2 ring-emerald-200' : 'bg-white text-emerald-700 border-emerald-300 hover:bg-emerald-50'" class="flex-1 px-4 py-3 rounded-xl text-sm font-bold border transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Setujui Pembatalan
                                    </button>
                                    <button type="button" @click="action = 'reject'" :class="action === 'reject' ? 'bg-red-600 text-white border-red-600 ring-2 ring-red-200' : 'bg-white text-red-700 border-red-300 hover:bg-red-50'" class="flex-1 px-4 py-3 rounded-xl text-sm font-bold border transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tolak Pembatalan
                                    </button>
                                </div>

                                {{-- Approve Form --}}
                                <div x-show="action === 'approve'" x-transition class="space-y-4">
                                    <form action="{{ route('void.approve', $vr->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Catatan (Opsional)</label>
                                            <textarea name="catatan" rows="2" class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400 text-stone-700 outline-none transition-all shadow-sm resize-none" placeholder="Catatan persetujuan..."></textarea>
                                        </div>
                                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-[12px] text-amber-800 mt-3 leading-relaxed">
                                            <strong>⚠️ Perhatian:</strong> Setelah disetujui, sistem akan membuat entry reversal di jurnal. Tindakan ini tidak dapat dibatalkan.
                                        </div>
                                        <div class="flex items-center justify-end gap-3 mt-4">
                                            <button type="button" @click="$dispatch('close-modal', 'review-void-{{ $vr->id }}')" class="px-4 py-2 bg-white border border-stone-200 text-stone-700 rounded-xl text-sm font-bold hover:bg-stone-50">Batal</button>
                                            <button type="submit" onclick="return confirm('Yakin menyetujui pembatalan ini?')" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-sm">Konfirmasi Setujui</button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Reject Form --}}
                                <div x-show="action === 'reject'" x-transition class="space-y-4">
                                    <form action="{{ route('void.reject', $vr->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                                            <textarea name="catatan_penolakan" rows="3" required minlength="5" class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-400 text-stone-700 outline-none transition-all shadow-sm resize-none" placeholder="Jelaskan alasan penolakan..."></textarea>
                                        </div>
                                        <div class="flex items-center justify-end gap-3 mt-4">
                                            <button type="button" @click="$dispatch('close-modal', 'review-void-{{ $vr->id }}')" class="px-4 py-2 bg-white border border-stone-200 text-stone-700 rounded-xl text-sm font-bold hover:bg-stone-50">Batal</button>
                                            <button type="submit" onclick="return confirm('Yakin menolak pengajuan pembatalan ini?')" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-sm">Konfirmasi Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </x-modal>
                    </div>
                    @endcan
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-white border border-stone-200 rounded-2xl p-12 text-center">
                <svg class="w-10 h-10 text-stone-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <p class="text-stone-500 font-medium text-sm">Tidak ada pengajuan pembatalan.</p>
            </div>
            @endforelse
        </div>

        @if($voidRequests->hasPages())
        <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
            {{ $voidRequests->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'Detail Piutang')
@section('subtitle', 'Rincian piutang dan riwayat pengembalian kas')

@section('actions')
    <div class="flex items-center gap-3">
        <x-back-button href="{{ route('piutang-eksternal.index') }}" />
        @if($piutang->status === 'aktif')
            @can('piutang_eksternal.bayar')
            <button @click="$dispatch('open-modal', 'modal-catat-pembayaran')" class="py-2.5 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Pembayaran</span>
            </button>
            @endcan
        @endif
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-col">
    {{-- Main Info Column --}}
    <div class="lg:col-span-1 space-y-6 flex flex-col">
        {{-- Profile Card --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden flex flex-col">
            <div class="h-2 bg-{{ $piutang->kategori_peminjam->color() }}-500"></div>
            <div class="p-6 flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-2xl bg-{{ $piutang->kategori_peminjam->color() }}-50 text-{{ $piutang->kategori_peminjam->color() }}-600 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="text-[18px] font-black text-stone-900 leading-tight">{{ $piutang->nama_peminjam }}</h2>
                <p class="text-sm font-bold text-stone-500 mt-1 uppercase tracking-tight">{{ $piutang->jabatan_peminjam ?? '-' }}</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-{{ $piutang->kategori_peminjam->color() }}-50 text-{{ $piutang->kategori_peminjam->color() }}-700 text-[11px] font-black border border-{{ $piutang->kategori_peminjam->color() }}-200 uppercase tracking-wider">
                        {{ $piutang->kategori_peminjam->label() }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-stone-100 text-stone-600 text-[11px] font-black border border-stone-200 uppercase tracking-wider">
                        Tahun {{ $piutang->tahun_pinjam }}
                    </span>
                </div>
            </div>
            
            <div class="border-t border-stone-100 p-6 space-y-4 flex flex-col bg-stone-50/30">
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-black text-stone-400 uppercase tracking-widest">No. Referensi</span>
                    <span class="text-[13px] font-mono font-black text-stone-700 tracking-tight">{{ $piutang->no_referensi }}</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-black text-stone-400 uppercase tracking-widest">Tanggal Pencatatan</span>
                    <span class="text-[13px] font-bold text-stone-700">{{ $piutang->tanggal_catat->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-black text-stone-400 uppercase tracking-widest">Dicatat Oleh</span>
                    <span class="text-[13px] font-bold text-[#043d2e]">{{ $piutang->pencatat->nama }}</span>
                </div>
            </div>
        </div>

        {{-- Financial Summary Card --}}
        <div class="bg-[#043d2e] rounded-2xl p-6 text-white shadow-lg flex flex-col relative overflow-hidden">
            {{-- Decoration icon --}}
            <svg class="absolute -right-8 -bottom-8 w-40 h-40 opacity-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
            </svg>

            <div class="relative z-10 flex flex-col">
                <p class="text-[11px] font-black text-emerald-300/80 uppercase tracking-widest mb-1">Total Piutang Awal</p>
                <h3 class="text-[22px] font-mono font-black mb-6">{{ format_rupiah($piutang->nominal_awal) }}</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <p class="text-[10px] font-black text-emerald-300/60 uppercase tracking-tight mb-0.5">Sudah Bayar</p>
                        <p class="text-[14px] font-mono font-bold text-white">{{ format_rupiah($piutang->nominal_terbayar) }}</p>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-white/10 flex flex-col">
                    <p class="text-[11px] font-black text-emerald-300/80 uppercase tracking-widest mb-1">Sisa Piutang</p>
                    <h4 class="text-[28px] font-mono font-black text-white leading-none">{{ format_rupiah($piutang->sisa_piutang) }}</h4>
                    
                    @if($piutang->status === 'lunas')
                        <div class="mt-4 px-4 py-2 rounded-xl bg-white/10 border border-white/20 flex items-center gap-2 w-fit">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-[11px] font-black uppercase tracking-widest">Tagihan Lunas</span>
                        </div>
                    @else
                        <div class="mt-6 flex flex-col gap-2">
                            <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-400 rounded-full shadow-[0_0_8px_rgba(52,211,153,0.5)]" style="width: {{ $piutang->progressPersen() }}%"></div>
                            </div>
                            <span class="text-[11px] font-bold text-emerald-300/80 italic">Penagihan selesai {{ $piutang->progressPersen() }}%</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        @if($piutang->keterangan)
        <div class="bg-amber-50 rounded-2xl border border-amber-200/50 p-5 flex flex-col">
            <div class="flex items-center gap-2 mb-2 text-amber-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[11px] font-black uppercase tracking-widest">Catatan Penjelasan</span>
            </div>
            <p class="text-[13px] text-amber-900 leading-relaxed font-medium">
                {{ $piutang->keterangan }}
            </p>
        </div>
        @endif
    </div>

    {{-- Payment History Column --}}
    <div class="lg:col-span-2 flex flex-col gap-6">
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm flex flex-col overflow-hidden">
            <div class="px-6 py-4 bg-stone-50 border-b border-stone-200/60 flex items-center justify-between">
                <h3 class="text-[13px] font-black text-stone-600 uppercase tracking-widest leading-none">Riwayat Pembayaran</h3>
                <span class="px-2 py-0.5 rounded-lg bg-stone-200 text-stone-600 text-[10px] font-black">{{ $piutang->pembayaran->count() }} Transaksi</span>
            </div>

            <div class="overflow-x-auto text-stone-800 flex flex-col h-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stone-100 bg-stone-50/30">
                            <th class="px-6 py-4 text-[12px] font-black text-stone-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-[12px] font-black text-stone-400 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-[12px] font-black text-stone-400 uppercase tracking-wider">Pencatat / Keterangan</th>
                            <th class="px-6 py-4 text-[12px] font-black text-stone-400 uppercase tracking-wider">Bukti</th>
                            <th class="px-6 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-50">
                        @forelse($piutang->pembayaran as $bayar)
                            <tr class="hover:bg-stone-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-[14px] font-bold text-stone-800">{{ $bayar->tanggal_bayar->translatedFormat('d F Y') }}</p>
                                    <p class="text-[11px] text-stone-400 font-medium">{{ $bayar->created_at->format('H:i') }} WIB</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-[15px] font-mono font-black text-[#043d2e]">{{ format_rupiah($bayar->nominal) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col max-w-[200px]">
                                        <p class="text-[13px] font-bold text-stone-700 truncate">{{ $bayar->pencatat->nama }}</p>
                                        <p class="text-[12px] text-stone-500 font-medium italic mt-0.5">{{ $bayar->keterangan ?? '-' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($bayar->bukti_bayar)
                                        <a href="{{ Storage::url($bayar->bukti_bayar) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 text-[11px] font-bold hover:bg-emerald-100 transition-colors group">
                                            <svg class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-[11px] font-bold text-stone-400 italic">No File</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @can('piutang_eksternal.bayar')
                                    <button @click="if(confirm('Batalkan pembayaran ini? Saldo piutang akan dikembalikan dan jurnal kas akan di-void.')) document.getElementById('form-delete-{{ $bayar->id }}').submit()" class="p-2 rounded-lg text-stone-300 hover:text-red-500 hover:bg-red-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    <form id="form-delete-{{ $bayar->id }}" action="{{ route('piutang-eksternal.bayar.destroy', [$piutang->id, $bayar->id]) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3 opacity-20">
                                        <svg class="w-16 h-16 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-[16px] font-black tracking-tight">Belum ada riwayat pembayaran</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Catat Pembayaran --}}
@can('piutang_eksternal.bayar')
<x-modal name="modal-catat-pembayaran" title="Input Pengembalian Piutang" maxWidth="xl">
    <form action="{{ route('piutang-eksternal.bayar', $piutang->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-[#043d2e]/5 border border-[#043d2e]/10 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-[#043d2e] uppercase tracking-widest">Sisa Piutang Saat Ini</span>
                    <span class="text-[18px] font-mono font-black text-[#043d2e]">{{ format_rupiah($piutang->sisa_piutang) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <x-label for="tanggal_bayar" value="Tanggal Bayar" required />
                    <x-datepicker id="tanggal_bayar" name="tanggal_bayar" value="{{ now()->toDateString() }}" required />
                </div>
                <div class="flex flex-col gap-1.5">
                    <x-label for="nominal" value="Nominal Pengembalian" required />
                    <x-currency-input id="nominal" name="nominal" :max="$piutang->sisa_piutang" placeholder="0" required />
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <x-label for="bukti_bayar" value="Unggah Bukti (Mandatory)" required />
                <div class="p-4 border-2 border-dashed border-stone-200 rounded-2xl bg-stone-50/50 hover:bg-stone-50 transition-colors flex flex-col items-center gap-2 group cursor-pointer relative">
                    <input type="file" id="bukti_bayar" name="bukti_bayar" class="absolute inset-0 opacity-0 cursor-pointer" required accept="image/*,application/pdf" @change="fileName = $el.files[0].name" x-data="{ fileName: '' }">
                    <svg class="w-8 h-8 text-stone-300 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span class="text-xs font-bold text-stone-500" x-text="fileName || 'Klik atau tarik file bukti di sini (Max 5MB)'"></span>
                    <span class="text-[10px] text-stone-400">JPG, PNG, atau PDF</span>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <x-label for="keterangan" value="Catatan Pembayaran" />
                <textarea id="keterangan" name="keterangan" rows="2" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none" placeholder="Contoh: Titipan lewat bendahara, transfer bank, dll..."></textarea>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end gap-3">
            <button type="button" @click="show = false" class="py-2.5 px-4 rounded-xl text-sm font-bold text-stone-500 hover:bg-stone-100">Batal</button>
            <button type="submit" class="py-2.5 px-6 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold shadow-sm transition-all shadow-orange-500/20">Catat & Masuk Kas</button>
        </div>
    </form>
</x-modal>
@endcan

@endsection

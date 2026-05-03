@extends('layouts.app')

@section('title', 'Manajemen Persetujuan Pinjaman')
@section('subtitle', 'Daftar pengajuan pinjaman anggota menunggu validasi kas dan approval')

@section('content')

{{-- Statistik Persetujuan --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-stone-200 flex items-center justify-between shadow-sm">
        <div>
            <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Total Kas Tersedia</p>
            <h3 class="text-2xl font-bold font-mono text-[#043d2e]">Rp {{ number_format($totalSaldoKoperasi, 0, ',', '.') }}</h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-[#043d2e]/5 flex items-center justify-center text-[#043d2e] border border-[#043d2e]/10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    {{-- Tombol Copy Link Cek Status --}}
    <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-sm md:col-span-2">
        <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-2">Link Cek Status Pinjaman</p>
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <input type="text" id="linkCekStatus" value="{{ route('pinjaman.guest.status') }}" readonly
                   class="w-full px-4 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-sm font-mono font-semibold text-stone-600 truncate focus:outline-none">
            <button onclick="navigator.clipboard.writeText(document.getElementById('linkCekStatus').value); this.innerHTML='<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'/></svg> Tersalin!'; this.classList.add('bg-emerald-600'); this.classList.remove('bg-[#043d2e]', 'hover:bg-[#043d2e]/90'); setTimeout(() => { this.innerHTML='Copy Link'; this.classList.remove('bg-emerald-600'); this.classList.add('bg-[#043d2e]', 'hover:bg-[#043d2e]/90'); }, 2000);"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-all shadow-md active:scale-95 whitespace-nowrap flex items-center justify-center gap-2">
                Copy Link
            </button>
        </div>
        <p class="text-[10px] text-stone-400 mt-2 font-medium">Bagikan link URL ini kepada anggota via WhatsApp agar mereka dapat mengecek status pinjaman secara mandiri.</p>
    </div>
</div>

<div x-data="{
    selected: [],
    menungguIds: [{{ $pinjamans->filter(fn($p) => $p->status->value === 'menunggu')->pluck('id')->join(',') }}],
    get allSelected() { return this.selected.length > 0 && this.selected.length === this.menungguIds.length; },
    toggleAll() { if (this.allSelected) this.selected = []; else this.selected = [...this.menungguIds]; },
    showRejectModal: false
}">

    {{-- Alert Bar Mass Action --}}
    <div x-show="selected.length > 0" x-transition class="mb-6 bg-[#043d2e] rounded-2xl p-4 md:p-5 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl border border-[#043d2e]">
        <div class="flex items-center gap-4 text-white">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-black text-lg border border-white/20">
                <span x-text="selected.length"></span>
            </div>
            <div>
                <p class="font-bold text-sm tracking-wide">Pengajuan Terpilih</p>
                <p class="text-[11px] text-white/70 font-medium">Siap untuk dieksekusi secara massal</p>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('pinjaman.massApprove') }}" method="POST" class="w-full md:w-auto">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="pinjaman_ids[]" :value="id">
                </template>
                <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-white text-[#043d2e] hover:bg-stone-50 text-sm font-black transition-all shadow-md active:scale-95 whitespace-nowrap" onclick="return confirm('Apakah Anda yakin memberikan persetujuan MASAL pada ' + selected.length + ' pengajuan ini?')">
                    Setujui Semua
                </button>
            </form>
            
            <button type="button" @click="showRejectModal = true" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-black transition-all shadow-md active:scale-95 whitespace-nowrap border border-red-400">
                Tolak
            </button>
        </div>
    </div>

<div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden relative">
    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-stone-600">
            <thead class="bg-stone-50/80 text-stone-500 border-b border-stone-200 text-[11px] uppercase tracking-wider font-bold">
                <tr>
                    <th scope="col" class="px-5 py-4 w-12 text-center">
                        <input type="checkbox" @click="toggleAll()" :checked="allSelected" :disabled="menungguIds.length === 0" class="w-4 h-4 rounded text-[#043d2e] focus:ring-[#043d2e]/20 border-stone-300 disabled:opacity-50 cursor-pointer">
                    </th>
                    <th scope="col" class="px-6 py-4">Referensi / Tgl</th>
                    <th scope="col" class="px-6 py-4">Peminjam</th>
                    <th scope="col" class="px-6 py-4">Informasi Pencairan (Bank & Rekening)</th>
                    <th scope="col" class="px-6 py-4">Nominal / Tenor</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($pinjamans as $pinjaman)
                <tr class="hover:bg-stone-50/50 transition-colors" :class="{ 'bg-stone-50': selected.includes({{ $pinjaman->id }}) }">
                    <td class="px-5 py-4 text-center">
                        @if($pinjaman->status->value === 'menunggu')
                        <input type="checkbox" x-model="selected" value="{{ $pinjaman->id }}" class="w-4 h-4 rounded text-[#043d2e] focus:ring-[#043d2e]/20 border-stone-300 cursor-pointer">
                        @else
                        <div class="w-4 h-4 rounded border border-stone-200 bg-stone-100 opacity-50 inline-block"></div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-0.5">
                            <span class="font-mono text-[#043d2e] font-bold text-[13px]">{{ $pinjaman->no_referensi }}</span>
                            <span class="text-[11px] font-medium text-stone-500">{{ $pinjaman->tanggal_pengajuan->format('d M Y - H:i') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-stone-800 font-bold text-[13px]">{{ $pinjaman->anggota->nama }}</span>
                            <span class="text-[11px] font-medium text-stone-500">NIP: {{ $pinjaman->anggota->nip }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded bg-stone-100 text-stone-600 font-bold text-[10px] uppercase tracking-wider border border-stone-200">{{ $pinjaman->nama_bank }}</span>
                                <span class="font-mono text-stone-800 font-bold text-[12px]">{{ $pinjaman->no_rekening }}</span>
                            </div>
                            <span class="text-[11px] font-medium text-stone-500">A.N: {{ $pinjaman->nama_rekening }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-0.5">
                            <span class="font-black text-stone-800 font-mono text-[13px]">Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}</span>
                            <span class="text-[11px] font-medium text-stone-500">{{ $pinjaman->tenor_bulan }} Bulan @ {{ number_format($pinjaman->bunga_persen, 1) }}% Flat</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($pinjaman->status->value === 'menunggu')
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold uppercase tracking-wide">
                            Menunggu Approval
                        </span>
                        @elseif($pinjaman->status->value === 'berjalan')
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase tracking-wide">
                            Aktif / Berjalan
                        </span>
                        @elseif($pinjaman->status->value === 'lunas')
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold uppercase tracking-wide">
                            Lunas
                        </span>
                        @elseif($pinjaman->status->value === 'ditolak')
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-red-50 text-red-700 border border-red-200 text-[10px] font-bold uppercase tracking-wide">
                            Ditolak
                        </span>
                        @elseif($pinjaman->status->value === 'dibatalkan')
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-stone-100 text-stone-600 border border-stone-200 text-[10px] font-bold uppercase tracking-wide">
                            Dibatalkan
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-stone-100 text-stone-700 border border-stone-200 text-[10px] font-bold uppercase tracking-wide">
                            {{ $pinjaman->status->label() }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        <a href="{{ route('pinjaman.show', $pinjaman->id) }}" 
                           class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-white text-[#043d2e] hover:bg-stone-50 font-bold transition-colors border border-stone-200 hover:border-stone-300 shadow-sm text-[11px] uppercase tracking-wide">
                            Tinjau
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-stone-500 font-medium text-sm">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Belum ada riwayat pengajuan pinjaman.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pinjamans->hasPages())
    <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
        {{ $pinjamans->links() }}
    </div>
    @endif
</div>

    {{-- Modal Mass Reject --}}
    <div x-show="showRejectModal" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-stone-900/60 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showRejectModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-stone-200 overflow-hidden transform transition-all">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 border border-red-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-stone-900">Tolak Secara Massal</h3>
                        <p class="text-[12px] font-medium text-stone-500">Akan menolak <span x-text="selected.length" class="font-bold text-red-600"></span> pengajuan sekaligus</p>
                    </div>
                </div>
                
                <form action="{{ route('pinjaman.massReject') }}" method="POST" id="massRejectForm">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="pinjaman_ids[]" :value="id">
                    </template>
                    
                    <div class="mt-4">
                        <label class="block text-[13px] font-bold text-stone-700 mb-2">Alasan Penolakan Bersama <span class="text-red-500">*</span></label>
                        <textarea name="alasan_penolakan" rows="3" required class="w-full px-4 py-3 rounded-xl border border-stone-300 bg-stone-50 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm outline-none transition-colors" placeholder="Tuliskan kenapa pengajuan-pengajuan ini ditolak..."></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-stone-50 px-6 py-4 border-t border-stone-200 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                <button type="button" @click="showRejectModal = false" class="px-5 py-2.5 rounded-xl border border-stone-300 bg-white text-stone-700 font-bold text-sm hover:bg-stone-50 transition-colors">Batal</button>
                <button type="submit" form="massRejectForm" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-md transition-colors">Tolak Semua Terpilih</button>
            </div>
        </div>
    </div>

</div>
@endsection

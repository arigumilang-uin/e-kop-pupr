@extends('layouts.app')

@section('title', 'Tinjauan Persetujuan Pinjaman')
@section('subtitle', 'Detail pengajuan ref: ' . $pinjaman->no_referensi)

@section('actions')
<x-back-button fallback="{{ route('pinjaman.index') }}">Kembali</x-back-button>
@endsection

@section('content')

@php
    $bisaApprove = $saldoAktif >= $pinjaman->nominal_pinjaman;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Card: Data Peminjam --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden p-6 sm:p-8">
            <h3 class="text-sm font-semibold text-stone-400 uppercase tracking-wider mb-4 border-b border-stone-100 pb-2">Informasi Anggota Pemohon</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">NIP</p>
                    <p class="font-mono text-[13px] font-bold text-[#043d2e]">{{ $pinjaman->anggota->nip }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Nama Anggota</p>
                    <p class="font-bold text-[13px] text-stone-800">{{ $pinjaman->anggota->nama }}</p>
                </div>
                <div class="col-span-3">
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Bidang Kerja</p>
                    <p class="font-bold text-[13px] text-stone-800">{{ $pinjaman->anggota->bidang ? $pinjaman->anggota->bidang->nama_bidang : '-' }}</p>
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-stone-50 rounded-xl border border-stone-200">
                <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Target Rekening Pencairan</p>
                <div class="flex flex-wrap items-center gap-2.5 mt-2">
                    <span class="px-2 py-0.5 rounded-md bg-stone-200 text-stone-700 text-[10px] font-bold tracking-wider uppercase">{{ strtoupper($pinjaman->nama_bank) }}</span>
                    <span class="font-mono text-[13px] font-bold text-stone-800">{{ $pinjaman->no_rekening }}</span>
                    <span class="text-[11px] font-medium text-stone-500">a.n {{ $pinjaman->nama_rekening }}</span>
                </div>
            </div>
        </div>

        {{-- Card: Detail Rincian Nominal --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden p-6 sm:p-8">
            <h3 class="text-sm font-semibold text-stone-400 uppercase tracking-wider mb-4 border-b border-stone-100 pb-2">Rincian Nominal Pengajuan</h3>
            
            <div class="flex items-center justify-between p-5 bg-[#043d2e]/5 rounded-xl border border-[#043d2e]/10 mb-6">
                <div>
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Pokok Pinjaman</p>
                    <p class="text-2xl font-bold font-mono text-[#043d2e]">Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Tenor Kredit</p>
                    <p class="text-xl font-black text-[#043d2e]">{{ $pinjaman->tenor_bulan }}<span class="text-sm font-bold text-stone-500 opacity-70"> / Bulan</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    @php
                        $swpPersen = $pinjaman->nominal_pinjaman > 0 ? round(($pinjaman->potongan_swp / $pinjaman->nominal_pinjaman) * 100, 1) : 0;
                        $resikoPersen = $pinjaman->nominal_pinjaman > 0 ? round(($pinjaman->potongan_dana_resiko / $pinjaman->nominal_pinjaman) * 100, 1) : 0;
                        $adminPersen = $pinjaman->nominal_pinjaman > 0 ? round(($pinjaman->potongan_biaya_admin / $pinjaman->nominal_pinjaman) * 100, 1) : 0;
                        $totalPotPersen = $swpPersen + $resikoPersen + $adminPersen;
                    @endphp
                    <h4 class="text-[11px] font-bold text-stone-400 uppercase mb-3 tracking-widest">Skema Pemotongan ({{ $totalPotPersen }}%)</h4>
                    <ul class="space-y-3 text-[13px]">
                        <li class="flex justify-between">
                            <span class="text-stone-500 font-medium">SWP ({{ $swpPersen }}%)</span>
                            <span class="font-mono text-stone-700 font-bold">Rp {{ number_format($pinjaman->potongan_swp, 0, ',', '.') }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-stone-500 font-medium">Dana Resiko ({{ $resikoPersen }}%)</span>
                            <span class="font-mono text-stone-700 font-bold">Rp {{ number_format($pinjaman->potongan_dana_resiko, 0, ',', '.') }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-stone-500 font-medium">Biaya Admin ({{ $adminPersen }}%)</span>
                            <span class="font-mono text-stone-700 font-bold">Rp {{ number_format($pinjaman->potongan_biaya_admin, 0, ',', '.') }}</span>
                        </li>
                        <li class="flex justify-between pt-2 border-t border-stone-100 font-bold text-red-500">
                            <span>Total Dipotong</span>
                            <span class="font-mono">- Rp {{ number_format($pinjaman->total_potongan, 0, ',', '.') }}</span>
                        </li>
                    </ul>
                    
                    <div class="mt-5 p-3.5 bg-emerald-50 rounded-xl border border-emerald-200 flex justify-between items-center text-emerald-800 shadow-sm">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Dana Cair Diterima</span>
                        <span class="font-black font-mono text-lg tracking-tight">Rp {{ number_format($pinjaman->dana_diterima, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-[11px] font-bold text-stone-400 uppercase mb-3 tracking-widest">Skema Angsuran</h4>
                    <ul class="space-y-3 text-[13px]">
                        <li class="flex justify-between">
                            <span class="text-stone-500 font-medium">Cicil Pokok /bln</span>
                            <span class="font-mono text-stone-700 font-bold">Rp {{ number_format($pinjaman->angsuran_pokok, 0, ',', '.') }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-stone-500 font-medium">Cicil Bunga /bln</span>
                            <span class="font-mono text-stone-700 font-bold">Rp {{ number_format($pinjaman->angsuran_bunga, 0, ',', '.') }}</span>
                        </li>
                        <li class="flex justify-between pt-2 border-t border-stone-100 font-black text-[#043d2e] items-center">
                            <span>Total Cicilan per Bulan</span>
                            <span class="font-mono text-[17px]">Rp {{ number_format($pinjaman->total_angsuran, 0, ',', '.') }}</span>
                        </li>
                    </ul>
                </div>
        </div>

        @if(in_array($pinjaman->status->value, ['berjalan', 'lunas']))
        {{-- Card: Jadwal & Pembayaran Angsuran --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden p-6 sm:p-8">
            <h3 class="text-sm font-semibold text-stone-800 mb-4 border-b border-stone-100 pb-2">Jadwal & Pembayaran Angsuran</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-stone-50/80 border-b border-stone-100">
                            <th class="px-4 py-3 text-[11px] font-bold text-stone-500 uppercase tracking-wider">Angsuran Ke-</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-stone-500 uppercase tracking-wider">Jatuh Tempo</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-stone-500 uppercase tracking-wider">Total Tagihan</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-stone-500 uppercase tracking-wider">Status / Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($pinjaman->angsuran as $angs)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-[#043d2e]">Ke-{{ $angs->angsuran_ke }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-stone-600 font-medium">{{ $angs->tanggal_jatuh_tempo->format('d M Y') }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-mono text-sm font-bold text-stone-700">Rp {{ number_format($angs->nominal_total, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($angs->status->value === 'lunas')
                                    <div class="inline-flex flex-col items-start">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 uppercase tracking-widest border border-emerald-200">Lunas</span>
                                        <span class="text-[11px] text-stone-400 mt-1.5 font-medium">Tgl: {{ $angs->tanggal_bayar ? $angs->tanggal_bayar->format('d/m/Y') : '-' }}</span>
                                    </div>
                                @elseif($angs->status->value === 'belum')
                                    @can('pinjaman.bayar')
                                    <form action="{{ route('pinjaman.angsuran.bayar', [$pinjaman->id, $angs->id]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-stone-50 text-[#043d2e] hover:bg-[#043d2e] hover:text-white border border-stone-200 hover:border-transparent text-xs font-bold transition-all" onclick="return confirm('Catat pembayaran Rp {{ number_format($angs->nominal_total, 0, ',', '.') }} untuk angsuran ke-{{ $angs->angsuran_ke }}? Saldo akan langsung masuk ke Kas Koperasi.')">
                                            Lunasi Sekarang
                                        </button>
                                    </form>
                                    @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-600 uppercase tracking-widest border border-amber-200">Belum Lunas</span>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-stone-400 font-medium tracking-wide">Jadwal angsuran belum digenerate.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- Kolom Kanan: Status & Tindakan --}}
    <div class="space-y-6">
        <div class="bg-gradient-to-br from-[#043d2e] to-[#043d2e]/90 rounded-2xl shadow-lg shadow-[#043d2e]/10 p-6 text-white relative overflow-hidden border border-[#043d2e]">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <svg class="w-32 h-32 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-[11px] font-bold text-white/70 uppercase tracking-wider mb-4">Status Pengajuan Saat Ini</h3>
                @if($pinjaman->status->value === 'menunggu')
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-500/20 text-amber-200 font-bold rounded-lg text-[13px] border border-amber-500/30 shadow-inner">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse border border-amber-200"></span> Menunggu Validasi & Approval
                </div>
                @else
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 text-white font-bold rounded-lg text-[13px] border border-white/20 capitalize shadow-inner">
                    {{ $pinjaman->status->value }}
                </div>
                @endif
                
                <div class="mt-6 flex flex-col gap-3">
                    <div>
                        <p class="text-[10px] text-white/60 mb-0.5">Tanggal Pengajuan</p>
                        <p class="text-xs font-mono text-white/90">{{ $pinjaman->tanggal_pengajuan->format('d F Y - H:i:s') }}</p>
                    </div>
                    @if($pinjaman->tanggal_approval)
                    <div>
                        <p class="text-[10px] text-white/60 mb-0.5">Dieksekusi Pada</p>
                        <p class="text-xs font-mono text-white/90">{{ $pinjaman->tanggal_approval->format('d F Y - H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-white/60 mb-0.5">Oleh Administrator</p>
                        <p class="text-xs font-bold text-white/90">{{ $pinjaman->approver->nama ?? 'Sistem' }}</p>
                    </div>
                    @endif
                </div>

                @if($pinjaman->catatan)
                <div class="mt-5 p-3.5 bg-red-900/40 border border-red-500/30 rounded-xl shadow-inner">
                    <p class="text-[11px] font-bold text-red-200 mb-1">Catatan/Alasan:</p>
                    <p class="text-sm text-red-100 font-medium">{{ $pinjaman->catatan }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($pinjaman->status->value === 'menunggu')
        @canany(['pinjaman.approve', 'pinjaman.reject'])
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 overflow-hidden relative">
            <h3 class="text-sm font-bold text-stone-800 mb-4 border-b border-stone-100 pb-3">Validasi Persetujuan</h3>
            
            @if($pinjaman->is_override)
            <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-800 flex items-start gap-3 shadow-inner">
                <svg class="w-6 h-6 shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <strong class="block mb-1 text-amber-900">Perhatian Khusus Buka Kunci (Override)</strong>
                    Anggota ini sudah memiliki pinjaman lain yang sedang <b>aktif/berjalan</b> pada tahun ini. Ini merupakan <b>Pengajuan Pinjaman Tambahan</b>. Pertimbangkan dengan hati-hati sebelum menyetujui.
                </div>
            </div>
            @endif

            <div class="mb-5 bg-stone-50 p-4 rounded-xl border border-stone-200">
                <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Kas Saldo Koperasi Tersedia</p>
                <p class="text-2xl font-black font-mono tracking-tight {{ $bisaApprove ? 'text-[#043d2e]' : 'text-red-600' }}">
                    Rp {{ number_format($saldoAktif, 0, ',', '.') }}
                </p>
            </div>
            
            @if(!$bisaApprove)
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-[13px] text-red-800 shadow-inner">
                <p class="font-bold mb-1 text-red-900">Peringatan Kritis!</p>
                Kas Saldo Koperasi tidak mencukupi untuk membiayai pinjaman ini sebesar (<b>Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}</b>).<br><br>Anda harus menunggu ada setoran Simpanan baru atau menolak pengajuan ini.
            </div>
            @else
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-[13px] text-emerald-800 font-medium shadow-inner flex gap-2.5 items-start">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Sistem Valid: Saldo kas koperasi saat ini <b>cukup</b> untuk menyetujui pendanaan pengajuan ini.</span>
            </div>
            @endif

            <div class="space-y-3 pt-4 border-t border-stone-100">
                {{-- Form Approve --}}
                @can('pinjaman.approve')
                <form action="{{ route('pinjaman.approve', $pinjaman->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" @if(!$bisaApprove) disabled @endif class="w-full py-3 rounded-xl {{ $bisaApprove ? 'bg-[#043d2e] hover:bg-[#043d2e]/90 text-white shadow-md active:scale-[0.98]' : 'bg-stone-200 text-stone-400 cursor-not-allowed' }} font-bold transition-all disabled:opacity-70 flex items-center justify-center gap-2" onclick="return confirm('Apakah Anda yakin memberikan persetujuan pendanaan ini?')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        Setujui Pengajuan & Cairkan
                    </button>
                </form>
                @endcan

                {{-- Trigger Reject Modal --}}
                @can('pinjaman.reject')
                <button type="button" class="w-full py-2.5 rounded-xl text-red-600 bg-white hover:bg-red-50 font-bold transition-all border border-red-200 hover:border-red-300" onclick="document.getElementById('rejectModal').classList.remove('hidden')">
                    Tolak Pengajuan...
                </button>
                @endcan
            </div>
        </div>

        {{-- Modal Tolak (Hidden by default) --}}
        <div id="rejectModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="document.getElementById('rejectModal').classList.add('hidden')">
                    <div class="absolute inset-0 bg-stone-900/75 backdrop-blur-sm"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-stone-200">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-stone-900" id="modal-title">Tolak Pengajuan Pinjaman</h3>
                                <div class="mt-4">
                                    <form action="{{ route('pinjaman.reject', $pinjaman->id) }}" method="POST" id="rejectForm">
                                        @csrf
                                        @method('PATCH')
                                        <label class="block text-[13px] font-bold text-stone-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea name="alasan_penolakan" rows="3" required class="w-full px-4 py-3 rounded-xl border border-stone-300 bg-stone-50 focus:bg-white focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm outline-none transition-colors" placeholder="Sebutkan kenapa pengajuan ini tidak di ACC..."></textarea>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-stone-50 px-4 py-3 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-stone-200">
                        <button type="button" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-stone-300 px-4 py-2.5 bg-white text-base font-bold text-stone-700 hover:bg-stone-50 transition-colors shadow-sm sm:text-sm" onclick="document.getElementById('rejectModal').classList.add('hidden')">
                            Batal
                        </button>
                        <button type="submit" form="rejectForm" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent px-4 py-2.5 bg-red-600 text-base font-bold text-white hover:bg-red-700 transition-colors shadow-md sm:text-sm">
                            Konfirmasi Penolakan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endcanany
        @endif
    </div>
</div>

@endsection

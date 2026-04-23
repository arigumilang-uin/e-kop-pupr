@extends('layouts.app')

@section('title', 'Tinjauan Persetujuan Pinjaman')
@section('subtitle', 'Detail pengajuan ref: ' . $pinjaman->no_referensi)

@section('actions')
<a href="{{ route('pinjaman.index') }}" class="py-2.5 px-4 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-sm font-medium transition-colors">
    Kembali
</a>
@endsection

@section('content')

@php
    $bisaApprove = $saldoAktif >= $pinjaman->nominal_pinjaman;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Card: Data Peminjam --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Informasi Anggota Pemohon</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate-500 mb-1">NIP</p>
                    <p class="font-mono text-sm font-semibold text-slate-800">{{ $pinjaman->anggota->nip }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-slate-500 mb-1">Nama Anggota</p>
                    <p class="font-medium text-sm text-slate-800">{{ $pinjaman->anggota->nama }}</p>
                </div>
                <div class="col-span-3">
                    <p class="text-xs text-slate-500 mb-1">Bidang Kerja</p>
                    <p class="font-medium text-sm text-slate-800">{{ $pinjaman->anggota->bidang ? $pinjaman->anggota->bidang->nama_bidang : '-' }}</p>
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-slate-50 rounded-xl border border-slate-200">
                <p class="text-xs text-slate-500 mb-1">Target Rekening Pencairan</p>
                <div class="flex items-center gap-3 mt-2">
                    <span class="px-2.5 py-1 rounded-md bg-blue-100 text-blue-700 text-xs font-bold">{{ strtoupper($pinjaman->nama_bank) }}</span>
                    <span class="font-mono text-sm font-semibold text-slate-800">{{ $pinjaman->no_rekening }}</span>
                    <span class="text-sm text-slate-500">a.n {{ $pinjaman->nama_rekening }}</span>
                </div>
            </div>
        </div>

        {{-- Card: Detail Rincian Nominal --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Rincian Nominal Pengajuan</h3>
            
            <div class="flex items-center justify-between p-4 bg-blue-50/50 rounded-xl border border-blue-100 mb-6">
                <div>
                    <p class="text-xs text-blue-600 font-medium mb-1">Pokok Pinjaman</p>
                    <p class="text-2xl font-bold font-mono text-blue-800">Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-blue-600 font-medium mb-1">Tenor Kredit</p>
                    <p class="text-xl font-bold text-blue-800">{{ $pinjaman->tenor_bulan }}<span class="text-sm font-normal"> Bulan</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase mb-3">Skema Pemotongan (5%)</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex justify-between">
                            <span class="text-slate-500">SWP (3%)</span>
                            <span class="font-mono text-slate-700">Rp {{ number_format($pinjaman->potongan_swp, 0, ',', '.') }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-slate-500">Dana Resiko (1.5%)</span>
                            <span class="font-mono text-slate-700">Rp {{ number_format($pinjaman->potongan_dana_resiko, 0, ',', '.') }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-slate-500">Biaya Admin (0.5%)</span>
                            <span class="font-mono text-slate-700">Rp {{ number_format($pinjaman->potongan_biaya_admin, 0, ',', '.') }}</span>
                        </li>
                        <li class="flex justify-between pt-2 border-t border-slate-100 font-semibold text-amber-600">
                            <span>Total Dipotong</span>
                            <span class="font-mono">- Rp {{ number_format($pinjaman->total_potongan, 0, ',', '.') }}</span>
                        </li>
                    </ul>
                    
                    <div class="mt-4 p-3 bg-emerald-50 rounded-xl border border-emerald-100 flex justify-between items-center text-emerald-800">
                        <span class="text-xs font-semibold uppercase">Dana Cair Diterima</span>
                        <span class="font-bold font-mono text-lg">Rp {{ number_format($pinjaman->dana_diterima, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase mb-3">Skema Angsuran</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex justify-between">
                            <span class="text-slate-500">Cicil Pokok /bln</span>
                            <span class="font-mono text-slate-700">Rp {{ number_format($pinjaman->angsuran_pokok, 0, ',', '.') }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-slate-500">Cicil Bunga /bln</span>
                            <span class="font-mono text-slate-700">Rp {{ number_format($pinjaman->angsuran_bunga, 0, ',', '.') }}</span>
                        </li>
                        <li class="flex justify-between pt-2 border-t border-slate-100 font-bold text-slate-800">
                            <span>Total Cicilan per Bulan</span>
                            <span class="font-mono text-lg">Rp {{ number_format($pinjaman->total_angsuran, 0, ',', '.') }}</span>
                        </li>
                    </ul>
                </div>
        </div>

        @if(in_array($pinjaman->status->value, ['berjalan', 'lunas']))
        {{-- Card: Jadwal & Pembayaran Angsuran --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8">
            <h3 class="text-sm font-semibold text-slate-800 mb-4 border-b border-slate-100 pb-2">Jadwal & Pembayaran Angsuran</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Angsuran Ke-</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Jatuh Tempo</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Total Tagihan</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Status / Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pinjaman->angsuran as $angs)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-slate-700">Ke-{{ $angs->angsuran_ke }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-slate-600">{{ $angs->tanggal_jatuh_tempo->format('d M Y') }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-mono text-sm font-semibold text-slate-700">Rp {{ number_format($angs->nominal_total, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($angs->status->value === 'lunas')
                                    <div class="inline-flex flex-col items-start">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-widest">Lunas</span>
                                        <span class="text-xs text-slate-400 mt-1">Tgl: {{ $angs->tanggal_bayar ? $angs->tanggal_bayar->format('d/m/Y') : '-' }}</span>
                                    </div>
                                @elseif($angs->status->value === 'belum')
                                    <form action="{{ route('pinjaman.angsuran.bayar', [$pinjaman->id, $angs->id]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-transparent text-xs font-semibold transition-colors" onclick="return confirm('Catat pembayaran Rp {{ number_format($angs->nominal_total, 0, ',', '.') }} untuk angsuran ke-{{ $angs->angsuran_ke }}? Saldo akan langsung masuk ke Kas Koperasi.')">
                                            Lunasi Sekarang
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">Jadwal angsuran belum digenerate.</td>
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
        <div class="bg-slate-900 rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
            <div class="relative z-10">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Status Pengajuan Saat Ini</h3>
                @if($pinjaman->status->value === 'menunggu')
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-500/20 text-amber-300 font-medium rounded-lg text-sm border border-amber-500/30">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Menunggu Validasi & Approval
                </div>
                @else
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 text-white font-medium rounded-lg text-sm border border-white/20 capitalize">
                    {{ $pinjaman->status->value }}
                </div>
                @endif
                <p class="text-[11px] text-slate-400 mt-4">Tanggal Pengajuan: <br><span class="text-slate-200">{{ $pinjaman->tanggal_pengajuan->format('d F Y - H:i:s') }}</span></p>
                @if($pinjaman->tanggal_approval)
                <p class="text-[11px] text-slate-400 mt-2">Dieksekusi Pada: <br><span class="text-slate-200">{{ $pinjaman->tanggal_approval->format('d F Y - H:i:s') }}</span></p>
                <p class="text-[11px] text-slate-400 mt-2">Oleh Administrator: <br><span class="text-slate-200">{{ $pinjaman->approver->nama ?? 'Sistem' }}</span></p>
                @endif
                @if($pinjaman->catatan)
                <div class="mt-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                    <p class="text-[11px] text-red-300 mb-1">Alasan Penolakan:</p>
                    <p class="text-sm text-red-200">{{ $pinjaman->catatan }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($pinjaman->status->value === 'menunggu')
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 line-clamp-4">
            <h3 class="text-sm font-semibold text-slate-800 mb-4 border-b border-slate-100 pb-2">Validasi Persetujuan</h3>
            
            @if($pinjaman->is_override)
            <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-800 flex items-start gap-3">
                <svg class="w-6 h-6 shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <strong class="block mb-1 text-amber-900">Perhatian Khusus Buka Kunci (Override)</strong>
                    Anggota ini sudah memiliki pinjaman lain yang sedang <b>aktif/berjalan</b> pada tahun ini. Ini merupakan <b>Pengajuan Pinjaman Tambahan</b>. Pertimbangkan dengan hati-hati sebelum menyetujui.
                </div>
            </div>
            @endif

            <div class="mb-4">
                <p class="text-xs text-slate-500 mb-1">Kas Saldo Koperasi Tersedia</p>
                <p class="text-xl font-bold font-mono {{ $bisaApprove ? 'text-emerald-600' : 'text-red-500' }}">
                    Rp {{ number_format($saldoAktif, 0, ',', '.') }}
                </p>
            </div>
            
            @if(!$bisaApprove)
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
                <p class="font-semibold mb-1">Peringatan Kritis!</p>
                Kas Saldo Koperasi (<b>Rp {{ number_format($saldoAktif, 0, ',', '.') }}</b>) tidak mencukupi untuk membiayai pinjaman ini sebesar (<b>Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}</b>).<br><br>Anda harus menunggu ada setoran Simpanan baru atau menolak pengajuan ini.
            </div>
            @else
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-700">
                Sistem Valid: Saldo kas koperasi saat ini <b>cukup</b> untuk menyetujui pendanaan pengajuan ini.
            </div>
            @endif

            <div class="space-y-3 pt-3 border-t border-slate-100">
                {{-- Form Approve --}}
                <form action="{{ route('pinjaman.approve', $pinjaman->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" @if(!$bisaApprove) disabled @endif class="w-full py-3 rounded-xl {{ $bisaApprove ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/25' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }} font-bold transition-all disabled:opacity-50" onclick="return confirm('Apakah Anda yakin memberikan persetujuan pendanaan ini?')">
                        Setujui Pengajuan & Cairkan
                    </button>
                </form>

                {{-- Trigger Reject Modal --}}
                <button type="button" class="w-full py-2 rounded-xl text-red-500 bg-red-50 hover:bg-red-100 font-semibold transition-colors border border-red-100" onclick="document.getElementById('rejectModal').classList.remove('hidden')">
                    Tolak Pengajuan...
                </button>
            </div>
        </div>

        {{-- Modal Tolak (Hidden by default) --}}
        <div id="rejectModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="document.getElementById('rejectModal').classList.add('hidden')">
                    <div class="absolute inset-0 bg-slate-900 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-semibold text-slate-900" id="modal-title">Tolak Pengajuan Pinjaman</h3>
                                <div class="mt-4">
                                    <form action="{{ route('pinjaman.reject', $pinjaman->id) }}" method="POST" id="rejectForm">
                                        @csrf
                                        @method('PATCH')
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea name="alasan_penolakan" rows="3" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm" placeholder="Sebutkan kenapa pengajuan ini tidak di ACC..."></textarea>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200">
                        <button type="submit" form="rejectForm" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Konfirmasi Penolakan
                        </button>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('rejectModal').classList.add('hidden')">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@extends('layouts.guest')
@section('content')

<main class="max-w-6xl mx-auto px-4 py-8 md:py-16">
    
    <!-- Premium Header Area (Consistent with Form) -->
    <div class="mb-10 text-left max-w-2xl">
        <div class="flex flex-row items-center justify-start gap-4 mb-6">
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 46px; font-weight: 800; margin: 0; letter-spacing: -2.5px; line-height: 1; color: #0f172a; white-space: nowrap;">ASSET</h1>
            <div style="width: 2px; height: 38px; background: #cbd5e1;"></div>
            <p style="color: #475569; font-size: 12px; margin: 0; line-height: 1.4; font-weight: 600;">
                Konfirmasi Pengajuan
            </p>
        </div>
        
        <p class="text-slate-500 text-[14px] leading-relaxed">
            Harap periksa kembali rincian data Anda sebelum pengajuan ini dikirimkan ke sistem antrean KSP PUPR PKPP Riau.
        </p>
    </div>

    @if($errors->any() || session('error'))
    <div class="mb-8 p-5 rounded-2xl bg-red-50/50 border border-red-100 flex items-start gap-4">
        <div class="bg-white p-2 rounded-full shadow-sm shrink-0">
            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <h3 class="text-red-800 font-bold text-[14px] mb-1.5">Gagal memproses pengajuan</h3>
            <ul class="text-red-600/80 text-[13px] list-disc list-inside space-y-1">
                @if(session('error')) <li>{{ session('error') }}</li> @endif
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('pinjaman.guest.store', $periode->token) }}" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        @csrf
        
        <!-- Data tersembunyi untuk proses submission -->
        <input type="hidden" name="nip" value="{{ $request->nip }}">
        <input type="hidden" name="nama_bank" value="{{ $request->nama_bank }}">
        <input type="hidden" name="nama_rekening" value="{{ $request->nama_rekening }}">
        <input type="hidden" name="no_rekening" value="{{ $request->no_rekening }}">
        <input type="hidden" name="nominal_pinjaman" value="{{ $request->nominal_pinjaman }}">
        <input type="hidden" name="tenor_bulan" value="{{ $request->tenor_bulan }}">
        @if(session('needs_override_confirmation'))
        <input type="hidden" name="confirm_override" value="1">
        @endif

        <!-- Left Column: Data Review -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Section 1: Data Keanggotaan -->
            <x-card class="bg-white">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Data Keanggotaan</h2>
                </div>
                
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-1">Nama Lengkap</p>
                        <p class="font-bold text-[15px] text-slate-800">{{ $anggota->nama }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-1">NIP</p>
                        <p class="font-mono font-bold text-[15px] text-slate-800">{{ $anggota->nip }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-1">Bidang / Instansi</p>
                        <p class="font-semibold text-[14px] text-slate-700">{{ $anggota->bidang->nama_bidang ?? '-' }}</p>
                    </div>
                </div>
            </x-card>

            <!-- Section 2: Rekening Pencairan -->
            <x-card class="bg-white">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Rekening Pencairan</h2>
                </div>
                
                <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100 grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                    <div class="sm:col-span-2">
                        <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-1">Nama Bank</p>
                        <p class="font-bold text-[15px] text-slate-800">{{ $request->nama_bank }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-1">Nomor Rekening</p>
                        <p class="font-mono font-bold text-lg text-slate-800 tracking-wider">{{ $request->no_rekening }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-1">Nama Pemilik Rekening</p>
                        <p class="font-bold text-[15px] text-slate-800">{{ $request->nama_rekening }}</p>
                    </div>
                </div>
            </x-card>

            @if(session('needs_override_confirmation'))
            <div class="p-5 rounded-2xl bg-orange-50 border border-orange-200 flex items-start gap-4">
                <div class="bg-white p-2 rounded-full shadow-sm shrink-0">
                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-orange-800 font-bold text-[14px] mb-1.5">Persetujuan Khusus Terdeteksi</h3>
                    <p class="text-[13.5px] text-orange-900/80 leading-relaxed font-medium">
                        {{ session('needs_override_confirmation') }}
                    </p>
                </div>
            </div>
            @endif

        </div>

        <!-- Right Column: Final Invoice & Action -->
        <div class="lg:col-span-5 h-full">
            <div class="sticky top-8 space-y-6">
                <!-- Rincian Validasi (Identik Styling form) -->
                <div class="bg-[#0f172a] rounded-[24px] p-6 lg:p-8 text-white relative overflow-hidden shadow-2xl shadow-slate-900/20">
                    <!-- Background Decor -->
                    <div class="absolute -right-20 -top-20 opacity-[0.03] pointer-events-none">
                        <svg width="300" height="300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13.5h-13L12 6.5z"/></svg>
                    </div>
                    
                    <div class="relative z-10 flex flex-col h-full space-y-8">
                        <div>
                            <div class="flex items-center gap-2 mb-2 text-emerald-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <h3 class="text-xs font-bold tracking-widest uppercase">Invoice Validasi</h3>
                            </div>
                            <p class="text-slate-400 text-[13px]">Rincian akhir transaksi Anda</p>
                        </div>

                        <!-- Nominal Utama -->
                        <div>
                            <p class="text-slate-400 text-[13px] font-medium mb-1">Nominal Pinjaman</p>
                            <div class="text-4xl font-mono font-extrabold text-white tracking-tight">
                                Rp {{ number_format($rincian['nominal_pinjaman'], 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Ketentuan -->
                        <div class="bg-white/5 rounded-2xl p-5 space-y-3 border border-white/5">
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-slate-400 font-medium">Total Bunga ({{ floatval($pengaturan['bunga_persen']) }}%)</span>
                                <span class="font-mono text-emerald-400 font-semibold">Rp {{ number_format($rincian['total_bunga'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-slate-400 font-medium">Tenor</span>
                                <span class="font-mono font-semibold">{{ $rincian['tenor_bulan'] }} Bln</span>
                            </div>
                            <div class="pt-3 mt-3 border-t border-white/10 flex flex-col justify-center gap-1.5">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-bold text-slate-200">Angsuran / Bulan</span>
                                    <span class="font-mono font-bold text-lg text-white">Rp {{ number_format($rincian['total_angsuran'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono tracking-wide">
                                    <span>Pokok</span>
                                    <span>Rp {{ number_format($rincian['angsuran_pokok'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono tracking-wide">
                                    <span>Bunga</span>
                                    <span>Rp {{ number_format($rincian['angsuran_bunga'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="pt-3 mt-3 border-t border-white/10 flex justify-between items-center">
                                <span class="text-[13px] font-medium text-slate-300">Total Pengembalian</span>
                                <span class="font-mono font-bold text-emerald-400">Rp {{ number_format($rincian['total_bayar'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Potongan -->
                        <div class="bg-white/5 rounded-2xl border border-white/5 overflow-hidden">
                            <div class="px-5 py-3 border-b border-white/5 bg-white/5">
                                <span class="text-slate-300 text-[11px] font-bold tracking-widest uppercase">Potongan Awal (Deduction)</span>
                            </div>
                            <div class="p-5 space-y-3">
                                <div class="flex justify-between items-center text-[13px]">
                                    <span class="text-slate-400 font-medium">SWP ({{ floatval($pengaturan['swp_persen']) }}%)</span>
                                    <span class="font-mono text-slate-300 font-semibold">Rp {{ number_format($rincian['potongan_swp'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[13px]">
                                    <span class="text-slate-400 font-medium">Dana Resiko ({{ floatval($pengaturan['resiko_persen']) }}%)</span>
                                    <span class="font-mono text-slate-300 font-semibold">Rp {{ number_format($rincian['potongan_dana_resiko'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[13px]">
                                    <span class="text-slate-400 font-medium">Administrasi ({{ floatval($pengaturan['admin_persen']) }}%)</span>
                                    <span class="font-mono text-slate-300 font-semibold">Rp {{ number_format($rincian['potongan_biaya_admin'], 0, ',', '.') }}</span>
                                </div>
                                <div class="pt-3 mt-3 border-t border-white/10 flex justify-between items-center">
                                    <span class="text-[12px] font-bold text-slate-400">Total Potongan</span>
                                    <span class="font-mono font-bold text-red-400 text-sm">- Rp {{ number_format($rincian['total_potongan'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Pencairan -->
                        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-5 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/0 via-emerald-500/5 to-emerald-500/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                            <p class="text-emerald-400/80 text-[12px] font-bold tracking-widest uppercase mb-1">Pencairan Bersih (Diterima)</p>
                            <div class="text-3xl font-mono font-extrabold text-white">Rp {{ number_format($rincian['dana_diterima'], 0, ',', '.') }}</div>
                        </div>

                        <!-- Action -->
                        <div class="pt-6">
                            <label class="flex items-start gap-3 cursor-pointer group mb-6">
                                <input name="agreed" type="checkbox" required class="mt-1 w-5 h-5 rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-500/50 focus:ring-offset-slate-900 cursor-pointer transition-colors"/>
                                <span class="text-[12px] text-slate-400 font-medium leading-relaxed group-hover:text-slate-300 transition-colors">
                                    Saya telah membaca, memeriksa, dan memahami seluruh rincian simulasi di atas. Serta menyatakan bahwa data adalah benar.
                                </span>
                            </label>
                            
                            <div class="flex gap-3">
                                <button type="button" onclick="history.back()" class="h-14 w-14 shrink-0 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-all flex items-center justify-center group outline-none">
                                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                </button>
                                <button type="submit" class="flex-1 bg-white text-slate-900 hover:bg-slate-100 h-14 rounded-xl font-bold text-[15px] transition-all flex items-center justify-center gap-2 group active:scale-[0.98] outline-none shadow-xl shadow-white/10">
                                    Konfirmasi & Kirim
                                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <footer class="mt-20 text-center space-y-3">
        <p class="text-[12px] font-semibold text-slate-400 tracking-widest uppercase">
            © {{ date('Y') }} KOPERASI SIMPAN PINJAM PKPP PUPR RIAU
        </p>
        <div class="flex items-center justify-center gap-2 text-slate-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span class="text-[11px] font-bold tracking-widest uppercase">Secure Encrypted Portal</span>
        </div>
    </footer>
</main>

@endsection

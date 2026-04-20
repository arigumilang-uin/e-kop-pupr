@extends('layouts.guest')

@section('title', 'Simulasi Pinjaman')

@section('content')
<div class="w-full max-w-5xl py-6 px-4">
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-400 mb-4 shadow-lg shadow-blue-500/25">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Simulasi Pinjaman</h1>
        <p class="text-slate-400 text-sm mt-1">Koperasi Simpan Pinjam PKPP — Dinas PUPR Provinsi Riau</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Panel Input (2 kolom) --}}
        <div class="lg:col-span-2">
            <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6 shadow-2xl sticky top-6">
                <h2 class="text-base font-semibold text-white mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Masukkan Data
                </h2>

                <div class="space-y-5">
                    {{-- Nominal --}}
                    <div>
                        <label for="sim-nominal" class="block text-sm font-medium text-slate-300 mb-1.5">Nominal Pinjaman (Rp)</label>
                        <input type="number" id="sim-nominal" min="100000" step="100000" value="5000000"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10 text-white text-lg font-bold font-mono
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all">
                        <p class="text-xs text-slate-500 mt-1.5">Minimal Rp 100.000</p>
                    </div>

                    {{-- Tenor --}}
                    <div>
                        <label for="sim-tenor" class="block text-sm font-medium text-slate-300 mb-1.5">Tenor Angsuran (Bulan)</label>
                        <input type="range" id="sim-tenor" min="{{ $pengaturan['tenor_min'] }}" max="12" value="8"
                               class="w-full h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-500">
                        <div class="flex justify-between mt-1.5">
                            <span class="text-xs text-slate-500">{{ $pengaturan['tenor_min'] }} bulan</span>
                            <span id="tenor-display" class="text-sm font-bold text-blue-400">8 bulan</span>
                            <span class="text-xs text-slate-500">12 bulan</span>
                        </div>
                    </div>

                    {{-- Info Ketentuan --}}
                    <div class="p-4 rounded-xl bg-blue-500/5 border border-blue-500/10 space-y-2">
                        <p class="text-xs text-blue-300 font-semibold uppercase tracking-wider mb-2">Ketentuan Berlaku</p>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400">Bunga per Pinjaman</span>
                            <span class="text-white font-medium">{{ $pengaturan['bunga_persen'] }}% <span class="text-xs text-slate-500">(flat)</span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400">Potongan SWP</span>
                            <span class="text-white font-medium">{{ $pengaturan['swp_persen'] }}%</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400">Dana Resiko</span>
                            <span class="text-white font-medium">{{ $pengaturan['resiko_persen'] }}%</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400">Biaya Admin</span>
                            <span class="text-white font-medium">{{ $pengaturan['admin_persen'] }}%</span>
                        </div>
                        <div class="border-t border-blue-500/10 pt-2 mt-2 flex justify-between text-sm">
                            <span class="text-slate-300 font-medium">Total Potongan Dimuka</span>
                            <span class="text-amber-400 font-bold">{{ $pengaturan['swp_persen'] + $pengaturan['resiko_persen'] + $pengaturan['admin_persen'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Hasil (3 kolom) --}}
        <div class="lg:col-span-3 space-y-5">
            {{-- Ringkasan Utama --}}
            <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6 shadow-2xl">
                <h2 class="text-base font-semibold text-white mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Hasil Simulasi
                </h2>

                {{-- Angsuran Per Bulan (Hero Card) --}}
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-5 mb-5 text-center shadow-lg shadow-blue-600/20">
                    <p class="text-blue-200 text-sm mb-1">Angsuran Per Bulan</p>
                    <p id="res-angsuran-total" class="text-3xl md:text-4xl font-bold text-white font-mono">Rp 0</p>
                    <div class="flex justify-center gap-6 mt-3 text-xs">
                        <span class="text-blue-200">Pokok: <strong id="res-angsuran-pokok" class="text-white">Rp 0</strong></span>
                        <span class="text-blue-200">Bunga: <strong id="res-angsuran-bunga" class="text-white">Rp 0</strong></span>
                    </div>
                </div>

                {{-- Grid Rincian --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-white/5">
                        <p class="text-xs text-slate-500 mb-1">Total Bunga</p>
                        <p id="res-total-bunga" class="text-lg font-bold text-white font-mono">Rp 0</p>
                        <p class="text-xs text-slate-500 mt-1">(<span id="res-bunga-persen">15</span>% dari pinjaman)</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-white/5">
                        <p class="text-xs text-slate-500 mb-1">Total Harus Dikembalikan</p>
                        <p id="res-total-bayar" class="text-lg font-bold text-white font-mono">Rp 0</p>
                        <p class="text-xs text-slate-500 mt-1">Pokok + Bunga selama <span id="res-tenor-display">0</span> bulan</p>
                    </div>
                </div>
            </div>

            {{-- Rincian Potongan --}}
            <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6 shadow-2xl">
                <h2 class="text-base font-semibold text-white mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Potongan di Muka
                </h2>

                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm py-2 border-b border-white/5">
                        <span class="text-slate-400">Simpanan Wajib Pinjam (SWP) <span class="text-xs text-slate-500">{{ $pengaturan['swp_persen'] }}%</span></span>
                        <span id="res-potongan-swp" class="text-white font-mono font-medium">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm py-2 border-b border-white/5">
                        <span class="text-slate-400">Dana Resiko <span class="text-xs text-slate-500">{{ $pengaturan['resiko_persen'] }}%</span></span>
                        <span id="res-potongan-resiko" class="text-white font-mono font-medium">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm py-2 border-b border-white/5">
                        <span class="text-slate-400">Biaya Admin <span class="text-xs text-slate-500">{{ $pengaturan['admin_persen'] }}%</span></span>
                        <span id="res-potongan-admin" class="text-white font-mono font-medium">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm py-2 border-b border-white/10">
                        <span class="text-amber-400 font-medium">Total Potongan</span>
                        <span id="res-total-potongan" class="text-amber-400 font-mono font-bold">- Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm py-3 bg-emerald-500/5 rounded-xl px-4 -mx-1">
                        <span class="text-emerald-400 font-semibold">Dana Bersih Diterima</span>
                        <span id="res-dana-diterima" class="text-emerald-400 text-lg font-mono font-bold">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- Tabel Angsuran --}}
            <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
                <div class="p-6 pb-4">
                    <h2 class="text-base font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Jadwal Angsuran
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-800/50 border-y border-white/5">
                                <th class="px-5 py-3 text-xs text-slate-400 font-semibold uppercase">Ke</th>
                                <th class="px-5 py-3 text-xs text-slate-400 font-semibold uppercase">Pokok</th>
                                <th class="px-5 py-3 text-xs text-slate-400 font-semibold uppercase">Bunga</th>
                                <th class="px-5 py-3 text-xs text-slate-400 font-semibold uppercase">Total</th>
                                <th class="px-5 py-3 text-xs text-slate-400 font-semibold uppercase text-right">Sisa Pokok</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-angsuran" class="divide-y divide-white/5">
                            {{-- Diisi via JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="text-center mt-8">
        <p class="text-slate-500 text-xs">
            Hasil simulasi ini bersifat estimasi berdasarkan pengaturan yang berlaku.<br>
            Nominal aktual dapat berbeda saat pengajuan resmi dilakukan.
        </p>
        <p class="text-slate-600 text-xs mt-2">&copy; {{ date('Y') }} Koperasi PKPP — Dinas PUPR Provinsi Riau</p>
    </div>
</div>

<script>
    // === Konfigurasi dari Server (dirender sekali saat page load) ===
    const CONFIG = {
        bungaPersen: {{ $pengaturan['bunga_persen'] }},
        swpPersen: {{ $pengaturan['swp_persen'] }},
        resikoPersen: {{ $pengaturan['resiko_persen'] }},
        adminPersen: {{ $pengaturan['admin_persen'] }},
    };

    // === DOM References ===
    const elNominal = document.getElementById('sim-nominal');
    const elTenor = document.getElementById('sim-tenor');
    const elTenorDisplay = document.getElementById('tenor-display');

    // Hasil
    const elAngsuranTotal = document.getElementById('res-angsuran-total');
    const elAngsuranPokok = document.getElementById('res-angsuran-pokok');
    const elAngsuranBunga = document.getElementById('res-angsuran-bunga');
    const elTotalBunga = document.getElementById('res-total-bunga');
    const elBungaPersen = document.getElementById('res-bunga-persen');
    const elTotalBayar = document.getElementById('res-total-bayar');
    const elTenorResult = document.getElementById('res-tenor-display');
    const elPotonganSwp = document.getElementById('res-potongan-swp');
    const elPotonganResiko = document.getElementById('res-potongan-resiko');
    const elPotonganAdmin = document.getElementById('res-potongan-admin');
    const elTotalPotongan = document.getElementById('res-total-potongan');
    const elDanaDiterima = document.getElementById('res-dana-diterima');
    const elTabelAngsuran = document.getElementById('tabel-angsuran');

    // === Formatter ===
    function formatRp(val) {
        return 'Rp ' + Math.round(val).toLocaleString('id-ID');
    }

    // === Kalkulasi Client-Side (sama dengan PinjamanService::hitungPinjaman) ===
    function hitung() {
        const nominal = parseFloat(elNominal.value) || 0;
        const tenor = parseInt(elTenor.value) || 1;

        if (nominal < 100000 || tenor < 1) return;

        // Bunga
        const totalBunga = nominal * (CONFIG.bungaPersen / 100);
        const angsuranPokok = nominal / tenor;
        const angsuranBunga = totalBunga / tenor;
        const totalAngsuran = angsuranPokok + angsuranBunga;
        const totalBayar = nominal + totalBunga;

        // Potongan
        const potonganSwp = nominal * (CONFIG.swpPersen / 100);
        const potonganResiko = nominal * (CONFIG.resikoPersen / 100);
        const potonganAdmin = nominal * (CONFIG.adminPersen / 100);
        const totalPotongan = potonganSwp + potonganResiko + potonganAdmin;
        const danaDiterima = nominal - totalPotongan;

        // Update UI
        elTenorDisplay.textContent = tenor + ' bulan';
        elAngsuranTotal.textContent = formatRp(totalAngsuran);
        elAngsuranPokok.textContent = formatRp(angsuranPokok);
        elAngsuranBunga.textContent = formatRp(angsuranBunga);
        elTotalBunga.textContent = formatRp(totalBunga);
        elBungaPersen.textContent = CONFIG.bungaPersen;
        elTotalBayar.textContent = formatRp(totalBayar);
        elTenorResult.textContent = tenor;
        elPotonganSwp.textContent = formatRp(potonganSwp);
        elPotonganResiko.textContent = formatRp(potonganResiko);
        elPotonganAdmin.textContent = formatRp(potonganAdmin);
        elTotalPotongan.textContent = '- ' + formatRp(totalPotongan);
        elDanaDiterima.textContent = formatRp(danaDiterima);

        // Tabel Angsuran
        let rows = '';
        let sisaPokok = nominal;
        for (let i = 1; i <= tenor; i++) {
            sisaPokok -= angsuranPokok;
            if (sisaPokok < 0) sisaPokok = 0;
            const isLast = i === tenor;
            rows += `
                <tr class="hover:bg-white/[0.02] transition-colors ${isLast ? 'bg-emerald-500/5' : ''}">
                    <td class="px-5 py-2.5 text-slate-400 font-medium">${i}</td>
                    <td class="px-5 py-2.5 text-slate-300 font-mono">${formatRp(angsuranPokok)}</td>
                    <td class="px-5 py-2.5 text-slate-300 font-mono">${formatRp(angsuranBunga)}</td>
                    <td class="px-5 py-2.5 text-white font-mono font-medium">${formatRp(totalAngsuran)}</td>
                    <td class="px-5 py-2.5 text-right font-mono ${isLast ? 'text-emerald-400 font-bold' : 'text-slate-400'}">${formatRp(sisaPokok)}</td>
                </tr>`;
        }
        elTabelAngsuran.innerHTML = rows;
    }

    // === Event Listeners ===
    elNominal.addEventListener('input', hitung);
    elTenor.addEventListener('input', hitung);

    // Jalankan saat pertama kali dimuat
    hitung();
</script>
@endsection

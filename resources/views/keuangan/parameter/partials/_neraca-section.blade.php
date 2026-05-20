<div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden mb-8">
    <table class="w-full text-left whitespace-nowrap">
        <thead class="bg-stone-50 border-b border-stone-200">
            <tr>
                <th colspan="3" class="px-6 py-4 font-black text-stone-800 uppercase tracking-widest text-[13px] border-b border-stone-200">
                    {{ $title }}
                </th>
            </tr>
            <tr class="bg-[#043d2e]/5 border-b border-[#043d2e]/10">
                <th class="px-6 py-3 font-black text-[#043d2e] uppercase text-[11px] tracking-widest">
                    TOTAL {{ $title }}
                </th>
                <th class="px-6 py-3 text-right font-mono font-black text-[#043d2e] text-sm">
                    {{ format_rupiah($neracaReport['sections'][$posisi]['total'] ?? 0) }}
                </th>
                <th class="px-6 py-3"></th>
            </tr>
            <tr class="bg-white border-b border-stone-200">
                <th class="px-6 py-2.5 font-bold text-stone-500 uppercase tracking-wider text-[10px] w-1/2">Nama Akun & Konfigurasi</th>
                <th class="px-6 py-2.5 font-bold text-stone-500 uppercase tracking-wider text-[10px] text-right">Nilai Pembukuan</th>
                <th class="px-6 py-2.5 font-bold text-stone-500 uppercase tracking-wider text-[10px] text-center w-24">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @foreach($items as $item)
                @php
                    $real_nominal = 0;
                    if (isset($neracaReport['sections'][$posisi]['items'])) {
                        $f = collect($neracaReport['sections'][$posisi]['items'])->firstWhere('nama', $item->nama);
                        if ($f) $real_nominal = $f['nominal'];
                    }
                @endphp
                <tr class="group hover:bg-stone-50/80 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="font-bold text-stone-800 text-[14px]">{{ $item->nama }}</h4>
                                @if($item->is_pengurang)
                                    <span class="px-2 py-0.5 bg-red-50 text-red-600 border border-red-200/60 text-[9px] font-black uppercase rounded-md tracking-widest" title="Akun ini bersifat mengurangi subtotal">Kontra-Akun</span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                @if($item->isManual())
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-stone-500 bg-stone-100 px-2 py-0.5 rounded border border-stone-200">Manual Statis</span>
                                @else
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded">Live Kalkulasi</span>
                                    <span class="text-[10px] font-mono font-bold tracking-wider text-stone-500">{{ $item->kode_otomatis }}</span>
                                @endif
                                
                                @if($item->isOtomatis() && $item->kode_otomatis === 'PIUTANG_EKSTERNAL')
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-stone-500 border border-stone-200 px-2 py-0.5 rounded">Filter: {{ $item->konfigurasi['kategori_peminjam'] ? ucfirst(str_replace('_', ' ', $item->konfigurasi['kategori_peminjam'])) : 'Global' }} {{ $item->konfigurasi['tahun_pinjam'] ? '('.$item->konfigurasi['tahun_pinjam'].')' : '' }}</span>
                                @endif
                                @if($item->isOtomatis() && $item->kode_otomatis === 'SIMPANAN_ANGGOTA_KUSTOM')
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-stone-500 border border-stone-200 px-2 py-0.5 rounded">Tarik: {{ $item->konfigurasi['jenis_simpanan_kode'] ?? 'Kosong!' }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($item->isOtomatis())
                            <p class="font-mono font-black text-emerald-700 text-[15px] tabular-nums tracking-tight">{{ format_rupiah($real_nominal) }}</p>
                            @if($item->nominal_manual > 0)
                                <p class="text-[9px] text-emerald-600 font-bold uppercase tracking-widest mt-0.5 flex items-center justify-end gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Termasuk Saldo Awal: {{ format_rupiah($item->nominal_manual) }}</p>
                            @else
                                <p class="text-[9px] text-emerald-600 font-bold uppercase tracking-widest mt-0.5 flex items-center justify-end gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Dihitung Sistem</p>
                            @endif
                        @else
                            <p class="font-mono font-black text-stone-800 text-[15px] tabular-nums tracking-tight">{{ format_rupiah($item->nominal_manual) }}</p>
                            <p class="text-[9px] text-stone-400 font-bold uppercase tracking-widest mt-0.5">Saldo Statis</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 border-l border-stone-100">
                        <div class="flex items-center justify-center gap-1.5">
                            <button @click="$dispatch('open-modal', 'modal-edit-neraca-{{ $item->id }}')" class="w-8 h-8 rounded-lg bg-white border border-stone-200 text-stone-500 hover:bg-[#043d2e] hover:border-[#043d2e] hover:text-white transition-all shadow-sm flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <form action="{{ route('keuangan.parameter.neraca.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Peringatan: Menghapus komponen ini akan menyebabkan perlindungan nilai Neraca berubah. Lanjutkan?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-white border border-stone-200 text-stone-400 hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-all shadow-sm flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- MODALS --}}
    @foreach($items as $item)
        <x-modal name="modal-edit-neraca-{{ $item->id }}" title="Edit Parameter Neraca" maxWidth="md">
            <div class="p-6">
                <form action="{{ route('keuangan.parameter.neraca.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nama Tampilan</label>
                            <input type="text" name="nama" value="{{ $item->nama }}" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] py-2.5 bg-stone-50 text-sm" required>
                        </div>
                        
                        @if($item->isManual() || in_array($item->kode_otomatis, ['SALDO_BANK_BRK', 'SALDO_KAS_TUNAI', 'SIMPANAN_LIVE_POKOK', 'SIMPANAN_LIVE_WAJIB', 'SIMPANAN_LIVE_SWP']))
                        <div>
                            <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">{{ $item->isManual() ? 'Nominal Manual' : 'Saldo Awal (Opening Balance)' }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-stone-400 font-bold">Rp</span>
                                </div>
                                <input type="number" name="nominal_manual" value="{{ (int) $item->nominal_manual }}" class="w-full pl-11 border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] font-mono font-bold py-2.5 bg-stone-50" min="0" step="1">
                            </div>
                            @if($item->isOtomatis())
                                <p class="text-[10px] text-stone-400 mt-1 uppercase">Ini adalah nominal Saldo Awal. Mutasi live tahun {{ request('tahun', now()->year) }} akan diproses otomatis.</p>
                            @endif
                        </div>
                        @endif

                        @if($item->isOtomatis() && $item->kode_otomatis === 'PIUTANG_EKSTERNAL')
                        <div class="mt-4 p-4 bg-emerald-50 border border-emerald-100 rounded-xl space-y-4">
                            <p class="text-[11px] font-black uppercase text-emerald-600 tracking-wider flex items-center gap-1.5 border-b border-emerald-100 pb-2"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Konfigurasi Piutang Dinamis</p>
                            
                            <div>
                                <label class="block text-[10px] sm:text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Kategori Peminjam</label>
                                <select name="kategori_peminjam" class="w-full border-emerald-200 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-[13px] bg-white py-2 font-bold text-[#043d2e]">
                                    <option value="">Semua Kategori (Global)</option>
                                    <option value="anggota" {{ ($item->konfigurasi['kategori_peminjam'] ?? '') == 'anggota' ? 'selected' : '' }}>Anggota (Periode Lalu)</option>
                                    <option value="pengurus" {{ ($item->konfigurasi['kategori_peminjam'] ?? '') == 'pengurus' ? 'selected' : '' }}>Pengurus</option>
                                    <option value="pihak_ketiga" {{ ($item->konfigurasi['kategori_peminjam'] ?? '') == 'pihak_ketiga' ? 'selected' : '' }}>Pihak Ketiga</option>
                                    <option value="instansi" {{ ($item->konfigurasi['kategori_peminjam'] ?? '') == 'instansi' ? 'selected' : '' }}>Instansi</option>
                                </select>
                                <p class="text-[9px] font-bold text-stone-400 mt-1 uppercase">Sistem akan menyortir dan membedakan berdasar kategori ini.</p>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] sm:text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Tahun Pinjam / Alokasi</label>
                                <select name="tahun_pinjam" class="w-full border-emerald-200 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-[13px] bg-white py-2 font-bold text-[#043d2e]">
                                    <option value="">Semua Tahun (Digabung)</option>
                                    @for($i = now()->year; $i >= 2015; $i--)
                                        <option value="{{ $i }}" {{ ($item->konfigurasi['tahun_pinjam'] ?? '') == $i ? 'selected' : '' }}>Hanya Tahun {{ $i }}</option>
                                    @endfor
                                </select>
                                <p class="text-[9px] font-bold text-stone-400 mt-1 uppercase">Kosongkan jika ingin menggabungkan total seluruh tahun.</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="flex gap-3 justify-end mt-8 border-t border-stone-100 pt-5">
                        <button type="button" @click="show = false" class="px-5 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-700 hover:bg-stone-100 rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-[#043d2e] hover:bg-[#065a45] rounded-xl transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    @endforeach
</div>

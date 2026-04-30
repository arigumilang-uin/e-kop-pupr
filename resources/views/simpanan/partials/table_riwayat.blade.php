<div class="w-full overflow-x-auto lg:overflow-x-visible relative">
    <table class="w-full text-left text-sm text-stone-600 whitespace-nowrap">
        <thead class="sticky top-[168px] z-20 shadow-sm border-b border-stone-200 bg-stone-50">
            <tr>
                <th class="px-5 py-4 text-[11px] font-bold text-stone-500 uppercase tracking-wider">No. Referensi</th>
                <th class="px-5 py-4 text-[11px] font-bold text-stone-500 uppercase tracking-wider">Tgl Transaksi</th>
                <th class="px-5 py-4 text-[11px] font-bold text-stone-500 uppercase tracking-wider">Informasi Anggota</th>
                <th class="px-5 py-4 text-[11px] font-bold text-stone-500 uppercase tracking-wider text-center">Kategori</th>
                <th class="px-5 py-4 text-[11px] font-bold text-stone-500 uppercase tracking-wider text-right">Nominal Rp</th>
                <th class="px-5 py-4 text-[11px] font-bold text-stone-500 uppercase tracking-wider">Deskripsi Tambahan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100 bg-white">
            @forelse($simpanans as $s)
            <tr class="hover:bg-stone-50 transition-colors group">
                <td class="px-5 py-3">
                    <span class="font-mono text-[12px] font-bold text-[#043d2e] bg-[#043d2e]/5 px-2 py-1 rounded">{{ $s->no_referensi }}</span>
                </td>
                <td class="px-5 py-3">
                    <div class="font-medium text-stone-800">{{ $s->tanggal->translatedFormat('d M Y') }}</div>
                    <div class="text-[11px] text-stone-400 font-mono">{{ $s->created_at->format('H:i') }} WIB</div>
                </td>
                <td class="px-5 py-3">
                    <a href="{{ route('anggota.show', $s->anggota_id) }}" class="flex flex-col group/link w-fit">
                        <span class="font-bold text-stone-800 group-hover/link:text-[#043d2e] transition-colors">{{ $s->anggota->nama ?? '-' }}</span>
                        <span class="text-[11px] text-stone-500 font-mono tracking-wide mt-0.5">NIP. <x-nip-display :value="$s->anggota->nip ?? ''"/></span>
                    </a>
                </td>
                <td class="px-5 py-3 text-center">
                    @php
                        $jenisColors = ['POKOK' => 'blue', 'WAJIB' => 'indigo', 'SWP' => 'amber', 'SUKARELA' => 'emerald'];
                        $kode = $s->jenisSimpanan->kode ?? '';
                        $c = $jenisColors[$kode] ?? 'stone';
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-widest bg-{{ $c }}-50 text-{{ $c }}-700 border border-{{ $c }}-200">
                        {{ $s->jenisSimpanan->nama ?? '-' }}
                    </span>
                </td>
                <td class="px-5 py-3 text-right">
                    <span class="font-mono text-[14px] font-bold text-[#043d2e] block">{{ format_rupiah($s->nominal) }}</span>
                </td>
                <td class="px-5 py-3">
                    <span class="text-[13px] text-stone-600 font-medium max-w-[200px] truncate block" title="{{ $s->keterangan }}">{{ $s->keterangan ?? '-' }}</span>
                    @if($s->pencatat)
                        <span class="text-[10px] uppercase font-bold text-stone-400 mt-1 block">Oleh: {{ $s->pencatat->nama }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 rounded-2xl bg-stone-50 border border-stone-200 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-stone-800 font-bold mb-1">Riwayat Transaksi Kosong</h4>
                        <p class="text-sm text-stone-500">Tidak ada riwayat mutasi yang termuat dalam sistem saat ini.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($simpanans->hasPages())
<div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
    <div class="alpine-pagination" @click.prevent="if($event.target.tagName === 'A' || $event.target.closest('a')) { let link = $event.target.tagName === 'A' ? $event.target : $event.target.closest('a'); if(link.href) { $dispatch('riwayat-paginate', { url: link.href }) } }">
        {{ $simpanans->links() }}
    </div>
</div>
@endif

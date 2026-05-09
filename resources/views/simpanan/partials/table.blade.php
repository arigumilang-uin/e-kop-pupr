<x-table>
    <x-table.thead :sticky="true">
        <x-table.th>Informasi Anggota</x-table.th>
        <x-table.th class="text-right">Thn. 2025</x-table.th>
        <x-table.th class="text-right">Pokok</x-table.th>
        <x-table.th class="text-right">Wajib</x-table.th>
        <x-table.th class="text-right">SWP</x-table.th>
        <x-table.th class="text-right">Bonus SHU</x-table.th>
        <x-table.th class="text-right text-[#043d2e] bg-emerald-50/90 backdrop-blur-sm border-l border-stone-200/60">Total Simpanan</x-table.th>
    </x-table.thead>
    
    <x-table.tbody>
        @if(isset($grandTotals))
        <tr class="bg-stone-100 border-b-2 border-stone-200 shadow-sm divide-x divide-stone-200">
            <td class="px-5 py-2.5 font-bold text-[11px] uppercase tracking-widest text-stone-800">TOTAL SELURUHNYA</td>
            <td class="px-5 py-2.5 text-right"><span class="font-mono text-[13px] font-bold text-stone-800">{{ format_rupiah($grandTotals['sim2025'] ?? 0) }}</span></td>
            <td class="px-5 py-2.5 text-right"><span class="font-mono text-[13px] font-bold text-stone-800">{{ format_rupiah($grandTotals['pokok'] ?? 0) }}</span></td>
            <td class="px-5 py-2.5 text-right"><span class="font-mono text-[13px] font-bold text-stone-800">{{ format_rupiah($grandTotals['wajib'] ?? 0) }}</span></td>
            <td class="px-5 py-2.5 text-right"><span class="font-mono text-[13px] font-bold text-stone-800">{{ format_rupiah($grandTotals['swp'] ?? 0) }}</span></td>
            <td class="px-5 py-2.5 text-right"><span class="font-mono text-[13px] font-bold text-amber-600">{{ format_rupiah($grandTotals['bonus_shu'] ?? 0) }}</span></td>
            <td class="px-6 py-2.5 text-right bg-[#043d2e]/10">
                <span class="font-mono text-[14px] font-black text-[#043d2e]">{{ format_rupiah($grandTotal ?? 0) }}</span>
            </td>
        </tr>
        @endif

        @forelse($anggotas as $anggota)
            <x-table.tr>
                <x-table.td>
                    <div class="flex flex-col">
                        @can('simpanan.create')
                        <button type="button" @click="openModal({{ $anggota->id }}, '{{ addslashes($anggota->nama) }}', {{ $anggota->neto_pokok > 0 ? 'true' : 'false' }})" class="group font-bold text-stone-800 hover:text-[#043d2e] transition-colors flex items-center gap-2 w-fit text-left focus:outline-none" title="Catat Simpanan Manual">
                            {{ $anggota->nama }}
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded border border-stone-200 bg-stone-50 text-stone-400 group-hover:border-[#043d2e]/30 group-hover:bg-emerald-50 group-hover:text-[#043d2e] transition-all shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </span>
                        </button>
                        @else
                        <span class="font-bold text-stone-800">{{ $anggota->nama }}</span>
                        @endcan
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[11px] text-stone-500 font-mono font-medium tracking-wide">NIP. <x-nip-display :value="$anggota->nip" /></span>
                            @if($anggota->golongan_asn)
                                <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-[1px] rounded bg-{{ $anggota->golongan_asn->color() }}-50 text-{{ $anggota->golongan_asn->color() }}-600 border border-{{ $anggota->golongan_asn->color() }}-200">{{ $anggota->golongan_asn->label() }}</span>
                            @endif
                        </div>
                    </div>
                </x-table.td>
                <x-table.td class="text-right">
                    @if($anggota->neto_sim2025 > 0)
                    <span class="font-mono text-[13px] font-semibold text-stone-700 block ml-auto">{{ number_format($anggota->neto_sim2025, 0, ',', '.') }}</span>
                    @else
                    <span class="text-stone-300 font-mono text-[13px]">-</span>
                    @endif
                </x-table.td>
                <x-table.td class="text-right">
                    @if($anggota->neto_pokok > 0)
                    <span class="font-mono text-[13px] font-semibold text-stone-700 block ml-auto">{{ number_format($anggota->neto_pokok, 0, ',', '.') }}</span>
                    @else
                    <span class="text-stone-300 font-mono text-[13px]">-</span>
                    @endif
                </x-table.td>
                <x-table.td class="text-right">
                    @if($anggota->neto_wajib > 0)
                    <span class="font-mono text-[13px] font-semibold text-stone-700 block ml-auto">{{ number_format($anggota->neto_wajib, 0, ',', '.') }}</span>
                    @else
                    <span class="text-stone-300 font-mono text-[13px]">-</span>
                    @endif
                </x-table.td>
                <x-table.td class="text-right">
                    @if($anggota->neto_swp > 0)
                    <span class="font-mono text-[13px] font-semibold text-stone-700 block ml-auto">{{ number_format($anggota->neto_swp, 0, ',', '.') }}</span>
                    @else
                    <span class="text-stone-300 font-mono text-[13px]">-</span>
                    @endif
                </x-table.td>
                <x-table.td class="text-right">
                    @if($anggota->neto_bonus_shu > 0)
                    <span class="font-mono text-[13px] font-semibold text-amber-600 block ml-auto">{{ number_format($anggota->neto_bonus_shu, 0, ',', '.') }}</span>
                    @else
                    <span class="text-stone-300 font-mono text-[13px]">-</span>
                    @endif
                </x-table.td>
                <x-table.td class="text-right bg-emerald-50/30 border-l border-stone-200/60">
                    <span class="font-mono text-[14px] font-black text-[#043d2e] block ml-auto">{{ number_format($anggota->neto_total, 0, ',', '.') }}</span>
                </x-table.td>
            </x-table.tr>
        @empty
            <tr>
                <td colspan="7" class="px-5 py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 rounded-2xl bg-stone-50 border border-stone-200 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-stone-800 font-bold mb-1">Data Tidak Ditemukan</h4>
                        <p class="text-sm text-stone-500">Belum ada data simpanan yang tercatat atau filter tidak cocok.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-table.tbody>
</x-table>

@if($anggotas->hasPages())
<div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
    <div class="alpine-pagination" @click.prevent="if($event.target.tagName === 'A' || $event.target.closest('a')) { let link = $event.target.tagName === 'A' ? $event.target : $event.target.closest('a'); if(link.href) { $dispatch('simpanan-paginate', { url: link.href }) } }">
        {{ $anggotas->links() }}
    </div>
</div>
@endif

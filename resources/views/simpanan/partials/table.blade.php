<div class="w-full overflow-x-auto lg:overflow-x-visible">
    <table class="w-full text-left text-sm text-stone-600 whitespace-nowrap">
        <thead class="sticky top-[168px] z-20 shadow-sm border-b border-stone-200">
            <tr class="divide-x divide-stone-200">
                <th scope="col" class="px-5 py-4 font-bold text-[11px] uppercase tracking-wider bg-stone-50">Informasi Anggota</th>
                <th scope="col" class="px-5 py-4 font-bold text-[11px] uppercase tracking-wider text-right bg-stone-50">Thn. 2025</th>
                <th scope="col" class="px-5 py-4 font-bold text-[11px] uppercase tracking-wider text-right bg-stone-50">Pokok</th>
                <th scope="col" class="px-5 py-4 font-bold text-[11px] uppercase tracking-wider text-right bg-stone-50">Wajib</th>
                <th scope="col" class="px-5 py-4 font-bold text-[11px] uppercase tracking-wider text-right bg-stone-50">SWP</th>
                <th scope="col" class="px-5 py-4 font-bold text-[11px] uppercase tracking-wider text-right bg-stone-50">Bonus SHU</th>
                <th scope="col" class="px-6 py-4 font-bold text-[11px] uppercase tracking-wider text-right text-[#043d2e] bg-emerald-50/90 backdrop-blur-sm border-l border-stone-200/60">Total Simpanan</th>
            </tr>
            @if(isset($grandTotals))
            <tr class="bg-stone-100 border-b-2 border-stone-200 shadow-sm divide-x divide-stone-200">
                <td class="px-5 py-2.5 font-bold text-[11px] uppercase tracking-widest text-stone-800">TOTAL</td>
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
        </thead>
        <tbody class="divide-y divide-stone-100 bg-white">
            @forelse($anggotas as $anggota)
            <tr class="hover:bg-stone-50 transition-colors group divide-x divide-stone-100">
                <td class="px-5 py-3">
                    <div class="flex flex-col">
                        <button type="button" @click="openModal({{ $anggota->id }}, '{{ addslashes($anggota->nama) }}')" class="font-bold text-stone-800 hover:text-[#043d2e] transition-colors flex items-center gap-1.5 w-fit text-left focus:outline-none" title="Catat Simpanan Manual">
                            {{ $anggota->nama }}
                            <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </button>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[11px] text-stone-500 font-mono font-medium tracking-wide">NIP. <x-nip-display :value="$anggota->nip" /></span>
                            @if($anggota->golongan_asn)
                                <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-[1px] rounded bg-{{ $anggota->golongan_asn->color() }}-50 text-{{ $anggota->golongan_asn->color() }}-600 border border-{{ $anggota->golongan_asn->color() }}-200">{{ $anggota->golongan_asn->label() }}</span>
                            @endif
                        </div>
                    </div>
                </td>
                
                {{-- Simpanan 2025 --}}
                <td class="px-5 py-3 text-right">
                    @if($anggota->neto_sim2025 > 0)
                    <span class="font-mono text-[13px] font-semibold text-stone-700 block ml-auto">{{ number_format($anggota->neto_sim2025, 0, ',', '.') }}</span>
                    @else
                    <span class="text-stone-300 font-mono text-[13px]">-</span>
                    @endif
                </td>

                {{-- Pokok --}}
                <td class="px-5 py-3 text-right">
                    @if($anggota->neto_pokok > 0)
                    <span class="font-mono text-[13px] font-semibold text-stone-700 block ml-auto">{{ number_format($anggota->neto_pokok, 0, ',', '.') }}</span>
                    @else
                    <span class="text-stone-300 font-mono text-[13px]">-</span>
                    @endif
                </td>
                
                {{-- Wajib --}}
                <td class="px-5 py-3 text-right">
                    @if($anggota->neto_wajib > 0)
                    <span class="font-mono text-[13px] font-semibold text-stone-700 block ml-auto">{{ number_format($anggota->neto_wajib, 0, ',', '.') }}</span>
                    @else
                    <span class="text-stone-300 font-mono text-[13px]">-</span>
                    @endif
                </td>
                
                {{-- SWP --}}
                <td class="px-5 py-3 text-right">
                    @if($anggota->neto_swp > 0)
                    <span class="font-mono text-[13px] font-semibold text-stone-700 block ml-auto">{{ number_format($anggota->neto_swp, 0, ',', '.') }}</span>
                    @else
                    <span class="text-stone-300 font-mono text-[13px]">-</span>
                    @endif
                </td>

                {{-- Bonus SHU --}}
                <td class="px-5 py-3 text-right">
                    @if($anggota->neto_bonus_shu > 0)
                    <span class="font-mono text-[13px] font-semibold text-amber-600 block ml-auto">{{ number_format($anggota->neto_bonus_shu, 0, ',', '.') }}</span>
                    @else
                    <span class="text-stone-300 font-mono text-[13px]">-</span>
                    @endif
                </td>
                
                <td class="px-6 py-3 text-right bg-[#043d2e]/5">
                    <span class="font-mono text-[14px] font-black text-[#043d2e] block ml-auto">{{ number_format($anggota->neto_total, 0, ',', '.') }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 rounded-2xl bg-stone-50 border border-stone-200 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-stone-800 font-bold mb-1">Data Tidak Ditemukan</h4>
                        <p class="text-sm text-stone-500">Pencarian atau saringan Anda tidak cocok dengan tabungan anggota manapun.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($anggotas->hasPages())
<div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
    <div class="alpine-pagination" @click.prevent="if($event.target.tagName === 'A' || $event.target.closest('a')) { let link = $event.target.tagName === 'A' ? $event.target : $event.target.closest('a'); if(link.href) { $dispatch('simpanan-paginate', { url: link.href }) } }">
        {{ $anggotas->links() }}
    </div>
</div>
@endif

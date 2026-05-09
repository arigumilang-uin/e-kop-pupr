<x-table>
    <x-table.thead :sticky="true" class="top-[168px]">
        <x-table.th>Informasi Pegawai</x-table.th>
        <x-table.th>Bidang</x-table.th>
        <x-table.th class="text-center">Status</x-table.th>
        @canany(['anggota.profile', 'anggota.edit', 'anggota.keluar', 'anggota.reaktivasi'])
        <x-table.th class="text-right">Aksi</x-table.th>
        @endcanany
    </x-table.thead>
    
    <x-table.tbody>
        @forelse($anggotas as $anggota)
        <x-table.tr>
            <x-table.td>
                <div class="flex flex-col">
                    @can('anggota.profile')
                    <a href="{{ route('anggota.show', $anggota->id) }}" class="font-bold text-stone-800 hover:text-[#043d2e] transition-colors flex items-center gap-1.5" title="Lihat Profil Anggota">
                        {{ $anggota->nama }}
                        <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @else
                    <span class="font-bold text-stone-800">{{ $anggota->nama }}</span>
                    @endcan
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[11px] text-stone-500 font-mono font-medium tracking-wide">NIP. <x-nip-display :value="$anggota->nip" /></span>
                        @if($anggota->golongan_asn)
                            <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-{{ $anggota->golongan_asn->color() }}-50 text-{{ $anggota->golongan_asn->color() }}-600 border border-{{ $anggota->golongan_asn->color() }}-200">{{ $anggota->golongan_asn->label() }}</span>
                        @endif
                    </div>
                </div>
            </x-table.td>
            <x-table.td>
                <span class="text-stone-700 font-medium text-sm">{{ $anggota->bidang ? $anggota->bidang->nama_bidang : '-' }}</span>
            </x-table.td>
            <x-table.td class="text-center">
                @if($anggota->status->value === 'aktif')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold uppercase tracking-wider mx-auto">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-stone-100 text-stone-600 border border-stone-300 text-xs font-bold uppercase tracking-wider mx-auto">
                    Non-Aktif
                </span>
                @endif
            </x-table.td>
            @canany(['anggota.profile', 'anggota.edit', 'anggota.keluar', 'anggota.reaktivasi'])
            <x-table.td class="text-right">
                <div class="flex items-center justify-end gap-1">
                    <x-action-dropdown>
                        @can('anggota.profile')
                        <x-action-dropdown-item href="{{ route('anggota.show', $anggota->id) }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'>
                            Lihat Profil
                        </x-action-dropdown-item>
                        @endcan
                        
                        @can('anggota.edit')
                        <x-action-dropdown-item 
                            type="button" 
                            @click="openEditModal({{ json_encode([
                                'id' => $anggota->id,
                                'nip' => $anggota->nip,
                                'nama' => $anggota->nama,
                                'golongan_asn' => $anggota->golongan_asn?->value,
                                'bidang_id' => $anggota->bidang_id,
                                'no_hp' => $anggota->no_hp,
                            ]) }})"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'>
                            Ubah Data
                        </x-action-dropdown-item>
                        @endcan
                        
                        @if($anggota->status->value === 'aktif')
                            @can('anggota.keluar')
                            <x-action-dropdown-item href="{{ route('anggota.keluar', $anggota->id) }}" color="red" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>'>
                                Proses Keluar
                            </x-action-dropdown-item>
                            @endcan
                        @else
                            @can('anggota.reaktivasi')
                            <x-action-dropdown-item href="{{ route('anggota.reaktivasi', $anggota->id) }}" color="emerald" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'>
                                Aktifkan Kembali
                            </x-action-dropdown-item>
                            @endcan
                        @endif
                    </x-action-dropdown>
                </div>
            </x-table.td>
            @endcanany
        </x-table.tr>
        @empty
        <tr>
            <td colspan="{{ auth()->user()->canAny(['anggota.profile', 'anggota.edit', 'anggota.keluar', 'anggota.reaktivasi']) ? 4 : 3 }}" class="px-6 py-16 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-2xl bg-stone-50 border border-stone-200 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-stone-800 font-bold mb-1">Data Tidak Ditemukan</h4>
                    <p class="text-sm text-stone-500">Pencarian atau filter Anda tidak cocok dengan anggota manapun.</p>
                </div>
            </td>
        </tr>
        @endforelse
    </x-table.tbody>
</x-table>

@if($anggotas->hasPages())
<div class="px-6 py-4 border-t border-stone-200 bg-stone-50/30">
    <div class="alpine-pagination" @click.prevent="if($event.target.tagName === 'A' || $event.target.closest('a')) { let link = $event.target.tagName === 'A' ? $event.target : $event.target.closest('a'); if(link.href) { $dispatch('anggota-paginate', { url: link.href }) } }">
        {{ $anggotas->links() }}
    </div>
</div>
@endif

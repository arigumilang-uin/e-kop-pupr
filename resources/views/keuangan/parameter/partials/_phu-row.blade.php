<div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group hover:bg-stone-50/50 transition-colors">
    <div class="flex-grow">
        <div class="flex items-center gap-3 mb-1">
            <h4 class="font-bold text-stone-800">{{ $item->nama }}</h4>
            @if($item->isManual())
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-black uppercase rounded-md tracking-wider">Manual</span>
            @else
                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-black uppercase rounded-md tracking-wider">Otomatis / Live</span>
            @endif
        </div>
        @if($item->isOtomatis())
            <p class="text-xs font-mono text-stone-500">Resolver: <span class="text-purple-600 font-bold">{{ $item->kode_otomatis }}</span></p>
        @endif
    </div>

    <div class="flex items-center gap-4 sm:min-w-[300px] justify-between sm:justify-end">
        <div class="text-right">
            @if($item->isManual() || $item->nominal_manual > 0)
                <p class="font-mono font-bold text-stone-800 text-base">{{ format_rupiah($item->nominal_manual) }}</p>
            @else
                <p class="text-sm font-medium text-stone-400 italic">Dihitung otomatis</p>
            @endif
        </div>

        <div>
            <button @click="$dispatch('open-modal', 'modal-edit-phu-{{ $item->id }}')" class="w-8 h-8 rounded-lg bg-stone-100 text-stone-600 flex items-center justify-center hover:bg-[#043d2e] hover:text-white transition-colors" title="Edit Parameter">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>

            <form action="{{ route('keuangan.parameter.phu.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus parameter ini? Perhitungan SHU untuk tahun ini akan ikut berubah.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-8 h-8 rounded-lg bg-stone-100 text-stone-600 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors" title="Hapus Parameter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>

            <x-modal name="modal-edit-phu-{{ $item->id }}" title="Edit Parameter PHU" maxWidth="md">
                <div class="p-6">
                    <form action="{{ route('keuangan.parameter.phu.update', $item) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nama Pos {{ ucfirst($item->tipe) }}</label>
                                <input type="text" name="nama" value="{{ $item->nama }}" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] py-2.5 bg-stone-50 text-sm" required>
                            </div>
                            
                            @if($item->isManual())
                            <div>
                                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nominal Manual</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-stone-400 font-bold">Rp</span>
                                    </div>
                                    <input type="number" name="nominal_manual" value="{{ (int) $item->nominal_manual }}" class="w-full pl-11 border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] font-mono font-bold py-2.5 bg-stone-50" min="0" step="1">
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
        </div>
    </div>
</div>

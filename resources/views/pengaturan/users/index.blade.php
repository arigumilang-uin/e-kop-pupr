@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('subtitle', 'Akun pengurus dan pimpinan yang dapat mengakses sistem')

@section('actions')
    <button onclick="document.getElementById('modal-tambah-user').classList.remove('hidden')" class="py-2.5 px-4 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Tambah Pengguna</span>
    </button>
@endsection

@section('content')
<div>

    {{-- Filter & Table Container --}}
    <div x-data="userFilter()" class="flex flex-col gap-5">
        <x-filter-bar searchPlaceholder="Cari nama, username, atau NIP..." x-model="q">
            <x-slot name="indicator">
                <template x-if="activeFiltersCount > 0">
                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full shadow-sm"></span>
                </template>
            </x-slot>

            <x-slot name="filters">
                {{-- Role --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Role</label>
                    <select x-model="role" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                        <option value="">Semua Role</option>
                        <option value="admin">Admin/Pengurus</option>
                        <option value="pimpinan">Pimpinan/Kepala</option>
                    </select>
                </div>

                {{-- Reset --}}
                <button type="button" @click="resetFilter()" x-show="activeFiltersCount > 0" class="mt-2 w-full py-2.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-sm font-bold transition-colors border border-red-200">
                    Reset Filter
                </button>
            </x-slot>
        </x-filter-bar>

        <div id="table-content-container" class="relative">
            <x-table>
                <x-table.thead>
                    <tr>
                        <x-table.th>Nama</x-table.th>
                        <x-table.th>Username</x-table.th>
                        <x-table.th>Role</x-table.th>
                        <x-table.th>NIP</x-table.th>
                        <x-table.th>Login Terakhir</x-table.th>
                        <x-table.th class="text-right">Aksi</x-table.th>
                    </tr>
                </x-table.thead>
                <x-table.tbody>
                    @forelse($users as $u)
                    <x-table.tr class="hover:bg-stone-50/50 transition-colors">
                        <x-table.td class="align-top">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold text-white shrink-0 shadow-sm {{ $u->role->value === 'admin' ? 'bg-[#043d2e]' : 'bg-amber-600' }}">
                                    {{ strtoupper(substr($u->nama, 0, 1)) }}
                                </div>
                                <span class="font-bold text-stone-800 text-[13px]">{{ $u->nama }}</span>
                            </div>
                        </x-table.td>
                        <x-table.td class="align-top">
                            <span class="font-mono text-[13px] text-stone-600">{{ $u->username }}</span>
                        </x-table.td>
                        <x-table.td class="align-top">
                            @if($u->role->value === 'admin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-[#043d2e]/10 text-[#043d2e] border border-[#043d2e]/20">Admin/Pengurus</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-amber-100 text-amber-700 border border-amber-200">Pimpinan/Kepala</span>
                            @endif
                        </x-table.td>
                        <x-table.td class="align-top">
                            <span class="font-mono text-[13px] text-stone-700">{{ $u->nip }}</span>
                        </x-table.td>
                        <x-table.td class="align-top">
                            @if($u->last_login_at)
                                <span class="text-[12px] text-stone-500">{{ $u->last_login_at->diffForHumans() }}</span>
                            @else
                                <span class="text-[12px] text-stone-400 italic">Belum pernah</span>
                            @endif
                        </x-table.td>
                        <x-table.td class="whitespace-nowrap text-right align-top">
                            <x-action-dropdown>
                                <x-action-dropdown-item href="{{ route('users.edit', $u->id) }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'>
                                    Edit
                                </x-action-dropdown-item>
                                @if($u->id !== auth()->id())
                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-action-dropdown-item type="button" color="red" onclick="this.closest('form').submit()" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>'>
                                        Hapus
                                    </x-action-dropdown-item>
                                </form>
                                @endif
                            </x-action-dropdown>
                        </x-table.td>
                    </x-table.tr>
                    @empty
                    <x-table.tr>
                        <x-table.td colspan="6" class="px-6 py-12 text-center text-stone-500 font-medium text-sm">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Belum ada data pengguna.
                            </div>
                        </x-table.td>
                    </x-table.tr>
                    @endforelse
                </x-table.tbody>
            </x-table>

            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
                {{ $users->links() }}
            </div>
            @endif

            {{-- Loading Overlay --}}
            <div x-show="loading" x-transition.opacity class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-20">
                <div class="flex items-center gap-3 px-5 py-3 bg-white rounded-2xl shadow-xl border border-stone-200">
                    <svg class="animate-spin h-5 w-5 text-[#043d2e]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-bold text-stone-700 text-sm">Memuat data...</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah User --}}
<div id="modal-tambah-user" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity backdrop-blur-sm bg-stone-900/60" aria-hidden="true" onclick="document.getElementById('modal-tambah-user').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-stone-100 relative z-10">
            <div class="bg-gradient-to-r from-[#043d2e] to-[#032e22] px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Tambah Pengguna Baru
                </h3>
                <button type="button" class="text-white/70 hover:text-white transition-colors" onclick="document.getElementById('modal-tambah-user').classList.add('hidden')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="px-6 py-5 bg-white space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">NIP Anggota</label>
                            <input type="text" name="nip" required value="{{ old('nip') }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all font-mono" placeholder="Masukkan NIP anggota koperasi">
                            <p class="text-[11px] text-stone-400 mt-1">Nama pengguna otomatis diambil dari data anggota berdasarkan NIP.</p>
                            @error('nip') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Username</label>
                            <input type="text" name="username" required value="{{ old('username') }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all font-mono" placeholder="contoh: afauzi">
                            @error('username') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Role</label>
                            <select name="role" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin/Pengurus</option>
                                <option value="pimpinan" {{ old('role') === 'pimpinan' ? 'selected' : '' }}>Pimpinan/Kepala</option>
                            </select>
                            @error('role') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Password</label>
                            <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all" placeholder="Min. 6 karakter">
                            @error('password') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all" placeholder="Ulangi password">
                        </div>
                    </div>
                </div>
                <div class="bg-stone-50 px-6 py-4 border-t border-stone-200 flex justify-end gap-3">
                    <button type="button" class="px-5 py-2.5 rounded-xl border border-stone-300 bg-white text-sm font-bold text-stone-700 hover:bg-stone-50 transition-colors shadow-sm" onclick="document.getElementById('modal-tambah-user').classList.add('hidden')">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl border border-transparent shadow-sm bg-[#043d2e] hover:bg-[#043d2e]/90 text-sm font-bold text-white transition-colors">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userFilter', () => ({
            q: new URLSearchParams(location.search).get('q') || '',
            role: new URLSearchParams(location.search).get('role') || '',
            loading: false,
            timeout: null,
            abortController: null,

            init() {
                this.$watch('q', () => this.debouncedFetch());
                this.$watch('role', () => this.fetchData());
            },

            get activeFiltersCount() {
                let count = 0;
                if (this.q !== '') count++;
                if (this.role !== '') count++;
                return count;
            },

            resetFilter() {
                this.q = '';
                this.role = '';
                this.fetchData();
            },

            debouncedFetch() {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.fetchData();
                }, 400);
            },

            async fetchData(targetUrl = null) {
                this.loading = true;

                if (this.abortController) {
                    this.abortController.abort();
                }
                this.abortController = new AbortController();

                let url = targetUrl;
                if (!url) {
                    const params = new URLSearchParams();
                    if (this.q !== '') params.append('q', this.q);
                    if (this.role !== '') params.append('role', this.role);

                    url = `${window.location.pathname}?${params.toString()}`;
                }

                try {
                    window.history.pushState({}, '', url);

                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal: this.abortController.signal
                    });

                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('table-content-container');

                    if (newContent) {
                        document.getElementById('table-content-container').innerHTML = newContent.innerHTML;
                    }
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.error('Failed to fetch data', error);
                    }
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endpush
@endsection

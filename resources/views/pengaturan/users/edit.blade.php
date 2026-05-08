@extends('layouts.app')

@section('title', 'Edit Pengguna — ' . $user->nama)

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Page Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('users.index') }}" class="p-2 rounded-xl bg-white border border-stone-200 text-stone-500 hover:text-stone-800 hover:bg-stone-50 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Edit Pengguna</h2>
            <p class="text-sm text-stone-500 mt-0.5">{{ $user->nama }} — {{ $user->role->label() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-[#043d2e] to-[#032e22] px-6 py-4">
            <h3 class="text-lg font-bold text-white">Informasi Akun</h3>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="px-6 py-5 space-y-4">
                {{-- NIP Section (Primary) --}}
                <div class="p-4 bg-stone-50 rounded-xl border border-stone-200">
                    <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">NIP Anggota Koperasi</label>
                    <input type="text" name="nip" required value="{{ old('nip', $user->nip) }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-white outline-none transition-all font-mono" placeholder="Masukkan NIP anggota koperasi">
                    @error('nip') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror

                    @if($anggotaNama)
                    <div class="mt-2 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-emerald-700 font-medium">Nama anggota: <strong>{{ $anggotaNama }}</strong></span>
                    </div>
                    @endif
                    <p class="text-[11px] text-stone-400 mt-1.5">Nama pengguna otomatis diambil dari data anggota berdasarkan NIP.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Username</label>
                        <input type="text" name="username" required value="{{ old('username', $user->username) }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all font-mono">
                        @error('username') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Role</label>
                        <select name="role" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                            <option value="admin" {{ old('role', $user->role->value) === 'admin' ? 'selected' : '' }}>Admin/Pengurus</option>
                            <option value="pimpinan" {{ old('role', $user->role->value) === 'pimpinan' ? 'selected' : '' }}>Pimpinan/Kepala</option>
                        </select>
                        @error('role') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Password Section --}}
                <div class="p-4 bg-amber-50/50 rounded-xl border border-amber-200/60">
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-3">Reset Password (Opsional)</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Password Baru</label>
                            <input type="password" name="password" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-white outline-none transition-all" placeholder="Kosongkan jika tidak diubah">
                            @error('password') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-white outline-none transition-all" placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-stone-50 px-6 py-4 border-t border-stone-200 flex justify-between">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-stone-300 bg-white text-sm font-bold text-stone-700 hover:bg-stone-50 transition-colors shadow-sm">Kembali</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl border border-transparent shadow-sm bg-[#043d2e] hover:bg-[#043d2e]/90 text-sm font-bold text-white transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

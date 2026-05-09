{{-- Shared form partial for Create & Edit User modals --}}
@php
    use App\Services\PermissionRegistry;
    $currentRole = $user ? ($user->getRoleNames()->first() ?? '') : '';
@endphp

<div class="space-y-4">
    {{-- NIP (optional) --}}
    <div>
        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">NIP Anggota <span class="text-stone-400 normal-case font-normal">(opsional)</span></label>
        <input type="text" name="nip" x-model="nip" value="{{ old('nip', $user->nip ?? '') }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all font-mono" placeholder="Kosongkan jika tidak terkait anggota">
        <p class="text-[11px] text-stone-400 mt-1">Jika diisi, nama otomatis diambil dari data anggota.</p>
        @error('nip') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
    </div>

    {{-- Nama (shown only when NIP empty) --}}
    <div x-show="!hasNip" x-transition>
        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $user->nama ?? '') }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all" placeholder="Masukkan nama lengkap" :required="!hasNip">
        @error('nama') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Username --}}
        <div>
            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Username</label>
            <input type="text" name="username" required value="{{ old('username', $user->username ?? '') }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all font-mono" placeholder="contoh: afauzi">
            @error('username') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>
        {{-- Role --}}
        <div>
            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Role</label>
            <select name="role" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                @foreach($roles as $r)
                    @if(auth()->user()->hasRole('super_admin') || $r->name !== 'super_admin')
                    <option value="{{ $r->name }}" {{ old('role', $currentRole) === $r->name ? 'selected' : '' }}>{{ PermissionRegistry::roleLabel($r->name) }}</option>
                    @endif
                @endforeach
            </select>
            @error('role') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Password --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">{{ $isEdit ? 'Password Baru' : 'Password' }}</label>
            <input type="password" name="password" {{ $isEdit ? '' : 'required' }} class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all" placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Min. 6 karakter' }}">
            @error('password') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" {{ $isEdit ? '' : 'required' }} class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all" placeholder="Ulangi password">
        </div>
    </div>
</div>

<x-slot name="footer">
    <button type="button" @click="$dispatch('close-modal', '{{ $isEdit ? 'modal-edit-user-'.$user->id : 'modal-tambah-user' }}')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batal</button>
    <button type="submit" form="{{ $formId }}" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Pengguna' }}</button>
</x-slot>

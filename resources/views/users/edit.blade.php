@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="mx-auto max-w-lg space-y-6">
    <div>
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar User
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">Edit User</h1>
        <p class="text-sm text-gray-500">Mengedit akun: {{ $user->username }}</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf @method('PUT')
            <div class="space-y-5">
                <div>
                    <label for="username" class="mb-1.5 block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('username') border-red-400 @enderror">
                    @error('username') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('email') border-red-400 @enderror">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="mb-1.5 block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" name="role" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                    <p class="mb-3 text-sm font-medium text-yellow-800">Ganti Password</p>
                    <p class="mb-3 text-xs text-yellow-600">Kosongkan jika tidak ingin mengubah password.</p>
                    <div class="space-y-3">
                        <div>
                            <label for="password" class="mb-1 block text-xs font-medium text-gray-600">Password Baru</label>
                            <input type="password" id="password" name="password" placeholder="Minimal 6 karakter"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('password') border-red-400 @enderror">
                            @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-1 block text-xs font-medium text-gray-600">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-gray-100 pt-6">
                <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">Perbarui</button>
                <a href="{{ route('users.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

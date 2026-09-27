@extends('layouts.app')
@section('title', 'Kelola User')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola User</h1>
            <p class="text-sm text-gray-500">Tambah, edit, atau hapus akun kasir dan admin</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah User
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-6 py-3 font-semibold text-gray-600">No</th>
                    <th class="px-6 py-3 font-semibold text-gray-600">Username</th>
                    <th class="px-6 py-3 font-semibold text-gray-600">Email</th>
                    <th class="px-6 py-3 font-semibold text-gray-600">Role</th>
                    <th class="px-6 py-3 font-semibold text-gray-600">Dibuat</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $i => $user)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-6 py-4 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-semibold text-white
                                {{ $user->role === 'owner' ? 'bg-purple-600' : ($user->role === 'admin' ? 'bg-blue-600' : 'bg-green-600') }}">
                                {{ strtoupper(substr($user->username, 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-800">{{ $user->username }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @php
                            $roleColors = [
                                'owner' => 'bg-purple-100 text-purple-700',
                                'admin' => 'bg-blue-100 text-blue-700',
                                'kasir' => 'bg-green-100 text-green-700',
                            ];
                        @endphp
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $roleColors[$user->role] }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        @if($user->id !== auth()->id() && !$user->isOwner())
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('users.edit', $user) }}" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-blue-50 hover:text-blue-600" title="Edit">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus user {{ $user->username }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-red-50 hover:text-red-600" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

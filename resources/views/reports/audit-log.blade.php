@extends('layouts.app')
@section('title', 'Audit Log')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Audit Log</h1>
        <p class="text-sm text-gray-500">Riwayat aktivitas penting dalam sistem</p>
    </div>

    {{-- Filter --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('reports.audit-log') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-500">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi, detail, atau username..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">Cari</button>
            <a href="{{ route('reports.audit-log') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-center text-sm text-gray-600 transition hover:bg-gray-50">Reset</a>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600">Waktu</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">User</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Aksi</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{{ $log->timestamp ? \Carbon\Carbon::parse($log->timestamp)->format('d/m/Y H:i:s') : '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="font-medium text-gray-800">{{ $log->user->username ?? 'System' }}</span>
                        <span class="ml-1 text-xs text-gray-400">({{ $log->user->role ?? '-' }})</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $log->aksi }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 max-w-md">{{ $log->detail_perubahan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada log aktivitas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>
@endsection

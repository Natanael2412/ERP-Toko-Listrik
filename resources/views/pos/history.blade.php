@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Transaksi</h1>
        <p class="text-sm text-gray-500">
            @if(auth()->user()->isKasir())
                Daftar transaksi yang Anda lakukan
            @else
                Semua riwayat transaksi penjualan
            @endif
        </p>
    </div>

    {{-- Filter --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('pos.history') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-500">Cari Nota</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor nota..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
            <div class="w-full sm:w-48">
                <label class="mb-1 block text-xs font-medium text-gray-500">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
            <div class="w-full sm:w-48">
                <label class="mb-1 block text-xs font-medium text-gray-500">Urutkan</label>
                <select name="sort" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                    <option value="terbesar" {{ request('sort') == 'terbesar' ? 'selected' : '' }}>Total Terbesar</option>
                    <option value="terkecil" {{ request('sort') == 'terkecil' ? 'selected' : '' }}>Total Terkecil</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">Filter</button>
            <a href="{{ route('pos.history') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-center text-sm text-gray-600 transition hover:bg-gray-50">Reset</a>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600">Nomor Nota</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Kasir</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Subtotal</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Diskon</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $trx)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-primary-600">{{ $trx->nomor_nota }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $trx->tanggal_waktu->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $trx->user->username }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right {{ $trx->diskon > 0 ? 'text-red-500' : 'text-gray-400' }}">
                        {{ $trx->diskon > 0 ? '-Rp ' . number_format($trx->diskon, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($trx->total_penjualan, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('pos.receipt', $trx) }}" class="rounded-lg px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 transition" title="Lihat Struk">
                            Struk
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">Belum ada transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $transactions->links() }}
</div>
@endsection

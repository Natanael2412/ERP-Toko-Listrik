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
                    <th class="px-4 py-3 font-semibold text-gray-600">Pembayaran</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Kasir</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Subtotal</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Diskon</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $trx)
                <tr class="transition hover:bg-gray-50 {{ $trx->status === 'void' ? 'bg-red-50/50 opacity-75' : '' }}">
                    <td class="px-4 py-3 font-mono text-xs font-semibold">
                        <a href="{{ route('pos.receipt', $trx) }}" class="text-primary-600 hover:text-primary-800 hover:underline transition">
                            {{ $trx->nomor_nota }}
                        </a>
                        @if($trx->status === 'void')
                            <span class="ml-2 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">VOID</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $trx->tanggal_waktu->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-600">{{ strtoupper($trx->metode_pembayaran ?? 'CASH') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $trx->user->username }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">
                        @if($trx->status === 'void') <del> @endif
                        Rp {{ number_format($trx->subtotal, 0, ',', '.') }}
                        @if($trx->status === 'void') </del> @endif
                    </td>
                    <td class="px-4 py-3 text-right {{ $trx->diskon > 0 ? 'text-red-500' : 'text-gray-400' }}">
                        @if($trx->status === 'void') <del> @endif
                        {{ $trx->diskon > 0 ? '-Rp ' . number_format($trx->diskon, 0, ',', '.') : '-' }}
                        @if($trx->status === 'void') </del> @endif
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">
                        @if($trx->status === 'void') <del> @endif
                        Rp {{ number_format($trx->total_penjualan, 0, ',', '.') }}
                        @if($trx->status === 'void') </del> @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('pos.receipt', $trx) }}" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-blue-50 hover:text-blue-600" title="Lihat Struk">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                                @if($trx->status !== 'void')
                                    <form action="{{ route('pos.void', $trx) }}" method="POST" onsubmit="return confirm('Yakin ingin me-void transaksi ini? Stok barang akan otomatis dikembalikan.');">
                                        @csrf
                                        <button type="submit" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-red-50 hover:text-red-600" title="Void Transaksi">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
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

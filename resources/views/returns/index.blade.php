@extends('layouts.app')
@section('title', 'Retur Penjualan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Retur Penjualan</h1>
            <p class="text-sm text-gray-500">Riwayat pengembalian barang dari pelanggan</p>
        </div>
        <a href="{{ route('returns.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Retur
        </a>
    </div>

    {{-- Filter --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('returns.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="w-full sm:w-48">
                <label class="mb-1 block text-xs font-medium text-gray-500">Urutkan</label>
                <select name="sort" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                    <option value="terbesar" {{ request('sort') == 'terbesar' ? 'selected' : '' }}>Refund Terbesar</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">No. Nota Asli</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Produk</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Qty Retur</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Refund</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Alasan</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Diproses Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($returns as $return)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $return->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-primary-600">{{ $return->transactionDetail->transaction->nomor_nota }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $return->transactionDetail->product->nama_barang }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">{{ $return->qty_retur }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-red-600">Rp {{ number_format($return->jumlah_refund, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs max-w-[200px] truncate">{{ $return->alasan }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $return->user->username }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">Belum ada data retur.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $returns->links() }}
</div>
@endsection

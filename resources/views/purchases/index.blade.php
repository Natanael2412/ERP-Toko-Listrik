@extends('layouts.app')
@section('title', 'Pembelian / Restock')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pembelian / Restock</h1>
            <p class="text-sm text-gray-500">Riwayat pembelian barang dari supplier</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pembelian
        </a>
    </div>

    {{-- Filter --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('purchases.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-500">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Faktur, supplier, atau produk..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
            <div class="w-full sm:w-48">
                <label class="mb-1 block text-xs font-medium text-gray-500">Urutkan</label>
                <select name="sort" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                    <option value="qty_terbanyak" {{ request('sort') == 'qty_terbanyak' ? 'selected' : '' }}>Qty Terbanyak</option>
                    <option value="total_terbesar" {{ request('sort') == 'total_terbesar' ? 'selected' : '' }}>Total Beli Terbesar</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">Filter</button>
            <a href="{{ route('purchases.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-center text-sm text-gray-600 transition hover:bg-gray-50">Reset</a>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">No. Faktur</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Supplier</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Produk</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Qty</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Harga Beli</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Jatuh Tempo</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($purchases as $purchase)
                @php $jatuhTempo = $purchase->isJatuhTempo(); @endphp
                <tr class="transition hover:bg-gray-50 {{ $jatuhTempo ? 'bg-red-50/50' : '' }}">
                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $purchase->tanggal_masuk->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-primary-600">
                        <a href="{{ route('purchases.receipt', $purchase) }}" class="hover:text-primary-800 hover:underline transition flex items-center gap-1">
                            {{ $purchase->nomor_faktur }}
                            @if($purchase->bukti_faktur)
                                <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            @endif
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $purchase->nama_supplier }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $purchase->product->nama_barang }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">{{ $purchase->qty_masuk }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($purchase->harga_beli, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($purchase->total_beli, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        @if($purchase->status_bayar === 'lunas')
                            <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Lunas</span>
                        @else
                            <span class="inline-flex rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-700">Belum Lunas</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs {{ $jatuhTempo ? 'font-bold text-red-600' : 'text-gray-500' }}">
                        {{ $purchase->tanggal_jatuh_tempo->format('d/m/Y') }}
                        @if($jatuhTempo)
                            <span class="block text-red-500">⚠️ Lewat!</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            {{-- Bayar Button (if not lunas) --}}
                            @if($purchase->status_bayar !== 'lunas')
                                <a href="{{ route('debts.show', $purchase) }}" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-green-50 hover:text-green-600" title="Bayar Hutang">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </a>
                            @endif
                            {{-- Detail Faktur --}}
                            <a href="{{ route('purchases.receipt', $purchase) }}" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-blue-50 hover:text-blue-600" title="Detail Faktur">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            {{-- Edit --}}
                            <a href="{{ route('purchases.edit', $purchase) }}" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-yellow-50 hover:text-yellow-600" title="Edit Faktur">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            {{-- Void/Hapus --}}
                            <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus / void faktur ini? Stok barang akan otomatis dikurangi.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-red-50 hover:text-red-600" title="Void Faktur">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-12 text-center text-gray-400">Belum ada data pembelian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $purchases->links() }}
</div>
@endsection

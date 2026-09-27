@extends('layouts.app')
@section('title', 'Kartu Stok')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Kartu Stok</h1>
        <p class="text-sm text-gray-500">Riwayat mutasi barang masuk dan keluar per produk</p>
    </div>

    {{-- Pilih Produk --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('reports.stock-card') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-500">Pilih Produk</label>
                <select name="product_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    <option value="">— Pilih Produk —</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_barang }} (Stok: {{ $p->stok }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">Tampilkan</button>
        </form>
    </div>

    @if($selectedProduct)
    {{-- Info Produk --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center gap-6">
            <div>
                <p class="text-xs text-gray-400">Produk</p>
                <p class="text-lg font-bold text-gray-800">{{ $selectedProduct->nama_barang }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Stok Saat Ini</p>
                <p class="text-lg font-bold {{ $selectedProduct->stok <= $selectedProduct->stok_minimum ? 'text-red-600' : 'text-gray-800' }}">
                    {{ $selectedProduct->stok }} {{ $selectedProduct->satuan }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400">HPP</p>
                <p class="text-lg font-bold text-gray-800">Rp {{ number_format($selectedProduct->hpp, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Harga Jual</p>
                <p class="text-lg font-bold text-primary-600">Rp {{ number_format($selectedProduct->harga_jual, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Tabel Mutasi --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Tipe</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Masuk</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Keluar</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Harga</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($mutations as $m)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 text-xs text-gray-500">{{ \Carbon\Carbon::parse($m['tanggal'])->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">
                        @if($m['tipe'] === 'masuk')
                            <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Masuk</span>
                        @elseif($m['tipe'] === 'keluar')
                            <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Keluar</span>
                        @else
                            <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Retur Masuk</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-green-600">
                        {{ in_array($m['tipe'], ['masuk', 'retur_masuk']) ? '+' . $m['qty'] : '' }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-red-600">
                        {{ $m['tipe'] === 'keluar' ? '-' . $m['qty'] : '' }}
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($m['harga'], 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $m['keterangan'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">Belum ada mutasi untuk produk ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
    @else
    {{-- Overview (Jika tidak ada produk yang dipilih) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Stok Rendah --}}
        <div class="rounded-xl border border-orange-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-sm font-bold text-orange-600 flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Peringatan Stok Rendah
            </h2>
            <div class="divide-y divide-gray-100 max-h-60 overflow-y-auto pr-2">
                @forelse($overview['low_stock'] as $p)
                    <div class="py-2 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-800">{{ $p->nama_barang }}</span>
                        <span class="rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold text-orange-700">{{ $p->stok }} tersisa</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-2">Semua stok produk aman.</p>
                @endforelse
            </div>
        </div>

        {{-- Stok Habis --}}
        <div class="rounded-xl border border-red-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-sm font-bold text-red-600 flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Stok Habis (Kosong)
            </h2>
            <div class="divide-y divide-gray-100 max-h-60 overflow-y-auto pr-2">
                @forelse($overview['out_of_stock'] as $p)
                    <div class="py-2 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-800">{{ $p->nama_barang }}</span>
                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">Habis</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-2">Tidak ada produk yang stoknya habis.</p>
                @endforelse
            </div>
        </div>

        {{-- Stok Mati --}}
        <div class="rounded-xl border border-gray-300 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-sm font-bold text-gray-600 flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Stok Menumpuk & Kurang Laku
            </h2>
            <div class="divide-y divide-gray-100 max-h-60 overflow-y-auto pr-2">
                @forelse($overview['dead_stock'] as $p)
                    <div class="py-2 flex flex-col justify-center">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-800">{{ $p->nama_barang }}</span>
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-600">Stok: {{ $p->stok }}</span>
                        </div>
                        <span class="text-[10px] text-gray-400">Terjual bulan ini: {{ $p->terjual_sebulan ?? 0 }} unit</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-2">Stok perputaran barang sehat.</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

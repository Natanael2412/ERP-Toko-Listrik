@extends('layouts.app')
@section('title', 'Edit Produk')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Produk
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">Edit Produk</h1>
        <p class="text-sm text-gray-500">{{ $product->nama_barang }} — SKU: {{ $product->sku }}</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="sku" class="mb-1.5 block text-sm font-medium text-gray-700">Kode SKU</label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('sku') border-red-400 @enderror">
                    @error('sku') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700">Kategori</label>
                    <select id="category_id" name="category_id" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="nama_barang" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $product->nama_barang) }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('nama_barang') border-red-400 @enderror">
                    @error('nama_barang') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="satuan" class="mb-1.5 block text-sm font-medium text-gray-700">Satuan</label>
                    <select id="satuan" name="satuan" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        @foreach(['pcs', 'unit', 'meter', 'cm', 'roll', 'set', 'box', 'pak', 'kg', 'gram', 'liter', 'lusin', 'batang', 'lembar', 'buah'] as $sat)
                            <option value="{{ $sat }}" {{ old('satuan', $product->satuan ?? 'pcs') == $sat ? 'selected' : '' }}>{{ ucfirst($sat) }}</option>
                        @endforeach
                    </select>
                    @error('satuan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Stok Saat Ini</label>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-semibold {{ $product->isStokRendah() ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $product->stok }} {{ $product->satuan ?? 'pcs' }}
                        @if($product->isStokRendah())
                            <span class="ml-1 text-xs font-normal text-red-500">(di bawah minimum)</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Stok hanya bisa berubah melalui Pembelian atau Transaksi</p>
                </div>

                <div>
                    <label for="stok_minimum" class="mb-1.5 block text-sm font-medium text-gray-700">Batas Stok Minimum</label>
                    <input type="number" id="stok_minimum" name="stok_minimum" value="{{ old('stok_minimum', $product->stok_minimum) }}" required min="0"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    @error('stok_minimum') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">HPP (Moving Average)</label>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-800">
                        Rp {{ number_format($product->hpp, 0, ',', '.') }}
                    </div>
                    <p class="mt-1 text-xs text-gray-400">HPP dihitung otomatis saat ada pembelian baru</p>
                </div>

                <div>
                    <label for="harga_jual" class="mb-1.5 block text-sm font-medium text-gray-700">Harga Jual (Rp)</label>
                    <input type="number" id="harga_jual" name="harga_jual" value="{{ old('harga_jual', $product->harga_jual) }}" required min="0" step="100"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    @error('harga_jual') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-gray-100 pt-6">
                <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">Perbarui</button>
                <a href="{{ route('products.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

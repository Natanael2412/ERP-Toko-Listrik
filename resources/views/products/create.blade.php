@extends('layouts.app')
@section('title', 'Tambah Produk')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Produk
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">Tambah Produk Baru</h1>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="sku" class="mb-1.5 block text-sm font-medium text-gray-700">Kode SKU</label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku') }}" required placeholder="Contoh: KBL-NYM-2.5"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('sku') border-red-400 @enderror">
                    @error('sku') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700">Kategori</label>
                    <select id="category_id" name="category_id" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('category_id') border-red-400 @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="nama_barang" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: Kabel NYM 2x2.5mm"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('nama_barang') border-red-400 @enderror">
                    @error('nama_barang') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="satuan" class="mb-1.5 block text-sm font-medium text-gray-700">Satuan</label>
                    <select id="satuan" name="satuan" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        @foreach(['pcs', 'unit', 'meter', 'cm', 'roll', 'set', 'box', 'pak', 'kg', 'gram', 'liter', 'lusin', 'batang', 'lembar', 'buah'] as $sat)
                            <option value="{{ $sat }}" {{ old('satuan', 'pcs') == $sat ? 'selected' : '' }}>{{ ucfirst($sat) }}</option>
                        @endforeach
                    </select>
                    @error('satuan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="stok" class="mb-1.5 block text-sm font-medium text-gray-700">Stok Awal</label>
                    <input type="number" id="stok" name="stok" value="{{ old('stok', 0) }}" required min="0"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('stok') border-red-400 @enderror">
                    @error('stok') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="stok_minimum" class="mb-1.5 block text-sm font-medium text-gray-700">Batas Stok Minimum</label>
                    <input type="number" id="stok_minimum" name="stok_minimum" value="{{ old('stok_minimum', 5) }}" required min="0"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('stok_minimum') border-red-400 @enderror">
                    <p class="mt-1 text-xs text-gray-400">Peringatan muncul jika stok ≤ angka ini</p>
                    @error('stok_minimum') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="harga_jual" class="mb-1.5 block text-sm font-medium text-gray-700">Harga Jual (Rp)</label>
                    <input type="number" id="harga_jual" name="harga_jual" value="{{ old('harga_jual') }}" required min="0" step="100"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('harga_jual') border-red-400 @enderror">
                    @error('harga_jual') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-gray-100 pt-6">
                <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">Simpan</button>
                <a href="{{ route('products.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

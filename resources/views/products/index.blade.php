@extends('layouts.app')
@section('title', 'Daftar Produk')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Produk</h1>
            <p class="text-sm text-gray-500">{{ $products->total() }} produk ditemukan</p>
        </div>
        @if(auth()->user()->hasRole('admin', 'owner'))
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Produk
        </a>
        @endif
    </div>

    {{-- Filter --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-500">Cari Produk</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama barang atau SKU..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
            <div class="w-full sm:w-48">
                <label class="mb-1 block text-xs font-medium text-gray-500">Kategori</label>
                <select name="category_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">
                    <input type="checkbox" name="stok_rendah" value="1" {{ request('stok_rendah') ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600">
                    <span class="text-red-600 font-medium">Stok Rendah</span>
                </label>
            </div>
            <div class="w-full sm:w-48">
                <label class="mb-1 block text-xs font-medium text-gray-500">Urutkan</label>
                <select name="sort" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>A-Z</option>
                    <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Z-A</option>
                    <option value="stok_terbanyak" {{ request('sort') == 'stok_terbanyak' ? 'selected' : '' }}>Stok Terbanyak</option>
                    <option value="stok_sedikit" {{ request('sort') == 'stok_sedikit' ? 'selected' : '' }}>Stok Paling Sedikit</option>
                    <option value="harga_termahal" {{ request('sort') == 'harga_termahal' ? 'selected' : '' }}>Harga Termahal</option>
                    <option value="harga_termurah" {{ request('sort') == 'harga_termurah' ? 'selected' : '' }}>Harga Termurah</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">Filter</button>
            <a href="{{ route('products.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-center text-sm text-gray-600 transition hover:bg-gray-50">Reset</a>
        </form>
    </div>

    {{-- Tabel Produk --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600">SKU</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Nama Barang</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Kategori</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Satuan</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Stok</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">HPP</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Harga Jual</th>
                    @if(auth()->user()->hasRole('admin', 'owner'))
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                <tr class="transition hover:bg-gray-50 {{ $product->isStokRendah() ? 'bg-red-50/50' : '' }}">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $product->sku }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800">{{ $product->nama_barang }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ $product->category->nama_kategori }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ ucfirst($product->satuan ?? 'pcs') }}</td>
                    <td class="px-4 py-3 text-right">
                        @if($product->isStokRendah())
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $product->stok }}
                            </span>
                        @else
                            <span class="font-medium text-gray-800">{{ $product->stok }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($product->hpp, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-medium text-gray-800">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                    @if(auth()->user()->hasRole('admin', 'owner'))
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('products.edit', $product) }}" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-blue-50 hover:text-blue-600" title="Edit">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-red-50 hover:text-red-600" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400">Belum ada produk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
</div>
@endsection

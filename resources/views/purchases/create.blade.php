@extends('layouts.app')
@section('title', 'Tambah Pembelian')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <a href="{{ route('purchases.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Pembelian
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">Tambah Pembelian / Restock</h1>
        <p class="text-sm text-gray-500">HPP Moving Average akan dihitung ulang otomatis saat disimpan</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('purchases.store') }}">
            @csrf
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="product_id" class="mb-1.5 block text-sm font-medium text-gray-700">Produk</label>
                    <select id="product_id" name="product_id" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('product_id') border-red-400 @enderror">
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}
                                data-stok="{{ $product->stok }}" data-hpp="{{ $product->hpp }}">
                                {{ $product->nama_barang }} (Stok: {{ $product->stok }}, HPP: Rp {{ number_format($product->hpp, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nomor_faktur" class="mb-1.5 block text-sm font-medium text-gray-700">Nomor Faktur</label>
                    <input type="text" id="nomor_faktur" name="nomor_faktur" value="{{ old('nomor_faktur') }}" required placeholder="Contoh: FK-2026-001"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('nomor_faktur') border-red-400 @enderror">
                    @error('nomor_faktur') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="supplier_id" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Supplier</label>
                    <select id="supplier_id" name="supplier_id" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('supplier_id') border-red-400 @enderror">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="qty_masuk" class="mb-1.5 block text-sm font-medium text-gray-700">Jumlah Masuk</label>
                    <input type="number" id="qty_masuk" name="qty_masuk" value="{{ old('qty_masuk') }}" required min="1"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('qty_masuk') border-red-400 @enderror">
                    @error('qty_masuk') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="harga_beli" class="mb-1.5 block text-sm font-medium text-gray-700">Harga Beli per Satuan (Rp)</label>
                    <input type="number" id="harga_beli" name="harga_beli" value="{{ old('harga_beli') }}" required min="0" step="100"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('harga_beli') border-red-400 @enderror">
                    @error('harga_beli') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tanggal_masuk" class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                    <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('tanggal_masuk') border-red-400 @enderror">
                    @error('tanggal_masuk') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tanggal_jatuh_tempo" class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal Jatuh Tempo</label>
                    <input type="date" id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo') }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('tanggal_jatuh_tempo') border-red-400 @enderror">
                    <p class="mt-1 text-xs text-gray-400">Batas waktu bayar ke supplier</p>
                    @error('tanggal_jatuh_tempo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Preview Kalkulasi --}}
            <div id="preview-calc" class="mt-5 hidden rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm">
                <p class="mb-2 font-semibold text-blue-800">Preview Perhitungan:</p>
                <div class="space-y-1 text-blue-700">
                    <p>Total Beli: <span id="prev-total" class="font-bold">-</span></p>
                    <p>HPP Lama: <span id="prev-hpp-lama" class="font-bold">-</span></p>
                    <p>HPP Baru (Moving Avg): <span id="prev-hpp-baru" class="font-bold text-blue-900">-</span></p>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-gray-100 pt-6">
                <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">Simpan Pembelian</button>
                <a href="{{ route('purchases.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('qty_masuk');
    const hargaInput = document.getElementById('harga_beli');
    const preview = document.getElementById('preview-calc');

    function updatePreview() {
        const option = productSelect.options[productSelect.selectedIndex];
        const qty = parseInt(qtyInput.value) || 0;
        const harga = parseFloat(hargaInput.value) || 0;

        if (!option.value || qty <= 0 || harga <= 0) {
            preview.classList.add('hidden');
            return;
        }

        const stokLama = parseInt(option.dataset.stok) || 0;
        const hppLama = parseFloat(option.dataset.hpp) || 0;
        const totalBeli = qty * harga;

        let hppBaru;
        if (stokLama + qty > 0) {
            hppBaru = ((stokLama * hppLama) + (qty * harga)) / (stokLama + qty);
        } else {
            hppBaru = harga;
        }

        document.getElementById('prev-total').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalBeli);
        document.getElementById('prev-hpp-lama').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(hppLama);
        document.getElementById('prev-hpp-baru').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(hppBaru));

        preview.classList.remove('hidden');
    }

    productSelect.addEventListener('change', updatePreview);
    qtyInput.addEventListener('input', updatePreview);
    hargaInput.addEventListener('input', updatePreview);
</script>
@endpush

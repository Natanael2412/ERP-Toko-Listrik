@extends('layouts.app')
@section('title', 'Edit Faktur Pembelian')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <a href="{{ route('purchases.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Pembelian
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">Edit Faktur Pembelian</h1>
        <p class="text-sm text-gray-500">Note: Qty dan Harga Beli dikunci agar HPP tidak rusak. Jika salah, harap Void faktur ini.</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('purchases.update', $purchase) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Produk (Tidak bisa diubah)</label>
                    <input type="text" value="{{ $purchase->product->nama_barang }}" disabled
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">
                </div>

                <div>
                    <label for="nomor_faktur" class="mb-1.5 block text-sm font-medium text-gray-700">Nomor Faktur</label>
                    <input type="text" id="nomor_faktur" name="nomor_faktur" value="{{ old('nomor_faktur', $purchase->nomor_faktur) }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('nomor_faktur') border-red-400 @enderror">
                    @error('nomor_faktur') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="supplier_id" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Supplier</label>
                    <select id="supplier_id" name="supplier_id" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('supplier_id') border-red-400 @enderror">
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Jumlah Masuk (Dikunci)</label>
                    <input type="text" value="{{ $purchase->qty_masuk }}" disabled
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Harga Beli (Dikunci)</label>
                    <input type="text" value="Rp {{ number_format($purchase->harga_beli, 0, ',', '.') }}" disabled
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">
                </div>

                <div>
                    <label for="tanggal_masuk" class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                    <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', $purchase->tanggal_masuk->format('Y-m-d')) }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('tanggal_masuk') border-red-400 @enderror">
                    @error('tanggal_masuk') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tanggal_jatuh_tempo" class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal Jatuh Tempo</label>
                    <input type="date" id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo', $purchase->tanggal_jatuh_tempo->format('Y-m-d')) }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('tanggal_jatuh_tempo') border-red-400 @enderror">
                    @error('tanggal_jatuh_tempo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="status_bayar" class="mb-1.5 block text-sm font-medium text-gray-700">Status Pembayaran</label>
                    <select id="status_bayar" name="status_bayar" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('status_bayar') border-red-400 @enderror">
                        <option value="belum_lunas" {{ old('status_bayar', $purchase->status_bayar) == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="lunas" {{ old('status_bayar', $purchase->status_bayar) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                    @error('status_bayar') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="bukti_faktur" class="mb-1.5 block text-sm font-medium text-gray-700">Upload Bukti Faktur Baru (Opsional)</label>
                    <input type="file" id="bukti_faktur" name="bukti_faktur" accept="image/jpeg,image/png,image/jpg"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm file:mr-4 file:rounded-full file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100 focus:border-primary-500 focus:outline-none">
                    @if($purchase->bukti_faktur)
                        <p class="mt-2 text-xs text-green-600 font-medium">✓ Bukti faktur saat ini sudah terlampir. Upload file baru jika ingin mengganti.</p>
                    @endif
                    @error('bukti_faktur') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-6">
                <div class="flex gap-3">
                    <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">Update Faktur</button>
                    <a href="{{ route('purchases.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

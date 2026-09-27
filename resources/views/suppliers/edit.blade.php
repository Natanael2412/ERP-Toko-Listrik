@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Data Supplier
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">Edit Supplier</h1>
        <p class="text-sm text-gray-500">Ubah informasi supplier</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="nama_supplier" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Supplier</label>
                    <input type="text" id="nama_supplier" name="nama_supplier" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('nama_supplier') border-red-400 @enderror">
                    @error('nama_supplier') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="kontak" class="mb-1.5 block text-sm font-medium text-gray-700">Kontak</label>
                    <input type="text" id="kontak" name="kontak" value="{{ old('kontak', $supplier->kontak) }}" placeholder="Contoh: 081234567890"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('kontak') border-red-400 @enderror">
                    @error('kontak') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="alamat" class="mb-1.5 block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('alamat') border-red-400 @enderror">{{ old('alamat', $supplier->alamat) }}</textarea>
                    @error('alamat') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                <a href="{{ route('suppliers.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100">Batal</a>
                <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-primary-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

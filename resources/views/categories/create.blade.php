@extends('layouts.app')
@section('title', 'Tambah Kategori')

@section('content')
<div class="mx-auto max-w-lg space-y-6">
    <div>
        <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Kategori
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">Tambah Kategori Baru</h1>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="mb-6">
                <label for="nama_kategori" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Kategori</label>
                <input type="text" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}" required autofocus
                    placeholder="Contoh: Kabel, Lampu, Saklar"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('nama_kategori') border-red-400 @enderror">
                @error('nama_kategori')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">Simpan</button>
                <a href="{{ route('categories.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

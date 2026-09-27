@extends('layouts.app')
@section('title', 'Laba Rugi')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Laba Rugi</h1>
        <p class="text-sm text-gray-500">Ringkasan keuangan bulanan toko</p>
    </div>

    {{-- Pilih Bulan --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('reports.profit-loss') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500">Periode Bulan</label>
                <input type="month" name="bulan" value="{{ $bulan }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">Tampilkan</button>
        </form>
    </div>

    {{-- Laporan --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="text-center text-lg font-bold text-gray-800">LAPORAN LABA RUGI</h2>
            <p class="text-center text-sm text-gray-500">Adit Kejut — Periode {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}</p>
        </div>

        <div class="divide-y divide-gray-100 px-6 py-4">
            {{-- Pendapatan --}}
            <div class="py-3">
                <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Pendapatan</p>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-sm text-gray-600 pl-4">Penjualan ({{ $jumlahTransaksi }} transaksi)</span>
                <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($pendapatanPenjualan, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-sm text-gray-600 pl-4">Retur Penjualan ({{ $jumlahRetur }} retur)</span>
                <span class="text-sm font-semibold text-red-600">(Rp {{ number_format($totalRetur, 0, ',', '.') }})</span>
            </div>
            <div class="flex items-center justify-between py-3 bg-gray-50 -mx-6 px-6">
                <span class="text-sm font-bold text-gray-700">Pendapatan Bersih</span>
                <span class="text-sm font-bold text-gray-800">Rp {{ number_format($pendapatanPenjualan - $totalRetur, 0, ',', '.') }}</span>
            </div>

            {{-- HPP --}}
            <div class="py-3 pt-5">
                <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Harga Pokok Penjualan</p>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-sm text-gray-600 pl-4">HPP (Moving Average)</span>
                <span class="text-sm font-semibold text-gray-800">(Rp {{ number_format($hpp, 0, ',', '.') }})</span>
            </div>

            {{-- Laba Kotor --}}
            <div class="flex items-center justify-between py-4 -mx-6 px-6 {{ $labaKotor >= 0 ? 'bg-green-50' : 'bg-red-50' }}">
                <span class="text-base font-bold {{ $labaKotor >= 0 ? 'text-green-700' : 'text-red-700' }}">LABA KOTOR</span>
                <span class="text-xl font-bold {{ $labaKotor >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    Rp {{ number_format($labaKotor, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Margin --}}
        @if($pendapatanPenjualan > 0)
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Margin Laba Kotor</span>
                <span class="font-bold {{ $labaKotor >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ round(($labaKotor / $pendapatanPenjualan) * 100, 1) }}%
                </span>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

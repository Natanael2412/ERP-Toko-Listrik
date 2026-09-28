@extends('layouts.app')
@section('title', 'Laporan Keuangan (Financial Statement)')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h1>
        <p class="text-sm text-gray-500">Laba Rugi dan Neraca Keuangan dalam satu tampilan terpadu.</p>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('reports.financial-statement') }}" class="flex flex-wrap items-center gap-4 bg-white px-4 py-2 rounded-full border border-gray-200 shadow-sm">
        <div class="relative flex items-center border-r border-gray-200 pr-4">
            <select name="filter" onchange="this.form.submit()" class="appearance-none bg-transparent border-none text-gray-700 font-medium text-sm focus:ring-0 pr-6 cursor-pointer">
                <option value="bulan" {{ $filter === 'bulan' ? 'selected' : '' }}>Bulan Tertentu</option>
                <option value="tahun" {{ $filter === 'tahun' ? 'selected' : '' }}>Tahun Tertentu</option>
                <option value="semua" {{ $filter === 'semua' ? 'selected' : '' }}>Semua Waktu</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center text-gray-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </div>

        <div>
            @if($filter === 'bulan')
                <input type="month" name="bulan" value="{{ $bulan }}" onchange="this.form.submit()" 
                    class="bg-transparent border-none text-primary-700 font-medium text-sm focus:ring-0 cursor-pointer px-0 py-0">
            @elseif($filter === 'tahun')
                <input type="number" name="tahun" value="{{ $tahun_filter }}" min="2000" max="{{ date('Y') }}" onchange="this.form.submit()"
                    class="bg-transparent border-none text-primary-700 font-medium text-sm focus:ring-0 w-24 cursor-pointer px-0 py-0">
            @else
                <span class="text-sm font-medium text-primary-700">Sejak Awal</span>
            @endif
        </div>
        
        <button type="submit" class="hidden"></button>
    </form>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    
    <!-- LABA RUGI (PROFIT & LOSS) -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
            <h2 class="text-lg font-bold text-gray-800">Laba Rugi (Profit & Loss)</h2>
            <p class="text-xs text-gray-500">
                Periode: 
                @if($filter === 'bulan')
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                @elseif($filter === 'tahun')
                    Tahun {{ $tahun_filter }}
                @else
                    Semua Waktu
                @endif
            </p>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <span class="text-sm font-medium text-gray-600">Pendapatan Penjualan</span>
                <span class="font-semibold text-gray-800">Rp {{ number_format($pendapatanPenjualan, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <span class="text-sm font-medium text-gray-600">Retur Penjualan</span>
                <span class="font-semibold text-red-500">- Rp {{ number_format($totalRetur, 0, ',', '.') }}</span>
            </div>

            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <span class="text-sm font-medium text-gray-600">Harga Pokok Penjualan (HPP)</span>
                <span class="font-semibold text-red-500">- Rp {{ number_format($hpp, 0, ',', '.') }}</span>
            </div>

            <div class="pt-2">
                <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4">
                    <span class="font-bold text-gray-800">Laba Kotor (Gross Profit)</span>
                    <span class="text-xl font-bold {{ $labaKotor >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($labaKotor, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- NERACA (BALANCE SHEET) -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
            <h2 class="text-lg font-bold text-gray-800">Neraca (Balance Sheet)</h2>
            <p class="text-xs text-gray-500">Posisi per Tanggal: {{ \Carbon\Carbon::parse($tanggalNeraca)->translatedFormat('d F Y') }}</p>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Aset -->
            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-gray-500">Aset (Harta)</h3>
                <div class="space-y-3">
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-sm text-gray-600">Kas (Estimasi)</span>
                        <span class="text-sm font-medium text-gray-800">Rp {{ number_format($kas, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-sm text-gray-600">Persediaan Barang (Nilai HPP)</span>
                        <span class="text-sm font-medium text-gray-800">Rp {{ number_format($persediaan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span class="font-bold text-gray-800">Total Aset</span>
                        <span class="font-bold text-primary-700">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Kewajiban -->
            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-gray-500">Kewajiban (Utang)</h3>
                <div class="space-y-3">
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-sm text-gray-600">Utang Usaha (Supplier)</span>
                        <span class="text-sm font-medium text-gray-800">Rp {{ number_format($hutangUsaha, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span class="font-bold text-gray-800">Total Kewajiban</span>
                        <span class="font-bold text-red-600">Rp {{ number_format($hutangUsaha, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="rounded-lg bg-primary-50 p-4">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-primary-900">Modal (Ekuitas)</span>
                    <span class="text-xl font-bold text-primary-700">
                        Rp {{ number_format($modal, 0, ',', '.') }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-primary-600/80 text-right">Modal = Total Aset - Total Kewajiban</p>
            </div>
        </div>
    </div>
    
</div>
@endsection

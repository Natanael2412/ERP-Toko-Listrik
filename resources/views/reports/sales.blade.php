@extends('layouts.app')
@section('title', 'Laporan Penjualan')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Penjualan</h1>
        <p class="text-sm text-gray-500">Ringkasan transaksi penjualan harian atau bulanan</p>
    </div>

    {{-- Filter Periode --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500">Periode</label>
                <select name="periode" onchange="togglePeriode(this.value)" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    <option value="harian" {{ $periode === 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="bulanan" {{ $periode === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            <div id="filter-harian" class="{{ $periode === 'bulanan' ? 'hidden' : '' }}">
                <label class="mb-1 block text-xs font-medium text-gray-500">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
            </div>
            <div id="filter-bulanan" class="{{ $periode === 'harian' ? 'hidden' : '' }}">
                <label class="mb-1 block text-xs font-medium text-gray-500">Bulan</label>
                <input type="month" name="bulan" value="{{ $bulan }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
            </div>
            <div class="w-full sm:w-48">
                <label class="mb-1 block text-xs font-medium text-gray-500">Urutkan</label>
                <select name="sort" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="terbaru" {{ ($sort ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ ($sort ?? 'terbaru') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                    <option value="penjualan_terbesar" {{ ($sort ?? 'terbaru') == 'penjualan_terbesar' ? 'selected' : '' }}>Penjualan Terbesar</option>
                    <option value="penjualan_terkecil" {{ ($sort ?? 'terbaru') == 'penjualan_terkecil' ? 'selected' : '' }}>Penjualan Terkecil</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">Tampilkan</button>
        </form>
    </div>

    {{-- Kartu Ringkasan --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Transaksi</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $jumlahTransaksi }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Penjualan</p>
            <p class="mt-1 text-xl font-bold text-gray-800">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">HPP</p>
            <p class="mt-1 text-xl font-bold text-gray-600">Rp {{ number_format($totalHpp, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Laba Kotor</p>
            <p class="mt-1 text-xl font-bold {{ $labaKotor >= 0 ? 'text-green-600' : 'text-red-600' }}">
                Rp {{ number_format($labaKotor, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Tabel Transaksi --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600">Waktu</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">No. Nota</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Kasir</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Item</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Penjualan</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">HPP</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Laba</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $trx)
                @php $laba = $trx->total_penjualan - $trx->total_hpp; @endphp
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $trx->tanggal_waktu->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-primary-600">{{ $trx->nomor_nota }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $trx->user->username ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">
                        @foreach($trx->details as $d)
                            {{ $d->product->nama_barang }} ({{ $d->qty }}x)<br>
                        @endforeach
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($trx->total_penjualan, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-gray-400">Rp {{ number_format($trx->total_hpp, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $laba >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($laba, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">Tidak ada transaksi pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePeriode(val) {
        document.getElementById('filter-harian').classList.toggle('hidden', val === 'bulanan');
        document.getElementById('filter-bulanan').classList.toggle('hidden', val === 'harian');
    }
</script>
@endpush

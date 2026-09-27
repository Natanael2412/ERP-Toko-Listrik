@extends('layouts.app')
@section('title', 'Dashboard Owner')

@section('content')
<div class="space-y-6">
    {{-- Greeting --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Owner</h1>
        <p class="text-sm text-gray-500">Overview keuangan toko — {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Baris 1: Ringkasan Hari Ini --}}
    <div>
        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-400">Hari Ini</h3>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">Penjualan</p>
                <p class="mt-1 text-lg font-bold text-gray-800 sm:text-xl">Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">Laba Kotor</p>
                <p class="mt-1 text-lg font-bold sm:text-xl {{ $labaKotorHariIni >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($labaKotorHariIni, 0, ',', '.') }}
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">Jumlah Transaksi</p>
                <p class="mt-1 text-lg font-bold text-gray-800 sm:text-xl">{{ $jumlahTransaksiHariIni }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">Rata-rata/Transaksi</p>
                <p class="mt-1 text-lg font-bold text-gray-800 sm:text-xl">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">Margin</p>
                <p class="mt-1 text-lg font-bold text-gray-800 sm:text-xl">
                    {{ $penjualanHariIni > 0 ? number_format(($labaKotorHariIni / $penjualanHariIni) * 100, 1) : 0 }}%
                </p>
            </div>
        </div>
    </div>

    {{-- Baris 2: Ringkasan Bulan Ini --}}
    <div>
        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-400">Bulan Ini</h3>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">Penjualan</p>
                <p class="mt-1 text-lg font-bold text-gray-800">Rp {{ number_format($penjualanBulanIni, 0, ',', '.') }}</p>
                <div class="mt-1 flex items-center gap-1 text-xs font-semibold {{ $growthPenjualan >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    @if($growthPenjualan >= 0)
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    @else
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    @endif
                    {{ number_format(abs($growthPenjualan), 1) }}%
                </div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">Laba Kotor</p>
                <p class="mt-1 text-lg font-bold {{ $labaKotorBulanIni >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($labaKotorBulanIni, 0, ',', '.') }}
                </p>
                <div class="mt-1 flex items-center gap-1 text-xs font-semibold {{ $growthLaba >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    @if($growthLaba >= 0)
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    @else
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    @endif
                    {{ number_format(abs($growthLaba), 1) }}%
                </div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">Transaksi</p>
                <p class="mt-1 text-lg font-bold text-gray-800">{{ $jumlahTransaksiBulanIni }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">Margin</p>
                <p class="mt-1 text-lg font-bold text-gray-800">
                    {{ $penjualanBulanIni > 0 ? number_format(($labaKotorBulanIni / $penjualanBulanIni) * 100, 1) : 0 }}%
                </p>
                <p class="mt-1 text-[10px] text-gray-400">Bulan lalu: {{ $penjualanBulanLalu > 0 ? number_format(($labaKotorBulanLalu / $penjualanBulanLalu) * 100, 1) : 0 }}%</p>
            </div>
            <div class="rounded-xl border {{ $jumlahReturBulanIni > 0 ? 'border-orange-200' : 'border-gray-200' }} bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">Retur</p>
                <p class="mt-1 text-lg font-bold {{ $jumlahReturBulanIni > 0 ? 'text-orange-600' : 'text-gray-800' }}">{{ $jumlahReturBulanIni }} item</p>
                <p class="mt-1 text-[10px] text-gray-400">Rp {{ number_format($totalReturBulanIni, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border {{ $utangJatuhTempo > 0 ? 'border-red-200 bg-red-50' : 'border-gray-200' }} bg-white p-4 shadow-sm">
                <p class="text-xs {{ $utangJatuhTempo > 0 ? 'text-red-500' : 'text-gray-500' }}">Utang Jatuh Tempo</p>
                <p class="mt-1 text-lg font-bold {{ $utangJatuhTempo > 0 ? 'text-red-700' : 'text-gray-800' }}">Rp {{ number_format($utangJatuhTempo, 0, ',', '.') }}</p>
                <p class="mt-1 text-[10px] text-gray-400">Total: Rp {{ number_format($totalUtang, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Grafik kiri-kanan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-600 mb-4">Penjualan & Laba (6 Bulan)</h3>
            <div class="relative h-64 w-full">
                <canvas id="dashboardChart"></canvas>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-600 mb-4">Laba Bersih 5 Produk Teratas</h3>
            <div class="relative h-64 w-full flex justify-center">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Baris bawah: Tabel + Sidebar --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            {{-- Tabel Produk Terlaris --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-600">5 Produk Terlaris</h3>
                    <a href="{{ route('reports.sales') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Lihat Laporan &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 sm:px-6 font-semibold text-gray-500">Produk</th>
                                <th class="px-4 py-3 sm:px-6 text-right font-semibold text-gray-500">Terjual</th>
                                <th class="px-4 py-3 sm:px-6 text-right font-semibold text-gray-500 hidden sm:table-cell">Laba Bersih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($topProducts as $tp)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 sm:px-6 font-medium text-gray-800">{{ $tp->nama_barang }}</td>
                                <td class="px-4 py-3 sm:px-6 text-right text-gray-600">{{ number_format($tp->total_terjual, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 sm:px-6 text-right font-semibold text-green-600 hidden sm:table-cell">Rp {{ number_format($tp->total_laba, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 sm:px-6 text-center text-gray-400">Belum ada penjualan bulan ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="space-y-4">
            {{-- Quick Stats sidebar --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Produk</p>
                        <p class="text-xl font-bold text-gray-800">{{ $totalProduk }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border {{ $stokRendah > 0 ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white' }} p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $stokRendah > 0 ? 'bg-red-100 text-red-600' : 'bg-orange-50 text-orange-500' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs {{ $stokRendah > 0 ? 'text-red-500' : 'text-gray-500' }}">Stok Rendah</p>
                        <p class="text-xl font-bold {{ $stokRendah > 0 ? 'text-red-700' : 'text-gray-800' }}">{{ $stokRendah }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Utang</p>
                        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalUtang, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Bar Chart: Penjualan & Laba 6 Bulan Terakhir ---
        const ctxBar = document.getElementById('dashboardChart').getContext('2d');
        const chartData = @json($chartData->reverse()->values()); 
        
        const labelsBar = chartData.map(data => data.month);
        const penjualanData = chartData.map(data => data.penjualan);
        const labaData = chartData.map(data => data.laba);

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labelsBar,
                datasets: [
                    {
                        label: 'Penjualan',
                        data: penjualanData,
                        backgroundColor: '#2563eb',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Laba Kotor',
                        data: labaData,
                        backgroundColor: '#16a34a',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.dataset.label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(ctx.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) {
                                if (v >= 1000000) return 'Rp ' + (v / 1000000) + ' Jt';
                                if (v >= 1000) return 'Rp ' + (v / 1000) + ' Rb';
                                return 'Rp ' + v;
                            }
                        }
                    }
                },
                interaction: { intersect: false, mode: 'index' },
            }
        });

        // --- Doughnut Chart: Top 5 Products by Profit ---
        const ctxDoughnut = document.getElementById('topProductsChart').getContext('2d');
        const topProductsData = @json($topProducts);
        
        const labelsDoughnut = topProductsData.map(d => d.nama_barang);
        const revenueData = topProductsData.map(d => d.total_laba);

        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: labelsDoughnut,
                datasets: [{
                    data: revenueData,
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(ctx.parsed);
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endpush

@extends('layouts.app')
@section('title', 'Utang Usaha')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Utang Usaha</h1>
        <p class="text-sm text-gray-500">Kelola hutang ke supplier dan pembayaran cicilan</p>
    </div>

    {{-- Kartu Ringkasan --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Utang Aktif</p>
            <p class="mt-1 text-2xl font-bold text-red-600">Rp {{ number_format($totalUtang, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $jumlahFaktur }} faktur belum lunas</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Jatuh Tempo / Lewat</p>
            <p class="mt-1 text-2xl font-bold {{ $totalJatuhTempo > 0 ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($totalJatuhTempo, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $totalJatuhTempo > 0 ? 'Segera lakukan pembayaran!' : 'Tidak ada yang jatuh tempo' }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Status</p>
            <div class="mt-2 flex gap-2">
                <a href="{{ route('debts.index', ['status' => 'belum_lunas']) }}" 
                   class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $status === 'belum_lunas' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Belum Lunas</a>
                <a href="{{ route('debts.index', ['status' => 'lunas']) }}"
                   class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $status === 'lunas' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Lunas</a>
                <a href="{{ route('debts.index', ['status' => 'semua']) }}"
                   class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $status === 'semua' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Semua</a>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('debts.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-500">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Faktur atau Nama Supplier..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
            <div class="w-full sm:w-48">
                <label class="mb-1 block text-xs font-medium text-gray-500">Urutkan</label>
                <select name="sort" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="jatuh_tempo" {{ request('sort', 'jatuh_tempo') == 'jatuh_tempo' ? 'selected' : '' }}>Jatuh Tempo</option>
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="hutang_terbesar" {{ request('sort') == 'hutang_terbesar' ? 'selected' : '' }}>Sisa Hutang Terbesar</option>
                    <option value="hutang_terkecil" {{ request('sort') == 'hutang_terkecil' ? 'selected' : '' }}>Sisa Hutang Terkecil</option>
                    <option value="total_terbesar" {{ request('sort') == 'total_terbesar' ? 'selected' : '' }}>Total Beli Terbesar</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">Filter</button>
            <a href="{{ route('debts.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-center text-sm text-gray-600 transition hover:bg-gray-50">Reset</a>
        </form>
    </div>

    {{-- Ringkasan per Supplier --}}
    @if($status === 'belum_lunas' && $debtsBySupplier->isNotEmpty())
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Ringkasan Hutang per Supplier</h3>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($debtsBySupplier as $ds)
            <div class="border border-gray-200 rounded-lg p-4 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-gray-800">{{ $ds->nama_supplier }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ $ds->jumlah_faktur }} Faktur Belum Lunas</p>
                    <p class="text-lg font-bold text-red-600 mt-2">Rp {{ number_format($ds->total_hutang, 0, ',', '.') }}</p>
                </div>
                <form action="{{ route('debts.paySupplier') }}" method="POST" class="mt-4 border-t border-gray-100 pt-3">
                    @csrf
                    <input type="hidden" name="nama_supplier" value="{{ $ds->nama_supplier }}">
                    <input type="hidden" name="tanggal_bayar" value="{{ date('Y-m-d') }}">
                    <button type="submit" onclick="return confirm('Lunasi semua utang (Rp {{ number_format($ds->total_hutang, 0, ',', '.') }}) ke {{ $ds->nama_supplier }}?')" 
                        class="w-full rounded-lg bg-primary-600 px-3 py-2 text-xs font-semibold text-white shadow transition hover:bg-primary-700">
                        Lunasi Semua
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabel Faktur --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600">No. Faktur</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Supplier</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Produk</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total Beli</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Sudah Bayar</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Sisa Hutang</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Jatuh Tempo</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($debts as $debt)
                @php 
                    $jatuhTempo = $debt->isJatuhTempo();
                    $sudahBayar = $debt->total_beli - $debt->sisa_hutang;
                @endphp
                <tr class="transition hover:bg-gray-50 {{ $jatuhTempo ? 'bg-red-50/50' : '' }}">
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-800">{{ $debt->nomor_faktur }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $debt->nama_supplier }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $debt->product->nama_barang }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($debt->total_beli, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-green-600">Rp {{ number_format($sudahBayar, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $debt->sisa_hutang > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rp {{ number_format($debt->sisa_hutang, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-xs {{ $jatuhTempo ? 'font-bold text-red-600' : 'text-gray-500' }}">
                        {{ $debt->tanggal_jatuh_tempo->format('d/m/Y') }}
                        @if($jatuhTempo) <span class="block text-red-500">⚠️ Lewat!</span> @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($debt->status_bayar === 'lunas')
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">Lunas</span>
                        @else
                            <span class="inline-flex rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-700">Belum Lunas</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('debts.show', $debt) }}" class="inline-flex items-center gap-1 rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-gray-400">Tidak ada data utang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $debts->links() }}
</div>
@endsection

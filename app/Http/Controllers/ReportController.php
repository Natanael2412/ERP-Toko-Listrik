<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\ReturnItem;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Laporan Penjualan Harian/Bulanan.
     */
    public function sales(Request $request)
    {
        $periode = $request->input('periode', 'harian');
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $bulan = $request->input('bulan', date('Y-m'));

        $sort = $request->input('sort', 'terbaru');

        if ($periode === 'harian') {
            $query = Transaction::with(['details.product', 'user'])
                ->whereDate('tanggal_waktu', $tanggal);
        } else {
            [$tahun, $bln] = explode('-', $bulan);
            $query = Transaction::with(['details.product', 'user'])
                ->whereMonth('tanggal_waktu', $bln)
                ->whereYear('tanggal_waktu', $tahun);
        }

        switch ($sort) {
            case 'terlama':
                $query->orderBy('tanggal_waktu');
                break;
            case 'penjualan_terbesar':
                $query->orderByDesc('total_penjualan');
                break;
            case 'penjualan_terkecil':
                $query->orderBy('total_penjualan');
                break;
            case 'terbaru':
            default:
                $query->orderByDesc('tanggal_waktu');
                break;
        }

        $transactions = $query->get();

        $totalPenjualan = $transactions->sum('total_penjualan');
        $totalHpp = $transactions->sum('total_hpp');
        $labaKotor = $totalPenjualan - $totalHpp;
        $jumlahTransaksi = $transactions->count();

        return view('reports.sales', compact(
            'periode', 'tanggal', 'bulan', 'transactions',
            'totalPenjualan', 'totalHpp', 'labaKotor', 'jumlahTransaksi', 'sort'
        ));
    }

    /**
     * Kartu Stok per Produk — riwayat mutasi masuk/keluar.
     */
    public function stockCard(Request $request)
    {
        $products = Product::orderBy('nama_barang')->get();
        $selectedProduct = null;
        $mutations = collect();
        $overview = [];

        if ($productId = $request->input('product_id')) {
            $selectedProduct = Product::findOrFail($productId);

            // Barang masuk (dari pembelian)
            $masuk = Purchase::where('product_id', $productId)
                ->select(
                    DB::raw("'masuk' as tipe"),
                    'tanggal_masuk as tanggal',
                    'qty_masuk as qty',
                    'harga_beli as harga',
                    DB::raw("CONCAT('Beli dari ', nama_supplier, ' (', nomor_faktur, ')') as keterangan")
                )
                ->get()
                ->map(fn($item) => [
                    'tipe' => 'masuk',
                    'tanggal' => $item->tanggal,
                    'qty' => $item->qty,
                    'harga' => $item->harga,
                    'keterangan' => $item->keterangan,
                ]);

            // Barang keluar (dari penjualan)
            $keluar = TransactionDetail::where('product_id', $productId)
                ->with('transaction')
                ->get()
                ->map(fn($item) => [
                    'tipe' => 'keluar',
                    'tanggal' => $item->transaction->tanggal_waktu,
                    'qty' => $item->qty,
                    'harga' => $item->harga_satuan,
                    'keterangan' => 'Jual — ' . $item->transaction->nomor_nota,
                ]);

            // Retur masuk (barang kembali dari customer)
            $retur = ReturnItem::whereHas('transactionDetail', fn($q) => $q->where('product_id', $productId))
                ->with('transactionDetail.transaction')
                ->get()
                ->map(fn($item) => [
                    'tipe' => 'retur_masuk',
                    'tanggal' => $item->created_at,
                    'qty' => $item->qty_retur,
                    'harga' => $item->transactionDetail->harga_satuan,
                    'keterangan' => 'Retur — ' . $item->transactionDetail->transaction->nomor_nota,
                ]);

            $mutations = $masuk->concat($keluar)->concat($retur)
                ->sortBy('tanggal')
                ->values();
        } else {
            // Data overview jika tidak ada produk yang dipilih
            // Stok Rendah (di bawah batas minimum)
            $overview['low_stock'] = Product::whereColumn('stok', '<=', 'stok_minimum')
                ->where('stok', '>', 0)
                ->orderBy('stok')
                ->get();
            
            // Stok Habis
            $overview['out_of_stock'] = Product::where('stok', '<=', 0)
                ->get();

            // Stok Mati / Kurang Laku (Stok banyak tapi penjualan sebulan terakhir minim)
            $sebulanLalu = now()->subDays(30);
            $overview['dead_stock'] = Product::where('stok', '>', 10)
                ->withSum(['transactionDetails as terjual_sebulan' => function($q) use ($sebulanLalu) {
                    $q->whereHas('transaction', fn($t) => $t->where('tanggal_waktu', '>=', $sebulanLalu));
                }], 'qty')
                ->get()
                ->filter(fn($p) => ($p->terjual_sebulan ?? 0) <= 5)
                ->sortByDesc('stok')
                ->take(10)
                ->values();
        }

        return view('reports.stock-card', compact('products', 'selectedProduct', 'mutations', 'overview'));
    }

    /**
     * Laporan Laba Rugi sederhana.
     */
    public function profitLoss(Request $request)
    {
        $bulan = $request->input('bulan', date('Y-m'));
        [$tahun, $bln] = explode('-', $bulan);

        // Pendapatan
        $pendapatanPenjualan = Transaction::whereMonth('tanggal_waktu', $bln)
            ->whereYear('tanggal_waktu', $tahun)
            ->sum('total_penjualan');

        // HPP (Harga Pokok Penjualan)
        $hpp = Transaction::whereMonth('tanggal_waktu', $bln)
            ->whereYear('tanggal_waktu', $tahun)
            ->sum('total_hpp');

        // Retur (pengurang pendapatan)
        $totalRetur = ReturnItem::whereMonth('created_at', $bln)
            ->whereYear('created_at', $tahun)
            ->sum('jumlah_refund');

        // Laba Kotor
        $labaKotor = $pendapatanPenjualan - $hpp - $totalRetur;

        // Data tambahan untuk detail
        $jumlahTransaksi = Transaction::whereMonth('tanggal_waktu', $bln)
            ->whereYear('tanggal_waktu', $tahun)
            ->count();

        $jumlahRetur = ReturnItem::whereMonth('created_at', $bln)
            ->whereYear('created_at', $tahun)
            ->count();

        return view('reports.profit-loss', compact(
            'bulan', 'pendapatanPenjualan', 'hpp', 'totalRetur',
            'labaKotor', 'jumlahTransaksi', 'jumlahRetur'
        ));
    }

    /**
     * Laporan Neraca (Balance Sheet) sederhana.
     */
    public function balanceSheet(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // Aset Lancar
        // Persediaan (Total HPP barang yang masih ada di stok)
        $persediaan = Product::all()->sum(function($product) {
            // Asumsi nilai persediaan = stok * harga beli terakhir (atau average, disederhanakan)
            $lastPurchase = Purchase::where('product_id', $product->id)->orderByDesc('tanggal_masuk')->first();
            $hargaModal = $lastPurchase ? $lastPurchase->harga_beli : 0;
            return $product->stok * $hargaModal;
        });

        // Kas (Total Penjualan - Total Pembelian yang Lunas)
        $totalPenjualan = Transaction::whereDate('tanggal_waktu', '<=', $tanggal)->sum('total_penjualan');
        $totalPembelianLunas = Purchase::whereDate('tanggal_masuk', '<=', $tanggal)->where('status_bayar', 'lunas')->sum('total_harga');
        // Hutang yang sudah dibayar (Purchase lunas, atau sebagian)
        // Disederhanakan: Kas masuk dari penjualan, kas keluar dari pembelian lunas + pembayaran cicilan utang
        // Untuk sistem POS sederhana, kas = penjualan bersih
        $kas = $totalPenjualan - $totalPembelianLunas; // Sederhana

        $totalAset = $kas + $persediaan;

        // Kewajiban (Hutang Usaha yang belum lunas)
        $hutangUsaha = Purchase::whereDate('tanggal_masuk', '<=', $tanggal)->sum('sisa_hutang');

        // Modal (Ekuitas) = Total Aset - Kewajiban
        $modal = $totalAset - $hutangUsaha;

        return view('reports.balance-sheet', compact(
            'tanggal', 'persediaan', 'kas', 'totalAset', 'hutangUsaha', 'modal'
        ));
    }

    /**
     * Audit Log — Owner Only.
     */
    public function auditLog(Request $request)
    {
        $query = AuditLog::with('user')->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('event', 'like', "%{$search}%")
                  ->orWhere('auditable_type', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('username', 'like', "%{$search}%"));
            });
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('reports.audit-log', compact('logs'));
    }
}

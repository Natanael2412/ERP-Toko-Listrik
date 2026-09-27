<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Purchase;
use App\Models\ReturnItem;

class DashboardController extends Controller
{
    /**
     * Redirect ke dashboard sesuai role user.
     */
    public function index()
    {
        $user = auth()->user();

        return match ($user->role) {
            'owner' => $this->ownerDashboard(),
            'admin' => $this->adminDashboard(),
            'kasir' => $this->kasirDashboard(),
        };
    }

    /**
     * Dashboard Kasir: ringkasan transaksi hari ini.
     */
    private function kasirDashboard()
    {
        $today = now()->toDateString();

        $transaksiHariIni = Transaction::where('user_id', auth()->id())
            ->whereDate('tanggal_waktu', $today)
            ->count();

        $totalPenjualanHariIni = Transaction::where('user_id', auth()->id())
            ->whereDate('tanggal_waktu', $today)
            ->sum('total_penjualan');

        return view('dashboard.kasir', compact('transaksiHariIni', 'totalPenjualanHariIni'));
    }

    /**
     * Dashboard Admin: ringkasan stok & penjualan.
     */
    private function adminDashboard()
    {
        $today = now()->toDateString();

        $totalProduk = Product::count();
        $stokRendah = Product::whereColumn('stok', '<=', 'stok_minimum')->count();

        $transaksiHariIni = Transaction::whereDate('tanggal_waktu', $today)->count();
        $totalPenjualanHariIni = Transaction::whereDate('tanggal_waktu', $today)->sum('total_penjualan');

        return view('dashboard.admin', compact(
            'totalProduk', 'stokRendah', 'transaksiHariIni', 'totalPenjualanHariIni'
        ));
    }

    /**
     * Dashboard Owner: overview keuangan lengkap.
     */
    private function ownerDashboard()
    {
        $today = now()->toDateString();
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $bulanLaluDate = now()->subMonth();
        $bulanLalu = $bulanLaluDate->month;
        $tahunLalu = $bulanLaluDate->year;

        $totalProduk = Product::count();
        $stokRendah = Product::whereColumn('stok', '<=', 'stok_minimum')->count();

        // Penjualan hari ini
        $penjualanHariIni = Transaction::whereDate('tanggal_waktu', $today)->sum('total_penjualan');
        $hppHariIni = Transaction::whereDate('tanggal_waktu', $today)->sum('total_hpp');
        $labaKotorHariIni = $penjualanHariIni - $hppHariIni;
        $jumlahTransaksiHariIni = Transaction::whereDate('tanggal_waktu', $today)->count();
        $rataRataTransaksi = $jumlahTransaksiHariIni > 0 ? $penjualanHariIni / $jumlahTransaksiHariIni : 0;

        // Penjualan bulan ini
        $penjualanBulanIni = Transaction::whereMonth('tanggal_waktu', $bulanIni)
            ->whereYear('tanggal_waktu', $tahunIni)
            ->sum('total_penjualan');
        $hppBulanIni = Transaction::whereMonth('tanggal_waktu', $bulanIni)
            ->whereYear('tanggal_waktu', $tahunIni)
            ->sum('total_hpp');
        $labaKotorBulanIni = $penjualanBulanIni - $hppBulanIni;

        // Penjualan bulan lalu (MoM)
        $penjualanBulanLalu = Transaction::whereMonth('tanggal_waktu', $bulanLalu)
            ->whereYear('tanggal_waktu', $tahunLalu)
            ->sum('total_penjualan');
        $hppBulanLalu = Transaction::whereMonth('tanggal_waktu', $bulanLalu)
            ->whereYear('tanggal_waktu', $tahunLalu)
            ->sum('total_hpp');
        $labaKotorBulanLalu = $penjualanBulanLalu - $hppBulanLalu;

        // Hitung persentase kenaikan/penurunan (Growth)
        $growthPenjualan = $penjualanBulanLalu > 0 ? (($penjualanBulanIni - $penjualanBulanLalu) / $penjualanBulanLalu) * 100 : ($penjualanBulanIni > 0 ? 100 : 0);
        $growthLaba = $labaKotorBulanLalu > 0 ? (($labaKotorBulanIni - $labaKotorBulanLalu) / $labaKotorBulanLalu) * 100 : ($labaKotorBulanIni > 0 ? 100 : 0);

        // Retur bulan ini
        $totalReturBulanIni = ReturnItem::whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->sum('jumlah_refund');
        $jumlahReturBulanIni = ReturnItem::whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->count();

        // Jumlah transaksi bulan ini
        $jumlahTransaksiBulanIni = Transaction::whereMonth('tanggal_waktu', $bulanIni)
            ->whereYear('tanggal_waktu', $tahunIni)
            ->count();

        // Utang usaha
        $totalUtang = Purchase::where('status_bayar', 'belum_lunas')->sum('sisa_hutang');
        $utangJatuhTempo = Purchase::where('status_bayar', 'belum_lunas')
            ->whereDate('tanggal_jatuh_tempo', '<=', $today)
            ->sum('sisa_hutang');

        // Top 5 Produk Terlaris (Berdasarkan Laba Bersih) Bulan Ini
        $topProducts = \Illuminate\Support\Facades\DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereMonth('transactions.tanggal_waktu', $bulanIni)
            ->whereYear('transactions.tanggal_waktu', $tahunIni)
            ->select(
                'products.nama_barang', 
                \Illuminate\Support\Facades\DB::raw('SUM(transaction_details.qty) as total_terjual'), 
                \Illuminate\Support\Facades\DB::raw('SUM(transaction_details.subtotal - (transaction_details.qty * products.hpp)) as total_laba')
            )
            ->groupBy('products.id', 'products.nama_barang')
            ->orderByDesc('total_laba')
            ->limit(5)
            ->get();

        // Data Grafik (6 Bulan Terakhir)
        $chartData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $penjualan = Transaction::whereMonth('tanggal_waktu', $date->month)
                ->whereYear('tanggal_waktu', $date->year)
                ->sum('total_penjualan');
            $hpp = Transaction::whereMonth('tanggal_waktu', $date->month)
                ->whereYear('tanggal_waktu', $date->year)
                ->sum('total_hpp');
            
            $chartData->push([
                'month' => $date->translatedFormat('M Y'),
                'penjualan' => $penjualan,
                'laba' => $penjualan - $hpp
            ]);
        }

        return view('dashboard.owner', compact(
            'totalProduk', 'stokRendah',
            'penjualanHariIni', 'labaKotorHariIni',
            'jumlahTransaksiHariIni', 'rataRataTransaksi',
            'penjualanBulanIni', 'labaKotorBulanIni',
            'penjualanBulanLalu', 'labaKotorBulanLalu',
            'growthPenjualan', 'growthLaba',
            'totalReturBulanIni', 'jumlahReturBulanIni', 'jumlahTransaksiBulanIni',
            'totalUtang', 'utangJatuhTempo',
            'topProducts', 'chartData'
        ));
    }
}

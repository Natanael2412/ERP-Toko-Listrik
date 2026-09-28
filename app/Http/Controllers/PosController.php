<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    use Auditable;

    /**
     * Tampilkan halaman POS.
     */
    public function index()
    {
        $products = Product::with('category')
            ->where('stok', '>', 0)
            ->orderBy('nama_barang')
            ->get();
            
        $categories = \App\Models\Category::orderBy('nama_kategori')->get();

        return view('pos.index', compact('products', 'categories'));
    }

    /**
     * API: Cari produk (untuk pencarian dinamis).
     */
    public function searchProducts(Request $request)
    {
        $search = $request->input('q', '');

        $products = Product::with('category')
            ->where('stok', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('nama_barang', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->orderBy('nama_barang')
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    /**
     * Proses transaksi penjualan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items'       => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'diskon'      => 'nullable|numeric|min:0',
            'tipe_diskon' => 'nullable|in:nominal,persen',
            'metode_pembayaran' => 'required|in:Cash,Transfer',
        ]);

        $items = $request->input('items');
        $nilaiDiskon = (float) $request->input('diskon', 0);
        $tipeDiskon = $request->input('tipe_diskon', 'nominal');
        $metodePembayaran = $request->input('metode_pembayaran', 'Cash');

        DB::beginTransaction();

        try {
            $totalHpp = 0;
            $subtotal = 0;
            $detailsData = [];

            // Validasi stok dan hitung total
            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                $qty = (float) $item['qty'];

                if ($product->stok < $qty) {
                    DB::rollBack();
                    return back()->with('error', "Stok {$product->nama_barang} tidak mencukupi. Tersisa: {$product->stok}");
                }

                $hargaSatuan = $product->harga_jual;
                $itemSubtotal = $hargaSatuan * $qty;
                $itemHpp = $product->hpp * $qty;

                $subtotal += $itemSubtotal;
                $totalHpp += $itemHpp;

                $detailsData[] = [
                    'product'      => $product,
                    'qty'          => $qty,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal'     => $itemSubtotal,
                ];
            }

            // Hitung nilai nominal diskon jika tipe diskon persen
            $diskonNominal = $tipeDiskon === 'persen' ? ($subtotal * $nilaiDiskon / 100) : $nilaiDiskon;
            $totalPenjualan = $subtotal - $diskonNominal;

            // Generate nomor nota: INV-YYYYMMDD-XXXX
            $today = now()->format('Ymd');
            $lastNota = Transaction::where('nomor_nota', 'like', "INV-{$today}-%")
                ->orderByDesc('nomor_nota')
                ->first();

            $sequence = 1;
            if ($lastNota) {
                $lastSeq = (int) substr($lastNota->nomor_nota, -4);
                $sequence = $lastSeq + 1;
            }
            $nomorNota = "INV-{$today}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Buat transaksi
            $transaction = Transaction::create([
                'user_id'         => auth()->id(),
                'nomor_nota'      => $nomorNota,
                'tanggal_waktu'   => now(),
                'total_hpp'       => $totalHpp,
                'subtotal'        => $subtotal,
                'diskon'          => $diskonNominal,
                'total_penjualan' => $totalPenjualan,
                'metode_pembayaran' => $metodePembayaran,
                'tipe_diskon'     => $tipeDiskon,
            ]);

            // Buat detail & kurangi stok
            foreach ($detailsData as $detail) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $detail['product']->id,
                    'qty'            => $detail['qty'],
                    'harga_satuan'   => $detail['harga_satuan'],
                    'subtotal'       => $detail['subtotal'],
                ]);

                $detail['product']->decrement('stok', $detail['qty']);
            }

            DB::commit();

            $this->catatAudit('Transaksi Penjualan', "Nota: {$nomorNota}, Total: Rp " . number_format($totalPenjualan, 0, ',', '.'));

            return redirect()->route('pos.receipt', $transaction)->with('success', 'Transaksi berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan struk/nota transaksi.
     */
    public function receipt(Transaction $transaction)
    {
        $transaction->load('details.product', 'user');
        return view('pos.receipt', compact('transaction'));
    }

    /**
     * Void transaksi (Hanya Admin & Owner).
     */
    public function voidTransaction(Transaction $transaction)
    {
        if ($transaction->status === 'void') {
            return back()->with('error', 'Transaksi ini sudah di-void sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Ubah status
            $transaction->update(['status' => 'void']);

            // Kembalikan stok produk
            foreach ($transaction->details as $detail) {
                if ($detail->product) {
                    $detail->product->increment('stok', $detail->qty);
                }
            }

            DB::commit();

            $this->catatAudit('Void Transaksi', "Void Nota: {$transaction->nomor_nota}, Total: Rp " . number_format($transaction->total_penjualan, 0, ',', '.'));

            return back()->with('success', "Transaksi {$transaction->nomor_nota} berhasil di-void dan stok telah dikembalikan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mem-void transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat transaksi.
     */
    public function history(Request $request)
    {
        $query = Transaction::with('user');

        // Kasir hanya bisa lihat transaksi sendiri
        if (auth()->user()->isKasir()) {
            $query->where('user_id', auth()->id());
        }

        if ($search = $request->input('search')) {
            $query->where('nomor_nota', 'like', "%{$search}%");
        }

        if ($date = $request->input('tanggal')) {
            $query->whereDate('tanggal_waktu', $date);
        }

        // Sorting
        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $query->orderBy('tanggal_waktu', 'asc');
                break;
            case 'terbesar':
                $query->orderBy('total_penjualan', 'desc');
                break;
            case 'terkecil':
                $query->orderBy('total_penjualan', 'asc');
                break;
            default:
                $query->orderBy('tanggal_waktu', 'desc');
                break;
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('pos.history', compact('transactions', 'sort'));
    }
}

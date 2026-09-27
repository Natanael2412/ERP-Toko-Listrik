<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    use Auditable;

    public function index(Request $request)
    {
        $query = Purchase::with(['product', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_faktur', 'like', "%{$search}%")
                  ->orWhere('nama_supplier', 'like', "%{$search}%")
                  ->orWhereHas('product', fn($p) => $p->where('nama_barang', 'like', "%{$search}%"));
            });
        }

        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $query->orderBy('tanggal_masuk');
                break;
            case 'qty_terbanyak':
                $query->orderByDesc('qty_masuk');
                break;
            case 'total_terbesar':
                $query->orderByDesc('total_beli');
                break;
            case 'terbaru':
            default:
                $query->orderByDesc('tanggal_masuk');
                break;
        }

        $purchases = $query->paginate(20)->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::orderBy('nama_barang')->get();
        $suppliers = \App\Models\Supplier::orderBy('nama_supplier')->get();
        return view('purchases.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'          => 'required|exists:products,id',
            'nomor_faktur'        => 'required|string|max:50',
            'supplier_id'         => 'required|exists:suppliers,id',
            'qty_masuk'           => 'required|numeric|min:0.01',
            'harga_beli'          => 'required|numeric|min:0',
            'tanggal_masuk'       => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_masuk',
            'bukti_faktur'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pathBukti = null;
        if ($request->hasFile('bukti_faktur')) {
            $pathBukti = $request->file('bukti_faktur')->store('faktur', 'public');
        }

        DB::beginTransaction();

        try {
            $product = Product::lockForUpdate()->findOrFail($request->product_id);
            $supplier = \App\Models\Supplier::findOrFail($request->supplier_id);

            $qtyMasuk = (float) $request->qty_masuk;
            $hargaBeli = (float) $request->harga_beli;
            $totalBeli = $qtyMasuk * $hargaBeli;

            // === Hitung HPP Moving Average ===
            $stokLama = $product->stok;
            $hppLama = $product->hpp;

            if ($stokLama + $qtyMasuk > 0) {
                $hppBaru = (($stokLama * $hppLama) + ($qtyMasuk * $hargaBeli)) / ($stokLama + $qtyMasuk);
            } else {
                $hppBaru = $hargaBeli;
            }

            // Update stok & HPP produk
            $product->update([
                'stok' => $stokLama + $qtyMasuk,
                'hpp'  => round($hppBaru, 2),
            ]);

            // Catat pembelian
            Purchase::create([
                'user_id'              => auth()->id(),
                'product_id'           => $request->product_id,
                'supplier_id'          => $request->supplier_id,
                'nomor_faktur'         => $request->nomor_faktur,
                'nama_supplier'        => $supplier->nama_supplier,
                'qty_masuk'            => $qtyMasuk,
                'harga_beli'           => $hargaBeli,
                'total_beli'           => $totalBeli,
                'tanggal_masuk'        => $request->tanggal_masuk,
                'tanggal_jatuh_tempo'  => $request->tanggal_jatuh_tempo,
                'status_bayar'         => 'belum_lunas',
                'sisa_hutang'          => $totalBeli,
                'bukti_faktur'         => $pathBukti,
            ]);

            DB::commit();

            $this->catatAudit('Pembelian/Restock',
                "Produk: {$product->nama_barang}, Qty: {$qtyMasuk}, " .
                "Harga: Rp " . number_format($hargaBeli, 0, ',', '.') . ", " .
                "HPP baru: Rp " . number_format($hppBaru, 0, ',', '.') . ", " .
                "Supplier: {$supplier->nama_supplier}"
            );

            return redirect()->route('purchases.index')
                ->with('success', "Pembelian berhasil dicatat. HPP {$product->nama_barang} diperbarui menjadi Rp " . number_format($hppBaru, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function receipt(Purchase $purchase)
    {
        $purchase->load(['product', 'supplier']);
        return view('purchases.receipt', compact('purchase'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Traits\Auditable;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use Auditable;

    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter pencarian
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filter stok rendah
        if ($request->input('stok_rendah')) {
            $query->whereColumn('stok', '<=', 'stok_minimum');
        }

        // Sorting
        $sort = $request->input('sort', 'nama_asc');
        switch ($sort) {
            case 'nama_desc':
                $query->orderBy('nama_barang', 'desc');
                break;
            case 'stok_terbanyak':
                $query->orderBy('stok', 'desc');
                break;
            case 'stok_sedikit':
                $query->orderBy('stok', 'asc');
                break;
            case 'harga_termahal':
                $query->orderBy('harga_jual', 'desc');
                break;
            case 'harga_termurah':
                $query->orderBy('harga_jual', 'asc');
                break;
            default:
                $query->orderBy('nama_barang', 'asc');
                break;
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('products.index', compact('products', 'categories', 'sort'));
    }

    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (empty($request->sku) && $request->category_id) {
            $category = Category::find($request->category_id);
            if ($category) {
                $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $category->nama_kategori), 0, 3));
                if (strlen($prefix) < 3) {
                    $prefix = str_pad($prefix, 3, 'X');
                }
                
                $lastProduct = Product::where('sku', 'like', $prefix . '-%')
                                      ->orderBy('sku', 'desc')->first();
                                      
                if ($lastProduct && preg_match('/-(\d+)$/', $lastProduct->sku, $matches)) {
                    $nextNum = intval($matches[1]) + 1;
                    $sku = $prefix . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                } else {
                    $sku = $prefix . '-0001';
                }
                $request->merge(['sku' => $sku]);
            }
        }

        $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'sku'          => 'required|string|max:50|unique:products,sku',
            'nama_barang'  => 'required|string|max:100',
            'satuan'       => 'required|string|max:20',
            'stok'         => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
        ]);

        Product::create($request->only([
            'category_id', 'sku', 'nama_barang', 'satuan', 'stok', 'stok_minimum', 'harga_jual',
        ]));

        $this->catatAudit('Tambah Produk', "Menambahkan produk: {$request->nama_barang} (SKU: {$request->sku})");

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'sku'          => 'required|string|max:50|unique:products,sku,' . $product->id,
            'nama_barang'  => 'required|string|max:100',
            'satuan'       => 'required|string|max:20',
            'stok_minimum' => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
        ]);

        $product->update($request->only([
            'category_id', 'sku', 'nama_barang', 'satuan', 'stok_minimum', 'harga_jual',
        ]));

        $this->catatAudit('Edit Produk', "Mengedit produk: {$product->nama_barang} (SKU: {$product->sku})");

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->transactionDetails()->exists() || $product->purchases()->exists()) {
            return back()->with('error', 'Produk tidak bisa dihapus karena sudah pernah digunakan dalam transaksi atau pembelian.');
        }

        $nama = $product->nama_barang;
        $product->delete();

        $this->catatAudit('Hapus Produk', "Menghapus produk: {$nama}");

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}

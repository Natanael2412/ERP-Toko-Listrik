<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\Auditable;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use Auditable;

    public function index()
    {
        $categories = Category::withCount('products')->orderBy('nama_kategori')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:categories,nama_kategori',
        ]);

        Category::create($request->only('nama_kategori'));

        $this->catatAudit('Tambah Kategori', "Menambahkan kategori: {$request->nama_kategori}");

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:categories,nama_kategori,' . $category->id,
        ]);

        $namaLama = $category->nama_kategori;
        $category->update($request->only('nama_kategori'));

        $this->catatAudit('Edit Kategori', "Mengubah kategori '{$namaLama}' menjadi '{$request->nama_kategori}'");

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk.');
        }

        $nama = $category->nama_kategori;
        $category->delete();

        $this->catatAudit('Hapus Kategori', "Menghapus kategori: {$nama}");

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}

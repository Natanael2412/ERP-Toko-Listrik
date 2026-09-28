<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Traits\Auditable;

    protected $fillable = [
        'category_id', 'sku', 'nama_barang', 'satuan', 'stok', 'stok_minimum', 'hpp', 'harga_jual',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected $casts = [
        'stok'         => 'float',
        'stok_minimum' => 'float',
        'hpp'          => 'float',
        'harga_jual'   => 'float',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Cek apakah stok di bawah batas minimum.
     */
    public function isStokRendah(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }

    /**
     * Generate SKU unik
     */
    public static function generateSKU($categoryId)
    {
        $prefix = 'SKU-' . str_pad($categoryId, 2, '0', STR_PAD_LEFT);
        $random = strtoupper(\Illuminate\Support\Str::random(6));
        return $prefix . '-' . $random;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id', 'product_id', 'qty', 'harga_satuan', 'subtotal'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    protected $casts = [
        'qty' => 'float',
        'harga_satuan' => 'float',
        'subtotal' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'nomor_nota', 'tanggal_waktu', 'total_hpp',
        'subtotal', 'diskon', 'total_penjualan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_waktu' => 'datetime',
        ];
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function returns()
    {
        return $this->hasManyThrough(ReturnItem::class, TransactionDetail::class);
    }
}

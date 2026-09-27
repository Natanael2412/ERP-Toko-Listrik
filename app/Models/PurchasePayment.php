<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = [
        'purchase_id', 'user_id', 'jumlah_bayar', 'tanggal_bayar', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bayar' => 'datetime',
        ];
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'transaction_detail_id', 'user_id', 'qty_retur', 'alasan', 'jumlah_refund',
    ];

    public function transactionDetail()
    {
        return $this->belongsTo(TransactionDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

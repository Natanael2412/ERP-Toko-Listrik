<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use \App\Traits\Auditable;

    protected $fillable = [
        'user_id', 'product_id', 'supplier_id', 'nomor_faktur', 'nama_supplier',
        'qty_masuk', 'harga_beli', 'total_beli', 'tanggal_masuk',
        'tanggal_jatuh_tempo', 'status_bayar', 'sisa_hutang', 'bukti_faktur',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'datetime',
            'tanggal_jatuh_tempo' => 'date',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    protected $casts = [
        'qty_masuk' => 'float',
        'harga_beli' => 'float',
        'total_beli' => 'float',
        'sisa_hutang' => 'float',
        'tanggal_masuk' => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    /**
     * Cek apakah faktur ini sudah melewati jatuh tempo.
     */
    public function isJatuhTempo(): bool
    {
        return $this->status_bayar === 'belum_lunas' && $this->tanggal_jatuh_tempo->isPast();
    }
}

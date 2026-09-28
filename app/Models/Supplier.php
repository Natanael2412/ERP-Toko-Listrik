<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use \App\Traits\Auditable;

    protected $fillable = [
        'nama_supplier',
        'kontak',
        'alamat',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}

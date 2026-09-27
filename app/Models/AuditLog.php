<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // Nonaktifkan default timestamps Laravel
    public $timestamps = false; 

    protected $fillable = [
        'user_id', 'aksi', 'detail_perubahan', 'timestamp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokLog extends Model
{
    protected $table = 'stok_log';

    protected $fillable = [
        'obat_id',
        'user_id',
        'tipe',
        'jumlah',
        'keterangan'
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

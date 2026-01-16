<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Obat extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'obat';

    protected $fillable = [
        'kode',
        'nama',
        'kategori_id',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimal',
        'expired_at'
    ];

    protected $casts = [
        'expired_at' => 'date'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class)->withTrashed(); 
    }
}

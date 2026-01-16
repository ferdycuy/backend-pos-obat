<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'total',
        'diskon',
        'bayar',
        'kembali',
        'metode_pembayaran',
        'tanggal'
    ];


    protected $casts = [
        'tanggal' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}

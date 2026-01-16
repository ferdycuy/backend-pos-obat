<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $table = 'transaksi_detail';
    public $timestamps = false;

    protected $fillable = [
        'transaksi_id',
        'obat_id',
        'qty',
        'harga',
        'subtotal'
    ];

    // TAMBAHKAN RELASI INI
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }
}
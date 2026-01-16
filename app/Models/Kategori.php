<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use SoftDeletes;
    protected $table = 'kategori';
    protected $fillable = ['nama'];
    protected $dates = ['deleted_at'];

    public function obat()
    {
        return $this->hasMany(Obat::class);
    }
}

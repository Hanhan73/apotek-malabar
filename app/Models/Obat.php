<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obats';
    protected $fillable = [
    'kode_obat',
    'nama_obat',
    'jenis_obat',
    'stok',
    'harga_jual',
    'harga_beli',
    'kadaluarsa'
    ];

    public function penerimaanItems()
    {
        return $this->hasMany(PenerimaanPembelianDetail::class);
    }

    public function returItems()
    {
        return $this->hasMany(ReturPembelianDetail::class);
    }
}

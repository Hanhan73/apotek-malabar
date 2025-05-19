<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturPembelianDetail extends Model
{
    protected $table = 'retur_pembelian_detail';

    protected $fillable = [
        'retur_pembelian_id',
        'obat_id',
        'jumlah',
        'keterangan',
    ];

    public function returPembelian()
    {
        return $this->belongsTo(ReturPembelian::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}

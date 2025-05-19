<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanPembelianDetail extends Model
{
    protected $table = 'penerimaan_pembelian_detail';

    protected $fillable = [
        'penerimaan_pembelian_id',
        'obat_id',
        'jumlah_diterima',
        'harga_satuan',
    ];

    public function penerimaanPembelian()
    {
        return $this->belongsTo(PenerimaanPembelian::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
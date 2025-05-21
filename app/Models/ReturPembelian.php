<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturPembelian extends Model
{
    protected $table = 'retur_pembelian';

    protected $fillable = [
        'penerimaan_pembelian_id',
        'tanggal_retur',
        'alasan_retur',
        'total_retur',
        'user_id'
    ];

    public function penerimaanPembelian()
    {
        return $this->belongsTo(PenerimaanPembelian::class);
    }

    public function items()
    {
        return $this->hasMany(ReturPembelianDetail::class);
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }

}

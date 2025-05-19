<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPembelian extends Model
{
    protected $table = 'pembayaran_pembelian';

    protected $fillable = [
        'penerimaan_pembelian_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_pembayaran',
        'status',
        'user_id'
    ];

    public function penerimaanPembelian()
    {
        return $this->belongsTo(PenerimaanPembelian::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


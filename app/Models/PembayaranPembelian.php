<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPembelian extends Model
{
    protected $table = 'pembayaran_pembelian';

    protected $fillable = [
        'pembelian_id', // Add this
        'penerimaan_pembelian_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_pembayaran',
        'status',
        'sisa_hutang',
        'catatan',
        'user_id'
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'integer',
        'sisa_hutang' => 'integer'
    ];

    // Direct relationship to Pembelian
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    // Relationship to PenerimaanPembelian
    public function penerimaanPembelian()
    {
        return $this->belongsTo(PenerimaanPembelian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
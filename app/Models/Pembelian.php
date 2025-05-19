<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';
      protected $fillable = [
        'kode_pembelian',
        'tanggal_pembelian',
        'supplier_id',
        'total',
        'status',
        'jenis_pembayaran',
        'user_id'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class);
    }

    public function penerimaan()
    {
        return $this->hasOne(PenerimaanPembelian::class);
    }
    public function isDiterima()
    {
        return $this->penerimaan()->exists();
    }
    public function hitungTotal()
    {
        return $this->detailPembelian->sum('subtotal');
    }

        public function users() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
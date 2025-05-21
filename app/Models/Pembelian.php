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
        'sisa_pembayaran',
        'tanggal_jatuh_tempo',
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
        public function user()
    {
        return $this->belongsTo(User::class);
    }
public function pembayaran()
{
    return $this->hasOne(PembayaranPembelian::class, 'pembelian_id');
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

    public function getRemainingPaymentAttribute()
{
    if ($this->penerimaan) {
        $totalPaid = $this->penerimaan->pembayaran()->sum('jumlah_bayar') ?? 0;
        return $this->total - $totalPaid;
    }
    return $this->total;
}

public function getIsPaidAttribute()
{
    return $this->remaining_payment <= 0;
}
public function getIsOverdueAttribute()
{
    if (!$this->tanggal_jatuh_tempo) {
        return false;
    }
    return !$this->is_paid && Carbon::parse($this->tanggal_jatuh_tempo)->isPast();
}
}
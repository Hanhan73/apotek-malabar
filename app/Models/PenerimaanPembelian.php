<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanPembelian extends Model
{
    use HasFactory;
    
    protected $table = 'penerimaan_pembelian';
    protected $fillable = [
        'pembelian_id',
        'tanggal_penerimaan',
        'user_id',
        'catatan'
    ];
    
    protected $casts = [
        'tanggal_penerimaan' => 'date'
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Method untuk mendapatkan detail barang yang diterima
    public function getDetailBarang()
    {
        return $this->pembelian->detailPembelian;
    }
    
    // Method untuk mendapatkan total nilai barang yang diterima
    public function getTotalNilai()
    {
        return $this->pembelian->total_harga;
    }

    public function items()
    {
        return $this->hasMany(PenerimaanPembelianDetail::class);
    }
        public function detailPenerimaan()
    {
        return $this->hasMany(PenerimaanPembelianDetail::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(PembayaranPembelian::class);
    }

    public function retur()
    {
        return $this->hasMany(ReturPembelian::class);
    }
}
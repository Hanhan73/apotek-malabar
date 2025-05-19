<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;
    
    protected $table = 'detail_pembelian';
    protected $fillable = [
        'pembelian_id',
        'obat_id',
        'jumlah',
        'harga_satuan',
        'subtotal'
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }
    
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
    
    public function hitungSubtotal()
    {
        return $this->jumlah * $this->harga_satuan;
    }
    
    public function updateStokObat()
    {
        $obat = $this->obat;
        $obat->stok += $this->jumlah;
        $obat->save();
        
        return true;
    }
}
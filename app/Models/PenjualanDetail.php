<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjualanDetail extends Model
{
    protected $table = 'penjualan_details';
    
    protected $fillable = [
        'penjualan_id',
        'obat_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    /**
     * Mendapatkan penjualan yang terkait dengan detail ini
     */
    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    /**
     * Mendapatkan data obat dari detail penjualan
     */
    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }
}
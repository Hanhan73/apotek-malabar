<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penjualan extends Model
{
    protected $table = 'penjualans';
    
    protected $fillable = [
        'nomor_nota',
        'tanggal',
        'total_harga',
        'jenis_penjualan',
        'status_pembayaran',
        'user_id',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(PembayaranPenjualan::class, 'id', 'penjualan_id');
    }
}
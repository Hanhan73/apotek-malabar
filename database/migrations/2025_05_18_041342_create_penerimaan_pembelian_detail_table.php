<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('penerimaan_pembelian_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerimaan_pembelian_id')->constrained('penerimaan_pembelian')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('obats')->onDelete('cascade');
            $table->integer('jumlah_diterima');
            $table->integer('harga_satuan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penerimaan_pembelian_detail');
    }
};

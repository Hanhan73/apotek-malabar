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
        Schema::create('pembayaran_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerimaan_pembelian_id')->constrained('penerimaan_pembelian')->onDelete('cascade');
            $table->date('tanggal_bayar');
            $table->integer('jumlah_bayar');
            $table->enum('metode_pembayaran', ['tunai', 'kredit']);
            $table->enum('status', ['lunas', 'belum lunas']);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_pembelian');
    }
};

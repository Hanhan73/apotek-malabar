<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturPembelianTable extends Migration
{
    public function up()
    {
        Schema::create('retur_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerimaan_pembelian_id')->constrained('penerimaan_pembelian')->onDelete('cascade');
            $table->date('tanggal_retur');
            $table->text('alasan_retur')->nullable();
            $table->integer('total_retur')->default(0);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('retur_pembelian');
    }
}


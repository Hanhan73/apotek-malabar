<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('obats', function (Blueprint $table) {
            $table->id();
            $table->string('kode_obat', 10)->unique();
            $table->string('nama_obat');
            $table->enum('jenis_obat', ['bebas', 'herbal', 'psikotropik', 'suplemen', 'bebas_terbatas']);
            $table->integer('stok');
            $table->integer('harga_beli');
            $table->integer('harga_jual');
            $table->datetime('kadaluarsa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obats');
    }
};

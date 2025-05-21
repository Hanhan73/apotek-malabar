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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembelian', 20)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->date('tanggal_pembelian');
            $table->enum('status', ['dipesan', 'dikirim', 'diterima', 'diterima_sebagian', 'diretur'])->default('dipesan');
            $table->enum('status_pembayaran', ['belum_lunas', 'lunas'])->default('belum_lunas'); // Add this line
            $table->enum('jenis_pembayaran', ['tunai', 'kredit']);
            $table->decimal('total', 10, 2);
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('sisa_pembayaran', 12, 2)->default(0);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};

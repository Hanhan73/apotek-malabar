<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->date('tanggal_jatuh_tempo')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->date('tanggal_jatuh_tempo')->nullable(false)->change();
        });
    }
};
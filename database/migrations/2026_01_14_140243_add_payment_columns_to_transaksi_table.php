<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('metode_pembayaran')->default('cash');
            $table->integer('bayar')->default(0);
            $table->integer('kembali')->default(0);
        });
    }

    public function down()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'bayar', 'kembali']);
        });
    }

};

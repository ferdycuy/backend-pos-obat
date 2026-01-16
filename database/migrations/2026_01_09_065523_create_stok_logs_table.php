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
        Schema::create('stok_log', function (Blueprint $table) {
        $table->id();
        $table->foreignId('obat_id')->constrained('obat');
        $table->foreignId('user_id')->constrained('users');
        $table->enum('tipe', ['masuk', 'keluar']);
        $table->integer('jumlah');
        $table->string('keterangan')->nullable();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_logs');
    }
};

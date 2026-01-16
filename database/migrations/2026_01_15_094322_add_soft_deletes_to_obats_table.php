<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->softDeletes(); // Ini akan membuat kolom 'deleted_at'
        });
    }

    public function down()
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

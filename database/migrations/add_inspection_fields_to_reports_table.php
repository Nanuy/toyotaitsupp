<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('merek_tipe')->nullable();
            $table->text('dampak_ditimbulkan')->nullable();
            $table->text('tindakan_dilakukan')->nullable();
            $table->text('rekomendasi_teknis')->nullable();
            $table->text('spesifikasi_pengadaan')->nullable();
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'merek_tipe',
                'dampak_ditimbulkan', 
                'tindakan_dilakukan',
                'rekomendasi_teknis',
                'spesifikasi_pengadaan'
            ]);
        });
    }
};
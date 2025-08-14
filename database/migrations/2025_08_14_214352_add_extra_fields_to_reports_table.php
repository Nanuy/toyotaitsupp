<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('merek_tipe')->nullable()->after('description');
            $table->text('dampak_ditimbulkan')->nullable()->after('merek_tipe');
            $table->text('tindakan_dilakukan')->nullable()->after('dampak_ditimbulkan');
            $table->text('rekomendasi_teknis')->nullable()->after('tindakan_dilakukan');
            $table->text('spesifikasi_pengadaan')->nullable()->after('rekomendasi_teknis');
        });
    }

    public function down(): void
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

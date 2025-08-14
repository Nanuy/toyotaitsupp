<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->string('merek_tipe')->nullable();
            $table->text('dampak_ditimbulkan')->nullable();
            $table->text('tindakan_dilakukan')->nullable();
            $table->text('rekomendasi_teknis')->nullable();
            $table->text('spesifikasi_pengadaan')->nullable();
            $table->enum('status', ['pending', 'completed', 'approved'])->default('pending');
            $table->unsignedBigInteger('inspector_id')->nullable(); // ID teknisi yang melakukan inspeksi
            $table->timestamp('inspection_date')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('inspector_id')->references('id')->on('users')->onDelete('set null');
            
            // Index untuk performa
            $table->index('report_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspections');
    }
};
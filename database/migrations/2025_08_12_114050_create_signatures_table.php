<?php
// database/migrations/2025_08_12_114050_create_signatures_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->unsignedBigInteger('user_id')->nullable(); // null kalau tanda tangan otomatis
            $table->enum('role', ['user', 'it_supp', 'head_it', 'head_dept']); // peran di laporan
            $table->string('signature_path')->nullable(); // path file ttd (bisa hasil upload atau canvas)
            $table->boolean('is_checked')->default(false); // tanda sudah dicek
            $table->boolean('is_auto')->default(false); // true = auto-sign karena timeout
            $table->timestamp('signed_at')->nullable(); // kapan tanda tangan dilakukan
            $table->timestamps();

            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Untuk MySQL, kita perlu mengubah enum dengan ALTER TABLE
        DB::statement("ALTER TABLE signatures MODIFY COLUMN role ENUM('user', 'it_supp', 'head_it', 'head_dept', 'superadmin')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus data superadmin terlebih dahulu sebelum mengubah enum
        DB::table('signatures')->where('role', 'superadmin')->delete();
        
        // Kembalikan enum ke nilai semula
        DB::statement("ALTER TABLE signatures MODIFY COLUMN role ENUM('user', 'it_supp', 'head_it', 'head_dept')");
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom status menjadi VARCHAR agar dapat menampung semua status
        DB::statement("ALTER TABLE proposals MODIFY status VARCHAR(50) DEFAULT 'diajukan'");
    }

    public function down(): void
    {
        //
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('periode')) {
            Schema::create('periode', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai');
                $table->decimal('batas_anggaran', 15, 2)->default(7000000);
                $table->integer('batas_hari_lpj')->default(7);
                $table->boolean('is_aktif')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('periode');
    }
};

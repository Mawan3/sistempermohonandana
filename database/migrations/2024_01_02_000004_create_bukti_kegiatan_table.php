<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bukti_kegiatan')) {
            Schema::create('bukti_kegiatan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lpj_id')->constrained('lpj')->onDelete('cascade');
                $table->string('jenis')->default('foto'); // foto, nota, presensi, sertifikat, dokumen
                $table->string('nama_file');
                $table->string('path_file');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_kegiatan');
    }
};

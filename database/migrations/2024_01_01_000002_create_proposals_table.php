<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ormawa_id')->constrained('ormawa')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul_kegiatan');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_kegiatan');
            $table->string('lokasi')->nullable();
            $table->decimal('anggaran_diajukan', 15, 2)->default(0);
            $table->string('status')->default('diajukan'); // draft, diajukan, perlu_revisi, disetujui_kemahasiswaan, ditolak, dana_cair, selesai
            $table->text('catatan_reviewer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};

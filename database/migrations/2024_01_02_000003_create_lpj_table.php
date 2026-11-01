<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lpj')) {
            Schema::create('lpj', function (Blueprint $table) {
                $table->id();
                $table->foreignId('proposal_id')->constrained('proposals')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('rincian_penggunaan')->nullable();
                $table->decimal('total_realisasi', 15, 2)->default(0);
                $table->string('status', 50)->default('menunggu_review'); // draft, menunggu_review, disetujui, perlu_perbaikan
                $table->timestamp('tanggal_kirim')->nullable();
                $table->text('catatan_reviewer')->nullable();
                $table->string('file_lpj')->nullable();
                $table->timestamp('tanggal_review')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lpj');
    }
};

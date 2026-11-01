<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pencairan')) {
            Schema::create('pencairan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('proposal_id')->constrained('proposals')->onDelete('cascade');
                $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('jumlah_dicairkan', 15, 2)->default(0);
                $table->string('status', 50)->default('selesai');
                $table->date('tanggal_pencairan')->nullable();
                $table->text('catatan')->nullable();
                $table->string('bukti_transfer')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pencairan');
    }
};

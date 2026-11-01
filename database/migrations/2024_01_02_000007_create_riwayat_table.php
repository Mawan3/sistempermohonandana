<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('riwayat')) {
            Schema::create('riwayat', function (Blueprint $table) {
                $table->id();
                $table->foreignId('proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
                $table->foreignId('ormawa_id')->nullable()->constrained('ormawa')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('periode_id')->nullable()->constrained('periode')->nullOnDelete();
                $table->string('aktivitas');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat');
    }
};

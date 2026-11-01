<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('ormawa_id')->nullable()->constrained('ormawa')->nullOnDelete();
                $table->foreignId('proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
                $table->string('judul');
                $table->text('pesan');
                $table->string('tipe', 30)->default('info'); // info, success, warning, danger
                $table->boolean('is_dibaca')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};

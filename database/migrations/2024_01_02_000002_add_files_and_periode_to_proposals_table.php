<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            if (!Schema::hasColumn('proposals', 'file_proposal')) {
                $table->string('file_proposal')->nullable()->after('catatan_reviewer');
            }
            if (!Schema::hasColumn('proposals', 'file_koreksi')) {
                $table->string('file_koreksi')->nullable()->after('file_proposal');
            }
            if (!Schema::hasColumn('proposals', 'periode_id')) {
                $table->foreignId('periode_id')->nullable()->after('file_koreksi')->constrained('periode')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            if (Schema::hasColumn('proposals', 'periode_id')) {
                $table->dropForeign(['periode_id']);
                $table->dropColumn('periode_id');
            }
            if (Schema::hasColumn('proposals', 'file_koreksi')) {
                $table->dropColumn('file_koreksi');
            }
            if (Schema::hasColumn('proposals', 'file_proposal')) {
                $table->dropColumn('file_proposal');
            }
        });
    }
};

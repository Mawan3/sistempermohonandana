<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('pengurus')->after('email');
            }
            if (!Schema::hasColumn('users', 'ormawa_id')) {
                $table->foreignId('ormawa_id')->nullable()->after('role')->constrained('ormawa')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'ormawa_id')) {
                $table->dropForeign(['ormawa_id']);
                $table->dropColumn('ormawa_id');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('empresas') && Schema::hasColumn('empresas', 'password')) {
            Schema::table('empresas', function (Blueprint $table) {
                $table->dropColumn('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('empresas') && !Schema::hasColumn('empresas', 'password')) {
            Schema::table('empresas', function (Blueprint $table) {
                $table->string('password')->nullable()->after('nome');
            });
        }
    }
};
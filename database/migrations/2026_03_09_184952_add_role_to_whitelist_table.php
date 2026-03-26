<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whitelist', function (Blueprint $table) {
            $table->enum('role', ['player', 'captain'])->default('player')->after('iin');
        });
    }

    public function down(): void
    {
        Schema::table('whitelist', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};

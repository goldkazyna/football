<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->enum('stage', ['group', 'playoff'])->default('group');
            $table->string('playoff_round')->nullable();
            $table->foreignId('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->cascadeOnDelete();
            $table->unsignedInteger('home_score')->default(0);
            $table->unsignedInteger('away_score')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->string('venue')->nullable();
            $table->enum('status', ['scheduled', 'live', 'finished'])->default('scheduled');
            $table->timestamps();

            $table->index(['tournament_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};

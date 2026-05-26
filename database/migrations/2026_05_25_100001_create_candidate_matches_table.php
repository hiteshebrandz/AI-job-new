<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_description_id')->constrained('job_descriptions')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('match_score')->default(0);
            $table->text('ai_reason')->nullable();
            $table->json('match_breakdown')->nullable();
            $table->timestamps();

            $table->unique(['job_description_id', 'candidate_id']);
            $table->index(['job_description_id', 'match_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_matches');
    }
};

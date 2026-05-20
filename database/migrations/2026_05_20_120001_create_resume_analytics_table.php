<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resume_id')->constrained('resume_files')->cascadeOnDelete();
            $table->string('candidate_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('current_role')->nullable();
            $table->decimal('total_experience_years', 4, 1)->nullable();
            $table->unsignedSmallInteger('ai_score')->default(0);
            $table->unsignedSmallInteger('top_match_percentage')->default(0);
            $table->unsignedInteger('application_count')->default(0);
            $table->unsignedInteger('skill_count')->default(0);
            $table->json('skills')->nullable();
            $table->json('missing_skills')->nullable();
            $table->json('skill_gap_analysis')->nullable();
            $table->json('career_growth')->nullable();
            $table->json('education')->nullable();
            $table->json('nlp_analysis')->nullable();
            $table->json('soft_skills')->nullable();
            $table->longText('ai_profile_summary')->nullable();
            $table->json('resume_improvements')->nullable();
            $table->json('job_recommendations')->nullable();
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('raw_ai_response')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'resume_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_analytics');
    }
};

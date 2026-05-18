<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('candidate_code', 20)->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('current_title')->nullable();
            $table->unsignedTinyInteger('experience_years')->nullable();
            $table->string('seniority_level')->nullable();
            $table->text('previous_companies')->nullable();
            $table->string('education')->nullable();
            $table->string('university')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->json('skills')->nullable();
            $table->string('resume_path')->nullable();
            $table->text('ai_recommendation')->nullable();
            $table->unsignedTinyInteger('ai_score')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('company_name');
            $table->string('location');
            $table->string('job_type');
            $table->string('experience_required')->nullable();
            $table->text('description')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->text('skills_required')->nullable();
            $table->text('screening_question_1')->nullable();
            $table->text('screening_question_2')->nullable();
            $table->text('screening_question_3')->nullable();
            $table->text('minimum_qualification')->nullable();
            $table->text('preferred_qualification')->nullable();
            $table->string('work_mode')->nullable();
            $table->string('notice_period')->nullable();
            $table->string('salary')->nullable();
            $table->decimal('min_salary', 12, 2)->nullable();
            $table->decimal('max_salary', 12, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->date('application_deadline')->nullable();
            $table->unsignedInteger('number_of_openings')->default(1);
            $table->string('status', 20)->default('inactive');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};

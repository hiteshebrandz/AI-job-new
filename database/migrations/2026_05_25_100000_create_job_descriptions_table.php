<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->longText('jd_content');
            $table->string('source_type', 16)->default('text');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->json('extracted_requirements')->nullable();
            $table->string('status', 32)->default('pending');
            $table->text('analysis_error')->nullable();
            $table->timestamps();

            $table->index(['hr_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_descriptions');
    }
};

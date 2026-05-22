<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_optimizer_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('original_file_name');
            $table->string('original_file_path');
            $table->string('file_type', 10);
            $table->longText('extracted_text')->nullable();
            $table->enum('status', [
                'uploaded',
                'analyzing',
                'analyzed',
                'generating',
                'completed',
                'failed',
            ])->default('uploaded');
            $table->json('analysis_result')->nullable();
            $table->string('generated_file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_optimizer_runs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_parsing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('parsing_status', 20)->default('pending');
            $table->unsignedTinyInteger('ai_score')->nullable();
            $table->json('parsed_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('parsing_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_parsing_logs');
    }
};

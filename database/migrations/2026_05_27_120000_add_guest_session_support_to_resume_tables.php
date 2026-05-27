<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resume_parsing_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('resume_parsing_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('guest_session_id', 64)->nullable()->index()->after('user_id');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('resume_optimizer_runs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('resume_optimizer_runs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('guest_session_id', 64)->nullable()->index()->after('user_id');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('resume_parsing_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('guest_session_id');
        });

        Schema::table('resume_parsing_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('resume_optimizer_runs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('guest_session_id');
        });

        Schema::table('resume_optimizer_runs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};

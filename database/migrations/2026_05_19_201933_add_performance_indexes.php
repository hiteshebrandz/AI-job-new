<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // job_applications
        Schema::table('job_applications', function (Blueprint $table) {
            if (! $this->hasIndex('job_applications', 'job_applications_user_id_index')) {
                $table->index('user_id');
            }
            if (! $this->hasIndex('job_applications', 'job_applications_job_id_index')) {
                $table->index('job_id');
            }
            if (! $this->hasIndex('job_applications', 'job_applications_status_index')) {
                $table->index('status');
            }
            if (! $this->hasIndex('job_applications', 'job_applications_applied_at_index')) {
                $table->index('applied_at');
            }
        });

        // jobs
        Schema::table('jobs', function (Blueprint $table) {
            if (! $this->hasIndex('jobs', 'jobs_hr_id_index')) {
                $table->index('hr_id');
            }
            if (! $this->hasIndex('jobs', 'jobs_status_index')) {
                $table->index('status');
            }
            if (! $this->hasIndex('jobs', 'jobs_status_application_deadline_index')) {
                $table->index(['status', 'application_deadline']);
            }
        });

        // resume_parsing_logs
        Schema::table('resume_parsing_logs', function (Blueprint $table) {
            if (! $this->hasIndex('resume_parsing_logs', 'resume_parsing_logs_user_id_parsing_status_index')) {
                $table->index(['user_id', 'parsing_status']);
            }
        });

        // application_notifications — composite for unread count
        Schema::table('application_notifications', function (Blueprint $table) {
            if (! $this->hasIndex('application_notifications', 'application_notifications_user_id_is_read_index')) {
                $table->index(['user_id', 'is_read']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndexIfExists('job_applications_user_id_index');
            $table->dropIndexIfExists('job_applications_job_id_index');
            $table->dropIndexIfExists('job_applications_status_index');
            $table->dropIndexIfExists('job_applications_applied_at_index');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndexIfExists('jobs_hr_id_index');
            $table->dropIndexIfExists('jobs_status_index');
            $table->dropIndexIfExists('jobs_status_application_deadline_index');
        });

        Schema::table('resume_parsing_logs', function (Blueprint $table) {
            $table->dropIndexIfExists('resume_parsing_logs_user_id_parsing_status_index');
        });

        Schema::table('application_notifications', function (Blueprint $table) {
            $table->dropIndexIfExists('application_notifications_user_id_is_read_index');
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        try {
            $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$index}'");
            return ! empty($indexes);
        } catch (\Throwable) {
            return false;
        }
    }
};

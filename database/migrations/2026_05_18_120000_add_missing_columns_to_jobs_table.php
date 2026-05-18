<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('jobs', 'screening_question_1')) {
                $table->text('screening_question_1')->nullable()->after('skills_required');
            }
            if (! Schema::hasColumn('jobs', 'screening_question_2')) {
                $table->text('screening_question_2')->nullable()->after('screening_question_1');
            }
            if (! Schema::hasColumn('jobs', 'screening_question_3')) {
                $table->text('screening_question_3')->nullable()->after('screening_question_2');
            }
            if (! Schema::hasColumn('jobs', 'minimum_qualification')) {
                $table->text('minimum_qualification')->nullable()->after('screening_question_3');
            }
            if (! Schema::hasColumn('jobs', 'preferred_qualification')) {
                $table->text('preferred_qualification')->nullable()->after('minimum_qualification');
            }
            if (! Schema::hasColumn('jobs', 'work_mode')) {
                $table->string('work_mode')->nullable()->after('preferred_qualification');
            }
            if (! Schema::hasColumn('jobs', 'notice_period')) {
                $table->string('notice_period')->nullable()->after('work_mode');
            }
            if (! Schema::hasColumn('jobs', 'min_salary')) {
                $table->decimal('min_salary', 12, 2)->nullable()->after('salary');
            }
            if (! Schema::hasColumn('jobs', 'max_salary')) {
                $table->decimal('max_salary', 12, 2)->nullable()->after('min_salary');
            }
            if (! Schema::hasColumn('jobs', 'currency')) {
                $table->string('currency', 10)->default('USD')->after('max_salary');
            }
            if (! Schema::hasColumn('jobs', 'application_deadline')) {
                $table->date('application_deadline')->nullable()->after('currency');
            }
            if (! Schema::hasColumn('jobs', 'number_of_openings')) {
                $table->unsignedInteger('number_of_openings')->default(1)->after('application_deadline');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $columns = [
                'screening_question_1',
                'screening_question_2',
                'screening_question_3',
                'minimum_qualification',
                'preferred_qualification',
                'work_mode',
                'notice_period',
                'min_salary',
                'max_salary',
                'currency',
                'application_deadline',
                'number_of_openings',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('jobs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

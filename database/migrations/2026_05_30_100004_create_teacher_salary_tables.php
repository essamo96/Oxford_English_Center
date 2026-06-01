<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ----- Salary form (header) : one per teacher per month -----
        if (!Schema::hasTable('teacher_salary_forms')) {
            Schema::create('teacher_salary_forms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('teacher_id');
                $table->unsignedSmallInteger('year');
                $table->unsignedTinyInteger('month');          // 1..12
                $table->unsignedInteger('lectures_count')->default(0);
                $table->decimal('lecture_rate', 10, 2)->default(0);
                $table->decimal('gross_amount', 12, 2)->default(0);   // lectures * rate
                $table->decimal('bonus', 12, 2)->default(0);
                $table->decimal('deduction', 12, 2)->default(0);
                $table->decimal('net_amount', 12, 2)->default(0);     // gross + bonus - deduction
                $table->enum('status', ['open', 'closed'])->default('open');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('closed_by')->nullable();  // users.id
                $table->dateTime('closed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['teacher_id', 'year', 'month'], 'uniq_teacher_period');
                $table->index(['year', 'month']);
            });
        }

        // ----- Salary details (lines) : per group / per lecture-day breakdown -----
        if (!Schema::hasTable('teacher_salary_details')) {
            Schema::create('teacher_salary_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('salary_form_id');
                $table->unsignedBigInteger('group_id')->nullable();
                $table->date('lecture_date')->nullable();
                $table->unsignedInteger('lectures')->default(1);
                $table->decimal('rate', 10, 2)->default(0);
                $table->decimal('amount', 12, 2)->default(0);
                $table->timestamps();

                $table->index('salary_form_id');
            });
        }

        // ----- Monthly close log -----
        if (!Schema::hasTable('salary_close_logs')) {
            Schema::create('salary_close_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('year');
                $table->unsignedTinyInteger('month');
                $table->unsignedInteger('teachers_count')->default(0);
                $table->unsignedInteger('total_lectures')->default(0);
                $table->decimal('total_amount', 14, 2)->default(0);
                $table->unsignedBigInteger('closed_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['year', 'month']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_salary_details');
        Schema::dropIfExists('salary_close_logs');
        Schema::dropIfExists('teacher_salary_forms');
    }
};

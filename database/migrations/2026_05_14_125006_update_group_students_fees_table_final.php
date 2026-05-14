<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_students_fees', function (Blueprint $table) {
            if (!Schema::hasColumn('group_students_fees', 'payment_method_id')) {
                $table->unsignedBigInteger('payment_method_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('group_students_fees', 'total_due_amount')) {
                $table->decimal('total_due_amount', 10, 2)->default(0)->after('student_fee_paid');
            }
            if (!Schema::hasColumn('group_students_fees', 'remaining_amount')) {
                $table->decimal('remaining_amount', 10, 2)->default(0)->after('total_due_amount');
            }
            if (!Schema::hasColumn('group_students_fees', 'audit_status')) {
                $table->string('audit_status')->default('pending')->after('payment_method_id');
            }
        });

        // Use raw SQL to avoid Doctrine DBAL dependency for column changes
        \DB::statement("ALTER TABLE group_students_fees MODIFY COLUMN student_paid_type VARCHAR(255) NULL");
        \DB::statement("ALTER TABLE group_students_fees MODIFY COLUMN group_id BIGINT UNSIGNED NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_students_fees', function (Blueprint $table) {
            $table->dropColumn(['payment_method_id']);
        });
    }
};

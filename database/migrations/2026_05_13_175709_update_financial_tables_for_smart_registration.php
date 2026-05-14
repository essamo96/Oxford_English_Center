<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Placement Tests table
        Schema::table('placement_tests', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->default(50.00)->after('payment_method_id');
            $table->decimal('remaining_amount', 10, 2)->default(0)->after('total_amount');
            $table->enum('payment_status', ['pending', 'paid', 'partially_paid', 'refunded'])->default('pending')->after('remaining_amount');
        });

        // Update Group Students Fees table
        Schema::table('group_students_fees', function (Blueprint $table) {
            $table->decimal('total_due_amount', 10, 2)->default(0)->after('admin_verified_amount');
            $table->decimal('remaining_amount', 10, 2)->default(0)->after('total_due_amount');
            $table->enum('audit_status', ['pending', 'verified', 'rejected'])->default('pending')->after('remaining_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_tests', function (Blueprint $table) {
            $table->dropColumn(['total_amount', 'remaining_amount', 'payment_status']);
        });

        Schema::table('group_students_fees', function (Blueprint $table) {
            $table->dropColumn(['total_due_amount', 'remaining_amount', 'audit_status']);
        });
    }
};

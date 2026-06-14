<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_payment_submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('receipt_file');
            $table->unsignedBigInteger('fee_id')->nullable()->after('payment_method_id')
                  ->comment('FK to group_students_fees.id — the pending fee this submission covers');
        });
    }

    public function down(): void
    {
        Schema::table('student_payment_submissions', function (Blueprint $table) {
            $table->dropColumn(['payment_method_id', 'fee_id']);
        });
    }
};

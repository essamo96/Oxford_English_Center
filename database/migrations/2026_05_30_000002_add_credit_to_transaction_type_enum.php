<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('group_students_fees', 'transaction_type')) return;

        // The original enum was ('payment','refund','adjustment') — it lacked 'credit',
        // so credit rows were silently stored as '' (empty). Add 'credit' to the enum...
        DB::statement("ALTER TABLE `group_students_fees`
            MODIFY `transaction_type` ENUM('payment','refund','adjustment','credit')
            NOT NULL DEFAULT 'payment'");

        // ...then recover any credit rows that were corrupted into '' by the old column.
        // A row with empty type that carries a 'Credit Balance' marker is a lost credit.
        DB::table('group_students_fees')
            ->where('transaction_type', '')
            ->where('student_paid_type', 'Credit Balance')
            ->update(['transaction_type' => 'credit']);
    }

    public function down(): void
    {
        if (!Schema::hasColumn('group_students_fees', 'transaction_type')) return;

        DB::table('group_students_fees')->where('transaction_type', 'credit')
            ->update(['transaction_type' => 'adjustment']);

        DB::statement("ALTER TABLE `group_students_fees`
            MODIFY `transaction_type` ENUM('payment','refund','adjustment')
            NOT NULL DEFAULT 'payment'");
    }
};

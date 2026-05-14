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
            $table->string('payment_receipt')->nullable()->after('student_paid_type');
            $table->decimal('admin_verified_amount', 10, 2)->default(0)->after('payment_receipt');
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending')->after('admin_verified_amount');
            $table->text('notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_students_fees', function (Blueprint $table) {
            $table->dropColumn(['payment_receipt', 'admin_verified_amount', 'status', 'notes']);
        });
    }

};

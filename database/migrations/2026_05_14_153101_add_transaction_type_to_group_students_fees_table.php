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
            $table->enum('transaction_type', ['payment', 'refund', 'adjustment'])->default('payment')->after('payment_method_id');
            $table->decimal('transaction_amount', 10, 2)->default(0)->after('transaction_type');
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
            $table->dropColumn(['transaction_type', 'transaction_amount']);
        });
    }
};

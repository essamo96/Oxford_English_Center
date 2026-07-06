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
        Schema::create('student_compo_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_compo_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('payer_name')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency')->default('NIS');
            $table->string('payment_method')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamps();

            $table->foreign('student_compo_id')->references('id')->on('student_compos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_compo_payments');
    }
};

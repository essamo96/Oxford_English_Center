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
        Schema::create('placement_tests', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id'); // Signed integer to match students.id
            $table->date('test_date');
            $table->string('test_time');
            $table->enum('status', ['pending', 'payment_confirmed', 'waiting_for_test', 'completed'])->default('pending');
            $table->string('score')->nullable();
            $table->string('assigned_level')->nullable();
            $table->string('payment_receipt')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(100.00);
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_tests');
    }
};

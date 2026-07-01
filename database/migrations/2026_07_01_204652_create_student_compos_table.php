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
        Schema::create('student_compos', function (Blueprint $table) {
            $table->id();
            $table->string('full_name_ar');
            $table->string('full_name_en');
            $table->string('phone');
            $table->string('email');
            $table->date('dob');
            $table->enum('gender', ['Male', 'Female']);
            $table->text('address');
            $table->string('branch');
            $table->string('major_profession');
            $table->boolean('health_issues')->default(false);
            $table->boolean('placement_test')->default(false);
            $table->dateTime('placement_test_date')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->boolean('is_invoiced')->default(false);
            $table->boolean('is_read')->default(false); // for admin notification
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_compos');
    }
};

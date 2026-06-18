<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('mobile');
            $table->text('message');
            $table->string('status')->default('success'); // success or failed
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable(); // admin who sent it
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
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
        Schema::dropIfExists('sms_archives');
    }
}

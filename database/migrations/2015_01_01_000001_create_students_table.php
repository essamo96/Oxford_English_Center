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
        if (!Schema::hasTable('students')) {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191); // Field: name
            $table->string('name_en', 255)->nullable(); // Field: name_en
            $table->string('mobile', 50); // Field: mobile
            $table->date('dob')->nullable(); // Field: dob
            $table->string('job', 100)->nullable(); // Field: job
            $table->string('major', 255)->nullable(); // Field: major
            $table->string('current_level', 255)->nullable(); // Field: current_level
            $table->string('program_type')->nullable(); // Field: program_type
            $table->bigInteger('parent_id')->nullable(); // Field: parent_id
            $table->string('email', 100)->nullable(); // Field: email
            $table->date('join_date')->nullable(); // Field: join_date
            $table->date('exam_date')->nullable(); // Field: exam_date
            $table->string('exam_degree', 10)->nullable(); // Field: exam_degree
            $table->boolean('status'); // Field: status
            $table->boolean('delaying')->default(0); // Field: delaying
            $table->string('image', 191)->nullable(); // Field: image
            $table->mediumText('note')->nullable(); // Field: note
            $table->boolean('gender')->default(0); // Field: gender
            $table->text('delay_cusess')->nullable(); // Field: delay_cusess
            $table->string('username', 191); // Field: username
            $table->string('password', 191); // Field: password
            $table->boolean('seen')->nullable()->default(0); // Field: seen
            $table->boolean('ask_update')->nullable()->default(0); // Field: ask_update
            $table->string('remember_token', 100)->nullable(); // Field: remember_token
            $table->timestamps();
            $table->softDeletes();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('students'); // Safety: do not drop in synchronization
    }
};

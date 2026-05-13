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
        if (!Schema::hasTable('pending_data')) {
        Schema::create('pending_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable(); // Field: student_id
            $table->string('name', 191); // Field: name
            $table->string('mobile', 50); // Field: mobile
            $table->date('dob')->nullable(); // Field: dob
            $table->string('job', 100)->nullable(); // Field: job
            $table->string('email', 100)->nullable(); // Field: email
            $table->string('fileToUpload', 191)->nullable(); // Field: fileToUpload
            $table->boolean('seen')->nullable()->default(0); // Field: seen
            $table->boolean('ask_update')->nullable()->default(0); // Field: ask_update
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
        // Schema::dropIfExists('pending_data'); // Safety: do not drop in synchronization
    }
};

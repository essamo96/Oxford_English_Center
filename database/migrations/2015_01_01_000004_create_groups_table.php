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
        if (!Schema::hasTable('groups')) {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191); // Field: name
            $table->unsignedBigInteger('program_id')->nullable(); // Field: program_id
            $table->unsignedBigInteger('teacher_id')->nullable(); // Field: teacher_id
            $table->unsignedBigInteger('date_id')->nullable(); // Field: date_id
            $table->string('code', 191)->nullable(); // Field: code
            $table->timestamp('code_scope')->nullable()->useCurrent(); // Field: code_scope
            $table->timestamp('start_date')->nullable(); // Field: start_date
            $table->timestamp('end_date')->nullable(); // Field: end_date
            $table->mediumText('subjects')->nullable(); // Field: subjects
            $table->mediumText('teacher_lib')->nullable(); // Field: teacher_lib
            $table->mediumText('zoom')->nullable(); // Field: zoom
            $table->mediumText('image')->nullable(); // Field: image
            $table->integer('status'); // Field: status
            $table->integer('progress')->nullable(); // Field: progress
            $table->dateTime('progress_at')->nullable(); // Field: progress_at
            $table->string('drive', 300)->nullable(); // Field: drive
            $table->boolean('seen')->nullable()->default(0); // Field: seen
            $table->boolean('seen_progress')->nullable()->default(0); // Field: seen_progress
            $table->integer('attendance')->nullable(); // Field: attendance
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
        // Schema::dropIfExists('groups'); // Safety: do not drop in synchronization
    }
};

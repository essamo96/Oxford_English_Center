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
        if (!Schema::hasTable('teachers')) {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191); // Field: name
            $table->string('mobile', 14); // Field: mobile
            $table->date('dob')->nullable(); // Field: dob
            $table->string('email', 191); // Field: email
            $table->date('join_date')->nullable(); // Field: join_date
            $table->text('cv')->nullable(); // Field: cv
            $table->integer('status'); // Field: status
            $table->boolean('evaluations')->nullable()->default(0); // Field: evaluations
            $table->string('image', 191)->nullable(); // Field: image
            $table->string('username', 191); // Field: username
            $table->string('password', 191); // Field: password
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
        // Schema::dropIfExists('teachers'); // Safety: do not drop in synchronization
    }
};

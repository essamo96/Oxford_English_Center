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
        Schema::table('students', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->string('major')->nullable()->after('job');
            $table->string('current_level')->nullable()->after('major');
            $table->enum('program_type', ['adult', 'kids'])->nullable()->after('current_level');
            $table->unsignedBigInteger('parent_id')->nullable()->after('program_type');

            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['name_en', 'major', 'current_level', 'program_type', 'parent_id']);
        });
    }
};

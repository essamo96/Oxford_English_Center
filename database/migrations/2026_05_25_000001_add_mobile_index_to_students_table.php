<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobileIndexToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('students')) return;
        if (!Schema::hasColumn('students', 'mobile')) return;

        try {
            Schema::table('students', function (Blueprint $table) {
                // Use a try/catch in case the index already exists on older DBs
                $table->index('mobile');
            });
        } catch (\Exception $e) {
            // ignore if index already exists or other DB-specific issues
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('students')) return;
        if (!Schema::hasColumn('students', 'mobile')) return;

        try {
            Schema::table('students', function (Blueprint $table) {
                $table->dropIndex(['mobile']);
            });
        } catch (\Exception $e) {
            // ignore if index does not exist
        }
    }
}

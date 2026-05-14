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
        Schema::table('placement_tests', function (Blueprint $table) {
            $table->string('preferred_days')->nullable()->after('test_time');
            $table->string('preferred_time')->nullable()->after('preferred_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('placement_tests', function (Blueprint $table) {
            $table->dropColumn(['preferred_days', 'preferred_time']);
        });
    }
};

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
        Schema::table('student_compos', function (Blueprint $table) {
            $table->text('health_issues_details')->nullable()->after('health_issues');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_compos', function (Blueprint $table) {
            $table->dropColumn('health_issues_details');
        });
    }
};

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
        Schema::table('permissions_group', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions_group', 'color')) {
                $table->string('color', 30)->nullable()->after('icon');
            }
        });
    }

    public function down()
    {
        Schema::table('permissions_group', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};

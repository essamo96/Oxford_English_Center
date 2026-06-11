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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'sidebar_layout')) {
                $table->string('sidebar_layout', 30)->default('dark-sidebar')->after('id');
            }
            if (!Schema::hasColumn('settings', 'sidebar_bg_color')) {
                $table->string('sidebar_bg_color', 30)->nullable()->after('sidebar_layout');
            }
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['sidebar_layout', 'sidebar_bg_color']);
        });
    }
};

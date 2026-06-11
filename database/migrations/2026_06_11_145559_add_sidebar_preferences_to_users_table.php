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
        Schema::table('users', function (Blueprint $table) {
            $table->string('sidebar_layout',    30)->nullable()->after('last_login_at');
            $table->string('sidebar_bg_color',  30)->nullable()->after('sidebar_layout');
            $table->string('sidebar_text_color', 30)->nullable()->after('sidebar_bg_color');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['sidebar_layout', 'sidebar_bg_color', 'sidebar_text_color']);
        });
    }
};

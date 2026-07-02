<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fix all permissions that belong to the admin dashboard but were created with the 'web' guard
        DB::table('permissions')
            ->where('name', 'LIKE', 'admin.%')
            ->where('guard_name', 'web')
            ->update(['guard_name' => 'admin']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Not necessary to reverse
    }
};

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
        Schema::table('student_compos', function (Blueprint $table) {
            if (!Schema::hasColumn('student_compos', 'is_contacted')) {
                $table->boolean('is_contacted')->default(0)->after('is_read');
            }
        });

        // Update the icon for Combo Requests group to something more suitable
        DB::table('permissions_group')
            ->where('name', 'combo_requests')
            ->update(['icon' => 'ki-duotone ki-address-book']); // or ki-book-open
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_compos', function (Blueprint $table) {
            if (Schema::hasColumn('student_compos', 'is_contacted')) {
                $table->dropColumn('is_contacted');
            }
        });

        DB::table('permissions_group')
            ->where('name', 'combo_requests')
            ->update(['icon' => 'ki-duotone ki-briefcase']);
    }
};

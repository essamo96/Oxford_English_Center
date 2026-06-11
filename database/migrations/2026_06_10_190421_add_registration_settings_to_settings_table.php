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
            $table->tinyInteger('payment_required')->default(1)->after('contact_email');
            $table->tinyInteger('registration_open')->default(1)->after('payment_required');
            $table->text('registration_closed_message')->nullable()->after('registration_open');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['payment_required', 'registration_open', 'registration_closed_message']);
        });
    }
};

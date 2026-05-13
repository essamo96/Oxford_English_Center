<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 191); // Added from DB
            $table->string('name', 191);
            $table->string('email', 191)->unique();
            $table->integer('role'); // Added from DB
            $table->integer('created_by'); // Added from DB
            $table->string('password', 191);
            $table->integer('status'); // Added from DB
            $table->string('image', 191)->nullable(); // Added from DB
            $table->timestamp('last_login_at')->nullable(); // Added from DB
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Added from DB
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

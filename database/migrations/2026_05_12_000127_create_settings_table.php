<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable(); // Field: title
            $table->text('description')->nullable(); // Field: description
            $table->text('more_desc'); // Field: more_desc
            $table->string('logo', 255)->nullable(); // Field: logo
            $table->string('tags', 255)->nullable(); // Field: tags
            $table->string('mobile', 255)->nullable(); // Field: mobile
            $table->string('address', 255)->nullable(); // Field: address
            $table->integer('donars')->nullable(); // Field: donars
            $table->integer('clients')->nullable(); // Field: clients
            $table->integer('happy')->nullable(); // Field: happy
            $table->integer('tickects')->nullable(); // Field: tickects
            $table->string('contact_email', 255)->nullable(); // Field: contact_email
            $table->timestamps();
            $table->softDeletes();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('settings'); // Safety: do not drop in synchronization
    }
};

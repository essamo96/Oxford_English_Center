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
        if (!Schema::hasTable('pages')) {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191); // Field: title
            $table->string('slug', 200); // Field: slug
            $table->longText('details'); // Field: details
            $table->string('image', 191); // Field: image
            $table->string('banner', 300); // Field: banner
            $table->string('url', 200)->nullable(); // Field: url
            $table->text('tags'); // Field: tags
            $table->integer('status')->default(1); // Field: status
            $table->string('age', 200)->nullable(); // Field: age
            $table->string('level', 200)->nullable(); // Field: level
            $table->string('weeks', 200)->nullable(); // Field: weeks
            $table->string('hours', 200)->nullable(); // Field: hours
            $table->string('mock', 200)->nullable(); // Field: mock
            $table->string('duration', 200)->nullable(); // Field: duration
            $table->string('class_size', 200)->nullable(); // Field: class_size
            $table->string('fees', 200)->nullable(); // Field: fees
            $table->string('price', 200)->nullable(); // Field: price
            $table->string('start', 200)->nullable(); // Field: start
            $table->string('days', 200)->nullable(); // Field: days
            $table->string('time', 200)->nullable(); // Field: time
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
        // Schema::dropIfExists('pages'); // Safety: do not drop in synchronization
    }
};

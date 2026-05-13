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
        if (!Schema::hasTable('news')) {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title', 300); // Field: title
            $table->text('sub'); // Field: sub
            $table->text('descs'); // Field: descs
            $table->string('onwer', 200); // Field: onwer
            $table->string('source', 200)->nullable(); // Field: source
            $table->string('image', 250); // Field: image
            $table->string('img_notes', 80)->nullable(); // Field: img_notes
            $table->boolean('main')->default(0); // Field: main
            $table->boolean('slider')->default(0); // Field: slider
            $table->boolean('comment')->default(0); // Field: comment
            $table->unsignedBigInteger('category_id')->default(0); // Field: category_id
            $table->boolean('sidebar')->default(0); // Field: sidebar
            $table->unsignedBigInteger('others_id')->default(0); // Field: others_id
            $table->integer('publish')->default(0); // Field: publish
            $table->unsignedBigInteger('publish_id')->default(0); // Field: publish_id
            $table->unsignedBigInteger('sec_id')->default(0); // Field: sec_id
            $table->integer('resort')->nullable()->default(1); // Field: resort
            $table->bigInteger('views')->default(0); // Field: views
            $table->string('tags', 250)->nullable(); // Field: tags
            $table->dateTime('pub_date'); // Field: pub_date
            $table->unsignedBigInteger('user_id')->default(0); // Field: user_id
            $table->integer('updated_by')->default(0); // Field: updated_by
            $table->string('thumb', 300); // Field: thumb
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
        // Schema::dropIfExists('news'); // Safety: do not drop in synchronization
    }
};

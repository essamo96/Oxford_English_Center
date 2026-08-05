<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exam_violations: lightweight anti-cheat event log (no AI proctoring). Populated client-side
     * by the student exam UI when it detects copy/paste/cut/right-click/tab-switch/blur/focus events.
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_violations')) {
        Schema::create('exam_violations', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('attempt_id'); // FK to exam_attempts.id
            $table->enum('type', ['copy', 'paste', 'cut', 'right_click', 'tab_switch', 'window_blur', 'window_focus']); // Detected event type
            $table->dateTime('occurred_at'); // Client-reported timestamp of the event
            $table->json('meta')->nullable(); // Optional extra context (e.g. question_id at time of violation)
            $table->timestamps();

            $table->foreign('attempt_id')->references('id')->on('exam_attempts')->onDelete('cascade');
            $table->index('attempt_id');
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exam_violations'); // Safety: do not drop in synchronization
    }
};

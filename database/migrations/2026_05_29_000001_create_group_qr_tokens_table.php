<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('group_qr_tokens')) return;
        Schema::create('group_qr_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('token', 64)->unique();   // random token embedded in the QR
            $table->timestamp('expires_at');         // expiry instant
            $table->string('created_by_type', 20)->default('admin'); // 'admin' | 'teacher'
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->integer('max_uses')->nullable(); // optional usage cap (null = unlimited)
            $table->integer('uses_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('group_id');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_qr_tokens');
    }
};

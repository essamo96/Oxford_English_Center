<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('attendance_settings')) {
            Schema::create('attendance_settings', function (Blueprint $table) {
                $table->id();
                $table->text('allowed_ips')->nullable();        // comma/newline separated IPs or CIDR
                $table->unsignedInteger('grace_minutes')->default(15); // tolerance before/after the lecture window
                $table->boolean('enforce_ip')->default(true);
                $table->boolean('enforce_time')->default(true);
                $table->timestamps();
            });

            // Seed the single settings row (IP enforcement off by default until admin sets IPs)
            DB::table('attendance_settings')->insert([
                'allowed_ips'   => null,
                'grace_minutes' => 15,
                'enforce_ip'    => false,
                'enforce_time'  => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};

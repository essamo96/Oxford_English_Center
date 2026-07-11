<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة حقل brochure_path لجدول البرامج
     * Migration آمن — يضيف عمود فقط دون تدمير أي بيانات حالية
     */
    public function up(): void
    {
        if (!Schema::hasColumn('programs', 'brochure_path')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->string('brochure_path', 500)->nullable()->after('image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('programs', 'brochure_path')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('brochure_path');
            });
        }
    }
};

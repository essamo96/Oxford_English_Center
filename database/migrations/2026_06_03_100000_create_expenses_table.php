<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->date('expense_date');                       // تاريخ الصرف
                $table->string('statement');                        // بيان الصرف
                $table->decimal('amount', 12, 2)->default(0);       // المبلغ
                $table->text('notes')->nullable();                  // ملاحظات الصرف
                $table->unsignedBigInteger('created_by')->nullable(); // من قام بالخطوة (users.id)
                $table->timestamps();
                $table->softDeletes();

                $table->index('expense_date');
                $table->index('created_by');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

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
        Schema::create('payment_plan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('payment_plans')->cascadeOnDelete();
            $table->unsignedInteger('installment_number');
            $table->decimal('amount', 12, 2);
            $table->string('due_date', 20);
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_paid')->default(false);
            $table->string('paid_date', 20)->default('');
            $table->timestamps();

            $table->unique(['plan_id', 'installment_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_plan_installments');
    }
};

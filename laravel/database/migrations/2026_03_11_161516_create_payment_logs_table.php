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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('payment_id_ref');
            $table->unsignedBigInteger('student_id_ref')->nullable();
            $table->unsignedBigInteger('term_id_ref')->nullable();
            $table->unsignedBigInteger('fee_type_id_ref')->nullable();
            $table->string('action', 10);
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_email')->default('');
            $table->string('student_name')->default('');
            $table->string('term_name')->default('');
            $table->string('fee_type_name')->default('');
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('payment_date', 20)->default('');
            $table->string('method', 20)->default('');
            $table->string('reference')->default('');
            $table->text('note')->default('');
            $table->json('changes');
            $table->json('snapshot');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};

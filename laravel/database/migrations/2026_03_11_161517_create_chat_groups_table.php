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
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->default('');
            $table->boolean('is_class_group')->default(false);
            $table->foreignId('course_id')->nullable()->unique()->constrained('courses')->nullOnDelete();
            $table->foreignId('created_by_profile_id')->nullable()->constrained('profiles')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_groups');
    }
};

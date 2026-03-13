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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('type', 10);
            $table->string('first_name');
            $table->string('last_name')->default('');
            $table->string('dob', 20);
            $table->string('sex', 10);
            $table->string('phone_number', 20)->default('');
            $table->boolean('is_active')->default(true);
            $table->string('id_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};

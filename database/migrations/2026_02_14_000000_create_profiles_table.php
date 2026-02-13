<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['admin', 'teacher', 'student', 'parent']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('dob');
            $table->enum('sex', ['male', 'female']);
            $table->string('phone_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('id_number')->unique();
            $table->integer('user_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('token')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['photography', 'catering', 'banquet'])->nullable();
            $table->enum('user_type', ['vendor', 'user', 'admin'])->default('user');
            $table->string('profile_picture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

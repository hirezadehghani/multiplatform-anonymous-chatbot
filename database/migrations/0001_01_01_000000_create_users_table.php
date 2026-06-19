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
            $table->uuid('uuid')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('display_name')->nullable();
            $table->rememberToken();
            $table->enum('gender', [
                'male',
                'female',
                'unknown',
            ])->default('unknown');
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('city')->nullable();
            $table->boolean('profile_completed')
                ->default(false);
            $table->timestamp('last_seen_at')
                ->nullable();
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

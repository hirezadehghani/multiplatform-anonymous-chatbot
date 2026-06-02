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
        Schema::create('chat_rooms', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user1_id')
                ->constrained('users');

            $table->foreignId('user2_id')
                ->constrained('users');

            $table->enum('status', [
                'active',
                'ended'
            ]);

            $table->timestamp('started_at');

            $table->timestamp('ended_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user1_id',
                'user2_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};

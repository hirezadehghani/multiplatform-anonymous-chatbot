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
        Schema::create('messages', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->foreignId('room_id')
                ->constrained('chat_rooms')
                ->cascadeOnDelete();

            $table->foreignId('sender_user_id')
                ->constrained('users');

            $table->text('body')
                ->nullable();

            $table->string('type')
                ->default('text');

            $table->json('meta')
                ->nullable();

            $table->timestamps();

            $table->index('room_id');
            $table->index('sender_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

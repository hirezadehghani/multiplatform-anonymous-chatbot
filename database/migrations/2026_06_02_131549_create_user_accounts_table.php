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
        Schema::create('user_accounts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('bot_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('platform');

            $table->string('platform_user_id');

            $table->string('username')
                ->nullable();

            $table->boolean('is_primary')
                ->default(false);

            $table->timestamps();

            $table->unique([
                'platform',
                'platform_user_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accounts');
    }
};

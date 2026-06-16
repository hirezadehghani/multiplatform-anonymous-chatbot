<?php

namespace Database\Factories;

use App\Models\Bot;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserAccount>
 */
class UserAccountFactory extends Factory
{
    protected $model = UserAccount::class;

    public function definition(): array
    {
        $bot = Bot::factory()->create();
        $user = User::factory()->create();

        return [
            'user_id' => $user->id,
            'bot_id' => $bot->id,
            'platform' => $bot->platform,
            'platform_user_id' => $this->faker->unique()->userName(),
            'username' => $this->faker->userName(),
            'is_primary' => false,
        ];
    }
}

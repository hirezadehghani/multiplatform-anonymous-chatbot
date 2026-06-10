<?php

namespace Database\Factories;

use App\Models\Bot;
use App\Enums\PlatformEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bot>
 */
class BotFactory extends Factory
{
    protected $model = Bot::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Bot',
            'platform' => PlatformEnum::BALE->value,
            'token' => $this->faker->sha256(),
            'is_active' => true,
            'settings' => null,
        ];
    }
}

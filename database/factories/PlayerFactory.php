<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\User;
use App\Models\User as UserAlias;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Factory as FactoryAlias;

/**
 * @extends FactoryAlias<UserAlias>
 */
class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition()
    {
        return [
            'name' => User::factory(),
            'user' => User::factory(),
            'country' => $this->faker->randomElement([1,2,3,4,5]),
            'gameMode' => $this->faker->randomElement([1,2,3]),
            'attackStrategy' => $this->faker->randomElement([1,2,3]),
            'defenseStrategy' => $this->faker->randomElement([1,2,3]),
            'score' => $this->faker->numberBetween(1, 1000),
            'buildScore' => $this->faker->numberBetween(1, 1000),
            'attackScore' => $this->faker->numberBetween(1, 1000),
            'defenseScore' => $this->faker->numberBetween(1, 1000),
            'militaryScore' => $this->faker->numberBetween(1, 1000),
            'researchScore' => $this->faker->numberBetween(1, 1000),
            'researchPoints' => 0,
        ];
    }

    public function withName($name)
    {
        return $this->state(function (array $attributes) use ($name) {
            return [
                'name' => $name,
            ];
        });
    }
}

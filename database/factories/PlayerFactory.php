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
            'metal' => $this->faker->numberBetween(100, 1000),
            'uranium' => $this->faker->numberBetween(100, 1000),
            'crystal' => $this->faker->numberBetween(100, 1000),
            'energy' => $this->faker->numberBetween(100, 1000),
            'battery' => $this->faker->numberBetween(100, 1000),
            'extraBattery' => $this->faker->numberBetween(0, 10),
            'capMetal' => $this->faker->numberBetween(1000, 5000),
            'capUranium' => $this->faker->numberBetween(1000, 5000),
            'capCrystal' => $this->faker->numberBetween(1000, 5000),
            'proMetal' => $this->faker->numberBetween(1, 10),
            'proUranium' => $this->faker->numberBetween(1, 10),
            'proCrystal' => $this->faker->numberBetween(1, 10),
            'pwMetal' => $this->faker->numberBetween(1, 10),
            'pwUranium' => $this->faker->numberBetween(1, 10),
            'pwCrystal' => $this->faker->numberBetween(1, 10),
            'pwEnergy' => $this->faker->numberBetween(1, 10),
            'merchantShips' => $this->faker->numberBetween(0, 10),
            'score' => $this->faker->numberBetween(1, 1000),
            'buildScore' => $this->faker->numberBetween(1, 1000),
            'attackScore' => $this->faker->numberBetween(1, 1000),
            'defenseScore' => $this->faker->numberBetween(1, 1000),
            'militaryScore' => $this->faker->numberBetween(1, 1000),
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

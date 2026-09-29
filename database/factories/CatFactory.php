<?php

namespace Database\Factories;

use App\Enum\GenderEnum;
use App\Models\Cat\Cat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cat>
 */
class CatFactory extends Factory
{
    protected $model = Cat::class;

    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'gender' => fake()->randomElement(GenderEnum::values()),
            'age' => fake()->numberBetween(0, 20),
            'mother_id' => null,
        ];
    }

    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => GenderEnum::MALE->value,
        ]);
    }

    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => GenderEnum::FEMALE->value,
        ]);
    }
}

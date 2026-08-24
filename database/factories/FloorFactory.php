<?php

namespace Database\Factories;

use App\Models\Floor;
use App\Models\Staircase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Floor>
 */
class FloorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'staircase_id' => Staircase::factory(),
            'number' => fake()->numberBetween(0, 10),
        ];
    }
}

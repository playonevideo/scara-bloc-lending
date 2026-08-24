<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Staircase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Staircase>
 */
class StaircaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'name' => fake()->unique()->word(),
        ];
    }
}

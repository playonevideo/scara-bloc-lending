<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staircase>
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

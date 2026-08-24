<?php

namespace Database\Factories;

use App\Models\Floor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'floor_id' => Floor::factory(),
            'number' => fake()->unique()->numberBetween(1, 200),
        ];
    }
}

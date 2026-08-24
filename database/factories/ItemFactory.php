<?php

namespace Database\Factories;

use App\Enums\ObjectCondition;
use App\Enums\ObjectStatus;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'owner_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->sentence(),
            'condition' => ObjectCondition::Good,
            'status' => ObjectStatus::Available,
            'max_borrow_days' => 30,
            'is_published' => true,
        ];
    }

    public function borrowed(): static
    {
        return $this->state(fn () => ['status' => ObjectStatus::Borrowed]);
    }
}

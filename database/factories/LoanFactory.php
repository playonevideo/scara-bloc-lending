<?php

namespace Database\Factories;

use App\Enums\LoanStatus;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    public function definition(): array
    {
        $starts = now()->addDays(fake()->numberBetween(1, 10));

        return [
            'object_id' => Item::factory(),
            'borrower_id' => User::factory(),
            'lender_id' => User::factory(),
            'status' => LoanStatus::Requested,
            'starts_at' => $starts,
            'ends_at' => $starts->copy()->addDays(fake()->numberBetween(1, 7)),
            'requested_at' => now(),
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => LoanStatus::Accepted, 'responded_at' => now()]);
    }

    public function borrowed(): static
    {
        return $this->state(fn () => ['status' => LoanStatus::Borrowed, 'borrowed_at' => now()]);
    }
}

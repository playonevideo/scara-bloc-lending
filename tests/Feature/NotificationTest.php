<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Loan;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_loan_request_notifies_lender(): void
    {
        $owner = $this->createResident();
        $borrower = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($borrower)->post("/obiecte/{$object->id}/imprumut", [
            'starts_at' => now()->addDay()->toDateString(),
            'ends_at' => now()->addDays(3)->toDateString(),
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => 'user',
            'notifiable_id' => $owner->id,
        ]);
    }

    public function test_completed_loan_review_notifies_reviewee(): void
    {
        $owner = $this->createResident();
        $borrower = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id]);

        $loan = Loan::factory()->create([
            'object_id' => $object->id,
            'borrower_id' => $borrower->id,
            'lender_id' => $owner->id,
            'status' => 'completed',
        ]);

        $this->actingAs($borrower)->post("/imprumuturi/{$loan->id}/review", [
            'rating' => 5,
            'comment' => 'Excelent!',
        ]);

        $this->assertDatabaseHas('reviews', [
            'loan_id' => $loan->id,
            'reviewer_id' => $borrower->id,
            'reviewee_id' => $owner->id,
            'rating' => 5,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Enums\ObjectStatus;
use App\Models\Item;
use App\Models\Loan;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_resident_can_request_a_loan(): void
    {
        $owner = $this->createResident();
        $borrower = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($borrower)->post("/obiecte/{$object->id}/imprumut", [
            'starts_at' => now()->addDay()->toDateString(),
            'ends_at' => now()->addDays(3)->toDateString(),
            'message' => 'Am nevoie pentru weekend.',
        ])->assertRedirect();

        $this->assertDatabaseHas('loans', [
            'object_id' => $object->id,
            'borrower_id' => $borrower->id,
            'lender_id' => $owner->id,
            'status' => LoanStatus::Requested->value,
        ]);
    }

    public function test_owner_cannot_borrow_own_object(): void
    {
        $owner = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($owner)->post("/obiecte/{$object->id}/imprumut", [
            'starts_at' => now()->addDay()->toDateString(),
            'ends_at' => now()->addDays(3)->toDateString(),
        ])->assertSessionHasErrors();

        $this->assertDatabaseCount('loans', 0);
    }

    public function test_overlapping_loans_are_rejected(): void
    {
        $owner = $this->createResident();
        $borrower = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id]);

        Loan::factory()->create([
            'object_id' => $object->id,
            'borrower_id' => $borrower->id,
            'lender_id' => $owner->id,
            'status' => LoanStatus::Accepted,
            'starts_at' => now()->addDays(2)->toDateString(),
            'ends_at' => now()->addDays(5)->toDateString(),
        ]);

        $this->actingAs($borrower)->post("/obiecte/{$object->id}/imprumut", [
            'starts_at' => now()->addDays(3)->toDateString(),
            'ends_at' => now()->addDays(6)->toDateString(),
        ])->assertSessionHasErrors('starts_at');
    }

    public function test_borrower_cannot_request_already_borrowed_object(): void
    {
        $owner = $this->createResident();
        $borrower = $this->createResident();
        $object = Item::factory()->borrowed()->create(['owner_id' => $owner->id]);

        $this->actingAs($borrower)->post("/obiecte/{$object->id}/imprumut", [
            'starts_at' => now()->addDay()->toDateString(),
            'ends_at' => now()->addDays(3)->toDateString(),
        ])->assertSessionHasErrors('object');
    }

    public function test_lender_can_accept_and_marks_object_reserved(): void
    {
        $owner = $this->createResident();
        $borrower = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id]);

        $loan = Loan::factory()->create([
            'object_id' => $object->id,
            'borrower_id' => $borrower->id,
            'lender_id' => $owner->id,
        ]);

        $this->actingAs($owner)->post("/imprumuturi/{$loan->id}/accept")->assertRedirect();

        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => LoanStatus::Accepted->value]);
        $this->assertDatabaseHas('objects', ['id' => $object->id, 'status' => ObjectStatus::Reserved->value]);
    }

    public function test_borrower_cannot_accept_loan(): void
    {
        $owner = $this->createResident();
        $borrower = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id]);

        $loan = Loan::factory()->create([
            'object_id' => $object->id,
            'borrower_id' => $borrower->id,
            'lender_id' => $owner->id,
        ]);

        $this->actingAs($borrower)->post("/imprumuturi/{$loan->id}/accept")->assertForbidden();
    }
}

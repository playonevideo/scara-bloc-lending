<?php

namespace Tests\Feature;

use App\Enums\ReportReason;
use App\Models\Item;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_resident_can_report_an_object(): void
    {
        $reporter = $this->createResident();
        $owner = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($reporter)->post('/raporteaza', [
            'reportable_type' => 'object',
            'reportable_id' => $object->id,
            'reason' => ReportReason::Spam->value,
            'details' => 'Descriere înșelătoare.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'reportable_type' => 'object',
            'reportable_id' => $object->id,
            'reason' => ReportReason::Spam->value,
            'status' => 'new',
        ]);
    }
}

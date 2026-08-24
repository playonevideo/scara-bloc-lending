<?php

namespace Tests\Feature;

use App\Models\Item;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class ResidentProfileTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_resident_profile_shows_details_and_objects(): void
    {
        $owner = $this->createResident();
        $viewer = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id, 'is_published' => true]);

        $this->actingAs($viewer)
            ->get("/vecini/{$owner->id}")
            ->assertOk()
            ->assertSee($owner->name)
            ->assertSee($object->title);
    }

    public function test_object_show_links_to_owner_profile(): void
    {
        $owner = $this->createResident();
        $viewer = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id, 'is_published' => true]);

        $this->actingAs($viewer)
            ->get("/obiecte/{$object->id}")
            ->assertSee("/vecini/{$owner->id}");
    }
}

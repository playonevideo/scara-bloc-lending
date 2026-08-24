<?php

namespace Tests\Concerns;

use App\Models\Apartment;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Staircase;
use App\Models\User;

trait CreatesBuildingStructure
{
    protected function createApartment(): Apartment
    {
        $building = Building::factory()->create();
        $staircase = Staircase::factory()->for($building)->create();
        $floor = Floor::factory()->for($staircase)->create();

        return Apartment::factory()->for($floor)->create();
    }

    protected function createResident(?Apartment $apartment = null): User
    {
        return User::factory()->create(['apartment_id' => ($apartment ?? $this->createApartment())->id]);
    }
}

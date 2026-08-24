<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_resident_cannot_access_admin_panel(): void
    {
        $resident = $this->createResident();

        $this->actingAs($resident)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_resident_cannot_access_filament_resources(): void
    {
        $resident = $this->createResident();

        $this->actingAs($resident)->get('/admin/users')->assertForbidden();
    }
}

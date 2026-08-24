<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class ObjectTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_resident_can_publish_an_object(): void
    {
        $user = $this->createResident();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post('/obiecte', [
            'title' => 'Bormașină Bosch',
            'category_id' => $category->id,
            'description' => 'Bormașină în stare bună.',
            'condition' => 'good',
            'max_borrow_days' => 7,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('objects', [
            'title' => 'Bormașină Bosch',
            'owner_id' => $user->id,
        ]);
    }

    public function test_resident_can_update_own_object(): void
    {
        $user = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->put("/obiecte/{$object->id}", [
                'title' => 'Titlu actualizat',
                'category_id' => $object->category_id,
                'condition' => 'good',
                'max_borrow_days' => 5,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('objects', ['id' => $object->id, 'title' => 'Titlu actualizat']);
    }

    public function test_resident_cannot_edit_other_residents_object(): void
    {
        $owner = $this->createResident();
        $other = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($other)
            ->put("/obiecte/{$object->id}", [
                'title' => 'Hacked',
                'category_id' => $object->category_id,
                'condition' => 'good',
                'max_borrow_days' => 5,
            ])
            ->assertForbidden();
    }

    public function test_resident_can_delete_own_object(): void
    {
        $user = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)->delete("/obiecte/{$object->id}")->assertRedirect();

        $this->assertSoftDeleted('objects', ['id' => $object->id]);
    }

    public function test_guest_cannot_publish_object(): void
    {
        $category = Category::factory()->create();

        $this->post('/obiecte', [
            'title' => 'Test',
            'category_id' => $category->id,
            'condition' => 'good',
            'max_borrow_days' => 7,
        ])->assertRedirect('/login');
    }

    public function test_marketplace_shows_published_objects(): void
    {
        $user = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $user->id, 'is_published' => true]);
        $hidden = Item::factory()->create(['owner_id' => $user->id, 'is_published' => false]);

        $this->actingAs($user)->get('/obiecte')
            ->assertOk()
            ->assertSee($object->title)
            ->assertDontSee($hidden->title);
    }
}

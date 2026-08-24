<?php

namespace Tests\Feature;

use App\Livewire\Chat;
use App\Models\Conversation;
use App\Models\Item;
use Livewire\Livewire;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_resident_can_start_conversation(): void
    {
        $owner = $this->createResident();
        $borrower = $this->createResident();
        $object = Item::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($borrower)->post('/mesaje', [
            'user_id' => $owner->id,
            'object_id' => $object->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('conversation_participants', ['user_id' => $borrower->id]);
        $this->assertDatabaseHas('conversation_participants', ['user_id' => $owner->id]);
    }

    public function test_non_participant_cannot_view_conversation(): void
    {
        $a = $this->createResident();
        $b = $this->createResident();
        $intruder = $this->createResident();

        $conversation = Conversation::create();
        $conversation->participants()->attach([$a->id, $b->id]);

        $this->actingAs($intruder)->get("/mesaje/{$conversation->id}")->assertForbidden();
    }

    public function test_participant_can_send_message(): void
    {
        $a = $this->createResident();
        $b = $this->createResident();

        $conversation = Conversation::create();
        $conversation->participants()->attach([$a->id, $b->id]);

        Livewire::actingAs($a)
            ->test(Chat::class, ['conversation' => $conversation])
            ->set('body', 'Salut!')
            ->call('send')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $a->id,
            'body' => 'Salut!',
        ]);
    }
}

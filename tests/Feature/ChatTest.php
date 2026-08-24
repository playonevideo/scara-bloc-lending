<?php

namespace Tests\Feature;

use App\Livewire\Messaging;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_messaging_index_renders(): void
    {
        $user = $this->createResident();

        $this->actingAs($user)->get('/mesaje')->assertOk();
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
            ->test(Messaging::class, ['conversationId' => $conversation->id])
            ->set('body', 'Salut!')
            ->call('send')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $a->id,
            'body' => 'Salut!',
        ]);
    }

    public function test_sender_can_delete_own_message(): void
    {
        $a = $this->createResident();
        $b = $this->createResident();

        $conversation = Conversation::create();
        $conversation->participants()->attach([$a->id, $b->id]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $a->id,
            'body' => 'Șterge-mă',
        ]);

        Livewire::actingAs($a)
            ->test(Messaging::class, ['conversationId' => $conversation->id])
            ->call('deleteMessage', $message->id);

        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
    }

    public function test_blocked_user_cannot_send_message(): void
    {
        $a = $this->createResident();
        $b = $this->createResident();

        $conversation = Conversation::create();
        $conversation->participants()->attach([$a->id, $b->id]);

        $a->blocks()->create(['blocked_id' => $b->id]);

        Livewire::actingAs($a)
            ->test(Messaging::class, ['conversationId' => $conversation->id])
            ->set('body', 'Salut')
            ->call('send')
            ->assertHasErrors('body');

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_toggle_block_creates_and_removes_block(): void
    {
        $a = $this->createResident();
        $b = $this->createResident();

        $conversation = Conversation::create();
        $conversation->participants()->attach([$a->id, $b->id]);

        Livewire::actingAs($a)
            ->test(Messaging::class, ['conversationId' => $conversation->id])
            ->call('toggleBlock');

        $this->assertDatabaseHas('blocks', ['blocker_id' => $a->id, 'blocked_id' => $b->id]);

        Livewire::actingAs($a)
            ->test(Messaging::class, ['conversationId' => $conversation->id])
            ->call('toggleBlock');

        $this->assertDatabaseMissing('blocks', ['blocker_id' => $a->id, 'blocked_id' => $b->id]);
    }

    public function test_message_can_be_sent_with_attachment(): void
    {
        Storage::fake('public');

        $a = $this->createResident();
        $b = $this->createResident();

        $conversation = Conversation::create();
        $conversation->participants()->attach([$a->id, $b->id]);

        $file = UploadedFile::fake()->image('photo.jpg');

        Livewire::actingAs($a)
            ->test(Messaging::class, ['conversationId' => $conversation->id])
            ->set('attachments', [$file])
            ->call('send')
            ->assertHasNoErrors();

        $message = Message::where('conversation_id', $conversation->id)->first();

        $this->assertNotNull($message);
        $this->assertDatabaseHas('message_attachments', [
            'message_id' => $message->id,
            'name' => 'photo.jpg',
        ]);

        $attachment = $message->attachments()->first();
        Storage::disk('public')->assertExists($attachment->path);
    }
}

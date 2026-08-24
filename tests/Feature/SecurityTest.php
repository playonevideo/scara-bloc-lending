<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TwoFactorService;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_change_phone_sends_code_and_stores_pending_number(): void
    {
        $user = User::factory()->create(['phone' => '0711111111']);

        $this->actingAs($user)->post('/setari/securitate/telefon', [
            'new_phone' => '0767965218',
            'current_password' => 'password',
        ])->assertSessionHas('auth.pending_phone', '0767965218');

        $this->assertDatabaseHas('two_factor_challenges', ['user_id' => $user->id]);
    }

    public function test_verify_phone_change_updates_number(): void
    {
        $user = User::factory()->create(['phone' => '0711111111']);
        $code = app(TwoFactorService::class)->sendCode($user, '0767965218');

        $this->actingAs($user)
            ->withSession(['auth.pending_phone' => '0767965218'])
            ->post('/setari/securitate/telefon/verifica', [
                'code' => $code,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'phone' => '0767965218']);
    }

    public function test_change_phone_requires_correct_password(): void
    {
        $user = User::factory()->create(['phone' => '0711111111']);

        $this->actingAs($user)->post('/setari/securitate/telefon', [
            'new_phone' => '0767965218',
            'current_password' => 'wrong-password',
        ])->assertSessionHasErrors('current_password');
    }

    public function test_resend_phone_code_sends_a_new_code_after_throttle(): void
    {
        $user = User::factory()->create(['phone' => '0711111111']);
        $service = app(TwoFactorService::class);

        $service->sendCode($user, '0767965218');
        $this->assertDatabaseCount('two_factor_challenges', 1);

        $this->travel(31)->seconds();

        $this->actingAs($user)
            ->withSession(['auth.pending_phone' => '0767965218'])
            ->post('/setari/securitate/telefon/retrimite')
            ->assertRedirect();

        $this->assertDatabaseCount('two_factor_challenges', 2);
    }
}

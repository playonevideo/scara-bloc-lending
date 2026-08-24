<?php

namespace Tests\Feature;

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_setup_generates_pending_secret(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/setari/securitate/2fa/setup')->assertRedirect();

        $this->assertNotNull(session('auth.two_factor_pending_secret'));
    }

    public function test_confirm_enables_two_factor(): void
    {
        $user = User::factory()->create();
        $secret = (new Google2FA)->generateSecretKey();
        $code = (new Google2FA)->getCurrentOtp($secret);

        $this->actingAs($user)
            ->withSession(['auth.two_factor_pending_secret' => $secret])
            ->post('/setari/securitate/2fa/confirm', ['code' => $code])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'two_factor_enabled' => true,
            'two_factor_secret' => $secret,
        ]);
    }

    public function test_confirm_with_invalid_code_fails(): void
    {
        $user = User::factory()->create();
        $secret = (new Google2FA)->generateSecretKey();

        $this->actingAs($user)
            ->withSession(['auth.two_factor_pending_secret' => $secret])
            ->post('/setari/securitate/2fa/confirm', ['code' => '000000'])
            ->assertSessionHasErrors('code');
    }

    public function test_disable_clears_two_factor(): void
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'two_factor_secret' => 'ABCDEFGHIJKLMNOP',
        ]);

        $this->actingAs($user)->post('/setari/securitate/2fa/disable')->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ]);
    }
}

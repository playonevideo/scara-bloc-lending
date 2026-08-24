<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_login_redirects_to_challenge_when_2fa_enabled(): void
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'two_factor_secret' => 'ABCDEFGHIJKLMNOP',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.challenge'));
    }

    public function test_challenge_page_requires_pending_login(): void
    {
        $this->get('/two-factor/challenge')->assertRedirect(route('login'));
    }

    public function test_verify_with_valid_code_logs_in(): void
    {
        $secret = (new Google2FA)->generateSecretKey();
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'two_factor_secret' => $secret,
        ]);
        $code = (new Google2FA)->getCurrentOtp($secret);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.challenge'));

        $this->post('/two-factor/challenge', ['code' => $code])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_verify_with_invalid_code_fails(): void
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'two_factor_secret' => 'ABCDEFGHIJKLMNOP',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->post('/two-factor/challenge', ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }
}

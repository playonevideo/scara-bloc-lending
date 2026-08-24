<?php

namespace Tests\Feature\Auth;

use App\Models\TwoFactorChallenge;
use App\Models\User;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_login_redirects_to_challenge_when_2fa_enabled(): void
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'phone' => '+40712345678',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.challenge'));

        $this->assertDatabaseHas('two_factor_challenges', ['user_id' => $user->id]);
    }

    public function test_challenge_page_requires_pending_login(): void
    {
        $this->get('/two-factor/challenge')->assertRedirect(route('login'));
    }

    public function test_code_cannot_be_reused(): void
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'phone' => '+40712345678',
        ]);

        $service = app(\App\Services\TwoFactorService::class);
        $code = $service->sendCode($user);

        $this->assertTrue($service->verify($user, $code));
        $this->assertFalse($service->verify($user, $code));
    }

    public function test_code_expires(): void
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'phone' => '+40712345678',
        ]);

        $service = app(\App\Services\TwoFactorService::class);
        $code = $service->sendCode($user);

        TwoFactorChallenge::query()->update(['expires_at' => now()->subMinute()]);

        $this->assertFalse($service->verify($user, $code));
    }
}

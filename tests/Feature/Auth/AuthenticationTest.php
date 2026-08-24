<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\Concerns\CreatesBuildingStructure;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use CreatesBuildingStructure;

    public function test_resident_can_login_with_email_and_password(): void
    {
        $user = $this->createResident();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = $this->createResident();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_blocked_user_cannot_login(): void
    {
        $user = User::factory()->blocked()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_user_can_logout(): void
    {
        $user = $this->createResident();

        $this->actingAs($user)->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.community_invitation_code' => 'VECINI2026']);
    }

    public function test_login_page_offers_registration(): void
    {
        $this->get('/login')->assertSee('Creează un cont');
    }

    public function test_verify_code_redirects_to_registration_form(): void
    {
        $this->post('/inregistrare/cod', ['code' => 'VECINI2026'])
            ->assertRedirect('/inregistrare/VECINI2026');
    }

    public function test_verify_code_rejects_invalid_code(): void
    {
        $this->post('/inregistrare/cod', ['code' => 'INVALID'])
            ->assertSessionHasErrors('code');
    }

    public function test_user_can_register_with_community_code(): void
    {
        $this->post('/inregistrare', [
            'name' => 'Utilizator Nou',
            'email' => 'nou@vecini.ro',
            'phone' => '0712345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'code' => 'VECINI2026',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'nou@vecini.ro',
            'apartment_id' => null,
            'role' => 'resident',
        ]);

        $this->assertAuthenticated();
    }
}

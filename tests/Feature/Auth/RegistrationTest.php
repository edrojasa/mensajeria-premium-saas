<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_get_redirects_to_login(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect(route('login'));
    }

    public function test_register_post_redirects_to_login_and_does_not_create_user(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'organization_name' => 'Empresa Demo SAS',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_public_registration_does_not_create_organization(): void
    {
        $response = $this->followingRedirects()->post('/register', [
            'name' => 'Test User',
            'organization_name' => 'Empresa Demo SAS',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertOk();
        $this->assertSame(0, User::query()->where('email', 'newuser@example.com')->count());
    }
}

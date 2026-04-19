<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Organizations\OrganizationRole;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'organization_name' => 'Empresa Demo SAS',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertTrue($user->organizations()->exists());
        $org = $user->organizations()->first();
        $this->assertSame('Empresa Demo SAS', $org->name);
        $this->assertSame(OrganizationRole::OWNER, $org->pivot->role);
        $this->assertDatabaseHas('organizations', ['slug' => 'empresa-demo-sas']);
    }
}

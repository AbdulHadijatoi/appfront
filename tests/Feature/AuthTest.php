<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_page_loads_correctly()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertViewIs('login');
    }

    #[Test]
    public function it_allows_a_user_to_login_with_valid_credentials()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.products'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function it_rejects_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Invalid login credentials');
        $this->assertGuest();
    }

    #[Test]
    public function it_logs_out_a_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    #[Test]
    public function it_restricts_access_to_products_page_for_guests()
    {
        $response = $this->get(route('admin.products'));
        $response->assertRedirect(route('login'));
    }
}

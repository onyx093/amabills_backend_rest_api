<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CanAuthenticateTest extends TestCase
{

    // use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install --uuids')->expectsConfirmation('In order to finish configuring client UUIDs, we need to rebuild the Passport database tables. Would you like to rollback and re-run your last migration?', 'yes');
    }

    /**
     * A basic feature test for laravel passport authentication to register a user.
     *
     * @return void
     */
    public function test_can_register_user(): void
    {

        $user = [
            "name" => "Tolu Olaoluwa",
            "email" => "tiolu@example.org",
            "password" => "password",
            "password_confirmation" => "password"
        ];

        $response = $this->json('POST', 'api/v1/register', $user);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);
    }

    /**
     * A basic feature test for laravel passport authentication to login a user and get authenticated user.
     *
     * @return void
     */
    public function test_can_login_user_and_get_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->json('POST', 'api/v1/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);

        Passport::actingAs($user);
        $response = $this->json('GET', 'api/v1/user');

        $response->assertStatus(200)
            ->assertJsonStructure(['user']);
    }

    /**
     * A basic feature test for laravel passport authentication to logout a user.
     *
     * @return void
     */
    /* public function test_can_logout_user(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        $response = $this->json('POST', 'api/v1/logout');

        $response->assertStatus(200)
            ->assertJsonStructure([]);
    } */
}

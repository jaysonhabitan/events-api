<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * Test if user can login with correct credentials.
     *
     * @return void
     */
    public function test_api_user_can_login_with_correct_credentials()
    {
        $apiEndpoint = '/api/v1/login';

        $password = $this->faker->password();

        $user = User::factory()
            ->withPassword($password)
            ->create();

        $payload = [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->postJson($apiEndpoint, $payload);
        $response->assertOk()
            ->assertJsonStructure([
            'user' => [
                'name',
                'email',
            ],
            'access_token'
        ]);
    }

    /**
     * Test if user cannot login with wrong credentials.
     *
     * @return void
     */
    public function test_api_user_cannot_login_with_wrong_credentials()
    {
        $apiEndpoint = '/api/v1/login';

        $password = $this->faker->password();
        $wrongPassword = $this->faker->word();

        $serviceUser = User::factory()
            ->withPassword($password)
            ->create();

        $payload = [
            'email' => $serviceUser->email,
            'password' => $wrongPassword,
        ];

        $response = $this->postJson($apiEndpoint, $payload);
        $response->assertUnauthorized();
    }
}

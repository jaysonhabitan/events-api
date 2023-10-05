<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        // Status should be HTTP 200 for envs w/out basic auth and HTTP 401 for envs w/ basic auth
        $this->assertTrue($response->status() == 200 || $response->status() == 401);
    }
}

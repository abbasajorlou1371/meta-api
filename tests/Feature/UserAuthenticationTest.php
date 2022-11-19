<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_users_can_login()
    {
        $response = $this->post('/api/login', [
            'email' => 'sa204@yahoo.com',
            'password' => 'dS6tMFkwANK324l53r'
        ]);

        $response->assertOk();
    }
}

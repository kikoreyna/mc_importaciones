<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guests_are_redirected_to_login_from_the_root(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_authenticated_users_are_redirected_to_reception_from_the_root(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'bodega_usa']))
            ->get('/')
            ->assertRedirect('/packages');
    }
}

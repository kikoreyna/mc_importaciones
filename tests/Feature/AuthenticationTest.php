<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_guest_is_redirected_to_login_from_application_routes(): void
    {
        $this->get(route('packages.index'))
            ->assertRedirect(route('login'));

        $this->get(route('reports.comparison'))
            ->assertRedirect(route('login'));

        $this->get(route('clients.index'))
            ->assertRedirect(route('login'));
    }
}
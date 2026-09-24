<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    public function test_dashboard_is_available_to_management_and_documentation_roles(): void
    {
        foreach (['administrador', 'supervisor', 'documentador'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('reports.comparison'))
                ->assertOk();
        }
    }

    public function test_dashboard_is_not_available_to_warehouse_roles(): void
    {
        foreach (['bodega_usa', 'bodega_mex'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('reports.comparison'))
                ->assertForbidden();
        }
    }
}
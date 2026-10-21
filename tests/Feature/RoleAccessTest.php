<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    public function test_bodega_usa_can_only_access_usa_receiving_and_registered_packages(): void
    {
        $user = User::factory()->create(['role' => 'bodega_usa']);
        $this->actingAs($user);

        $this->get(route('packages.index'))->assertOk()->assertSee('Recepción USA')->assertDontSee('Comparativo USA-MEX');
        $this->get(route('packages.registered'))->assertOk();
        $this->get('/')->assertRedirect(route('packages.index'));

        foreach ([
            route('packages.search'),
            route('bodega.index'),
            route('documentacion.index'),
            route('reports.comparison'),
            route('reports.packages'),
            route('clients.index'),
        ] as $route) {
            $this->get($route)->assertForbidden();
        }
    }

    public function test_bodega_mex_can_only_access_its_warehouse_and_read_only_comparison(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'bodega_mex']));

        $this->get(route('bodega.index'))->assertOk()->assertSee('Comparativo USA-MEX');
        $this->get(route('reports.comparison'))->assertOk();
        $this->get('/')->assertRedirect(route('reports.comparison'));

        foreach ([
            route('packages.index'),
            route('packages.registered'),
            route('documentacion.index'),
            route('reports.packages'),
            route('clients.index'),
        ] as $route) {
            $this->get($route)->assertForbidden();
        }
    }

    public function test_documentador_can_access_documentation_and_read_only_comparison_only(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'documentador']));

        $this->get(route('documentacion.index'))->assertOk()->assertSee('Comparativo USA-MEX');
        $this->get(route('reports.comparison'))->assertOk();

        foreach ([
            route('packages.index'),
            route('packages.registered'),
            route('bodega.index'),
            route('reports.packages'),
            route('clients.index'),
        ] as $route) {
            $this->get($route)->assertForbidden();
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_only_administrators_can_access_user_management(): void
    {
        $operator = User::factory()->create(['role' => 'documentador']);

        $this->actingAs($operator)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_last_administrator_cannot_be_deleted_or_downgraded(): void
    {
        $administrator = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($administrator)
            ->put(route('users.update', $administrator), [
                'name' => $administrator->name,
                'email' => $administrator->email,
                'role' => 'documentador',
            ])
            ->assertRedirect();

        $this->assertSame('administrador', $administrator->fresh()->role);

        $this->actingAs($administrator)
            ->delete(route('users.destroy', $administrator))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $administrator->id]);
    }
}
<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Partner;
use App\Models\Transportadora;
use App\Models\User;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    public function test_client_partner_user_and_carrier_changes_are_logged_with_actor_and_safe_field_changes(): void
    {
        $actor = User::factory()->create(['name' => 'Admin Auditor', 'role' => 'administrador']);
        $this->actingAs($actor);

        $partner = Partner::create(['nombre' => 'Socio inicial']);
        $partner->update(['nombre' => 'Socio actualizado']);

        $client = Client::create(['nombre' => 'Cliente inicial', 'partner_id' => $partner->id]);
        $client->update(['nombre' => 'Cliente actualizado']);

        $carrier = Transportadora::create([
            'nombre' => 'Transportadora inicial',
            'web' => 'https://inicial.example',
        ]);
        $carrier->update(['nombre' => 'Transportadora actualizada']);

        $user = User::factory()->create(['name' => 'Usuario inicial', 'role' => 'documentador']);
        $user->update([
            'name' => 'Usuario actualizado',
            'password' => 'a-new-password-hash',
        ]);

        foreach ([
            [$partner, 'nombre', 'Socio inicial', 'Socio actualizado'],
            [$client, 'nombre', 'Cliente inicial', 'Cliente actualizado'],
            [$carrier, 'nombre', 'Transportadora inicial', 'Transportadora actualizada'],
            [$user, 'name', 'Usuario inicial', 'Usuario actualizado'],
        ] as [$subject, $field, $oldValue, $newValue]) {
            $log = ActivityLog::query()
                ->where('subject_type', $subject::class)
                ->where('subject_id', $subject->id)
                ->where('action', 'updated')
                ->latest('id')
                ->firstOrFail();

            $this->assertSame($actor->id, $log->actor_user_id);
            $this->assertSame('Admin Auditor', $log->actor_name);
            $this->assertSame('administrador', $log->actor_role);
            $this->assertSame([$oldValue, $newValue], $log->changes[$field]);
        }

        $passwordChange = ActivityLog::query()
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->where('action', 'updated')
            ->latest('id')
            ->firstOrFail();

        $this->assertSame(['[REDACTED]', '[REDACTED]'], $passwordChange->changes['password']);
        $this->assertStringNotContainsString('a-new-password-hash', json_encode($passwordChange->changes));
    }

    public function test_soft_deletions_are_logged_for_all_audited_entities(): void
    {
        $actor = User::factory()->create(['role' => 'administrador']);
        $this->actingAs($actor);

        $partner = Partner::create(['nombre' => 'Socio eliminado']);
        $client = Client::create(['nombre' => 'Cliente eliminado']);
        $carrier = Transportadora::create([
            'nombre' => 'Transportadora eliminada',
            'web' => 'https://eliminada.example',
        ]);
        $user = User::factory()->create(['role' => 'documentador']);

        foreach ([$partner, $client, $carrier, $user] as $subject) {
            $subject->delete();

            $this->assertDatabaseHas('activity_logs', [
                'actor_user_id' => $actor->id,
                'subject_type' => $subject::class,
                'subject_id' => $subject->id,
                'action' => 'deleted',
            ]);
        }
    }

    public function test_only_administrators_can_view_and_filter_the_activity_log(): void
    {
        $administrator = User::factory()->create(['role' => 'administrador']);
        $this->actingAs($administrator);
        $partner = Partner::create(['nombre' => 'Registro del historial']);
        $partner->update(['nombre' => 'Nombre actualizado']);

        $this->actingAs($administrator)
            ->get(route('activity-logs.index', ['entity' => 'partner', 'action' => 'updated']))
            ->assertOk()
            ->assertSee('Historial de cambios')
            ->assertSee('Socio #')
            ->assertSee('Registro del historial')
            ->assertSee('Nombre actualizado');

        $this->actingAs(User::factory()->create(['role' => 'supervisor']))
            ->get(route('activity-logs.index'))
            ->assertForbidden();
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\Admin\DashboardAdmin;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardAdminSolicitudesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_only_shows_pending_inscription_requests_in_the_quick_list(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'apellido' => 'Test',
            'role' => 'admin',
        ]);

        $inscriptionRequest = Solicitud::create([
            'tipo' => 'inscripcion',
            'asunto' => 'Solicitud de prueba de inscripción',
            'mensaje' => 'Mensaje de prueba',
            'email_contacto' => 'estudiante@example.com',
            'estado' => 'pendiente',
            'user_id' => $admin->id,
        ]);

        $otherRequest = Solicitud::create([
            'tipo' => 'problema',
            'asunto' => 'Solicitud de problema',
            'mensaje' => 'Mensaje de otra solicitud',
            'email_contacto' => 'otro@example.com',
            'estado' => 'pendiente',
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin);

        Livewire::test(DashboardAdmin::class)
            ->assertSee($inscriptionRequest->asunto)
            ->assertDontSee($otherRequest->asunto);
    }
}

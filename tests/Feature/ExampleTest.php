<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
//use RefreshDatabase; // pendiente por resolver

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        
        // crear un usuario para el test
        $user = User::factory()->create();

        // delete() en un modelo con SoftDeletes no elimina el registro de la base de datos, sino que establece el campo deleted_at
        $user->delete();
        
        // Ahora sí, verificar que está soft-deleted
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        
        // Alternativa usando el modelo directamente:
        $this->assertSoftDeleted($user);
    }
}

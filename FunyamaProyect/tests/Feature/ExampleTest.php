<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

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
        
        // Ahora sí, verificar que está soft-deleted
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        
        // Alternativa usando el modelo directamente:
        $this->assertSoftDeleted($user);
    }
}

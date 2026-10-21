<?php

namespace Tests\Feature;

use App\Models\Soggetto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoggettoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(\App\Models\User::factory()->create());
    }

    public function test_soggetto_viene_creato_e_listato(): void
    {
        $soggetto = Soggetto::create([
            'ragione_sociale' => 'Rossi & C. snc',
            'partita_iva' => 'IT12345678901',
            'is_cliente' => true,
            'attivo' => true,
        ]);

        $response = $this->get('/soggetti');

        $response->assertStatus(200);
        $response->assertSee('Rossi & C. snc');
        $this->assertDatabaseHas('soggetti', ['id' => $soggetto->id]);
    }

    public function test_soggetto_deve_essere_cliente_o_fornitore(): void
    {
        $response = $this->post('/soggetti', [
            'ragione_sociale' => 'X',
            'is_cliente' => false,
            'is_fornitore' => false,
            'attivo' => true,
        ]);

        $response->assertSessionHasErrors('is_cliente');
        $this->assertDatabaseMissing('soggetti', ['ragione_sociale' => 'X']);
    }
}

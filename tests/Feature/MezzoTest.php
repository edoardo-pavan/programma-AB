<?php

namespace Tests\Feature;

use App\Models\Mezzo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MezzoTest extends TestCase
{
    use RefreshDatabase;

    public function test_mezzo_viene_creato_e_listato(): void
    {
        $mezzo = Mezzo::create([
            'tipo' => 'trattore',
            'targa' => 'AA000BB',
            'marca' => 'Iveco',
            'modello' => 'Stralis',
            'attivo' => true,
        ]);

        $response = $this->get('/mezzi');

        $response->assertStatus(200);
        $response->assertSee('AA000BB');
        $this->assertDatabaseHas('mezzi', ['id' => $mezzo->id]);
    }

    public function test_mezzo_viene_aggiornato(): void
    {
        $mezzo = Mezzo::create([
            'tipo' => 'trattore',
            'targa' => 'AA000BB',
            'attivo' => true,
        ]);

        $this->put('/mezzi/' . $mezzo->id, [
            'tipo' => 'motrice',
            'targa' => 'AA001CC',
            'marca' => 'Mercedes',
            'modello' => 'Actros',
            'portata_t' => 18.0,
            'attivo' => true,
        ])->assertRedirect('/mezzi');

        $this->assertDatabaseHas('mezzi', [
            'id' => $mezzo->id,
            'targa' => 'AA001CC',
            'tipo' => 'motrice',
        ]);
    }
}

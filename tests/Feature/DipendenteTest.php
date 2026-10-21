<?php

namespace Tests\Feature;

use App\Models\Dipendente;
use App\Models\Mezzo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DipendenteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(\App\Models\User::factory()->create());
    }

    public function test_dipendente_viene_creato_e_listato(): void
    {
        $dipendente = Dipendente::create([
            'nome' => 'Mario',
            'cognome' => 'Rossi',
            'codice_fiscale' => 'RSSMRA80A01H501U',
            'mansione' => 'autista',
            'attivo' => true,
        ]);

        $response = $this->get('/dipendenti');

        $response->assertStatus(200);
        $response->assertSee('Rossi');
        $this->assertDatabaseHas('dipendenti', ['id' => $dipendente->id]);
    }

    public function test_dipendente_viene_aggiornato(): void
    {
        $dipendente = Dipendente::create([
            'nome' => 'Mario',
            'cognome' => 'Rossi',
            'codice_fiscale' => 'RSSMRA80A01H501U',
            'mansione' => 'ufficio',
            'attivo' => true,
        ]);

        $this->put('/dipendenti/' . $dipendente->id, [
            'nome' => 'Mario',
            'cognome' => 'Rossi',
            'codice_fiscale' => 'RSSMRA80A01H501U',
            'mansione' => 'autista',
            'data_assunzione' => '2020-01-01',
            'attivo' => true,
        ])->assertRedirect('/dipendenti');

        $this->assertDatabaseHas('dipendenti', [
            'id' => $dipendente->id,
            'mansione' => 'autista',
        ]);
    }

    public function test_mezzo_trattore_viene_collegato_al_dipendente(): void
    {
        $trattore = Mezzo::create(['tipo' => 'trattore', 'targa' => 'AA000BB', 'attivo' => true]);
        $dipendente = Dipendente::create([
            'nome' => 'Mario',
            'cognome' => 'Rossi',
            'codice_fiscale' => 'RSSMRA80A01H501U',
            'mansione' => 'autista',
            'trattore_id' => $trattore->id,
            'attivo' => true,
        ]);

        $this->assertInstanceOf(Mezzo::class, $dipendente->trattore);
        $this->assertSame($trattore->id, $dipendente->trattore_id);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Offerta;
use App\Models\Soggetto;
use App\Models\Viaggio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OffertaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(\App\Models\User::factory()->create());
    }

    public function test_offerta_viene_creata_e_listata(): void
    {
        $cliente = Soggetto::create([
            'ragione_sociale' => 'Edilizia Rossi srl',
            'partita_iva' => 'IT12345678901',
            'is_cliente' => true,
            'attivo' => true,
        ]);

        $offerta = Offerta::create([
            'soggetto_id' => $cliente->id,
            'origine' => 'Milano',
            'destinazione' => 'Roma',
            'merce' => 'Materiale edile',
            'prezzo_base' => 200.00,
            'tipo_prezzo' => 'viaggio',
            'data_validita' => now()->addDays(30),
            'stato' => 'inviata',
        ]);

        $response = $this->get('/offerte');

        $response->assertStatus(200);
        $response->assertSee('Edilizia Rossi srl');
        $response->assertSee('Milano → Roma');
        $this->assertDatabaseHas('offerte', ['id' => $offerta->id]);
    }

    public function test_offerta_viene_aggiornata(): void
    {
        $cliente = Soggetto::create([
            'ragione_sociale' => 'Edilizia Rossi srl',
            'partita_iva' => 'IT12345678901',
            'is_cliente' => true,
            'attivo' => true,
        ]);

        $offerta = Offerta::create([
            'soggetto_id' => $cliente->id,
            'origine' => 'Milano',
            'destinazione' => 'Roma',
            'prezzo_base' => 200.00,
            'tipo_prezzo' => 'viaggio',
            'data_validita' => now()->addDays(30),
            'stato' => 'bozza',
        ]);

        $this->put('/offerte/' . $offerta->id, [
            'soggetto_id' => $cliente->id,
            'origine' => 'Torino',
            'destinazione' => 'Napoli',
            'prezzo_base' => 250.00,
            'tipo_prezzo' => 'viaggio',
            'data_validita' => now()->addDays(30)->format('Y-m-d'),
            'stato' => 'bozza',
            'peso_minimo_fatturabile' => 0,
            'fermo_macchina_ore' => 0,
            'prezzo_fermo_macchina_ora' => 0,
            'supplementi' => 0,
        ])->assertRedirect('/offerte/' . $offerta->id);

        $this->assertDatabaseHas('offerte', [
            'id' => $offerta->id,
            'origine' => 'Torino',
            'destinazione' => 'Napoli',
            'prezzo_base' => 250.00,
        ]);
    }

    public function test_offerta_accettata_crea_viaggio_bozza(): void
    {
        $cliente = Soggetto::create([
            'ragione_sociale' => 'Logistica Verdi spa',
            'partita_iva' => 'IT09876543210',
            'is_cliente' => true,
            'attivo' => true,
        ]);

        $offerta = Offerta::create([
            'soggetto_id' => $cliente->id,
            'origine' => 'Genova',
            'destinazione' => 'Venezia',
            'prezzo_base' => 300.00,
            'tipo_prezzo' => 'tonnellata',
            'peso_minimo_fatturabile' => 12.00,
            'data_validita' => now()->addDays(30),
            'stato' => 'inviata',
            'totale' => 300.00,
        ]);

        $this->post('/offerte/' . $offerta->id . '/accetta')
            ->assertRedirect('/offerte/' . $offerta->id);

        $this->assertDatabaseHas('offerte', [
            'id' => $offerta->id,
            'stato' => 'accettata',
        ]);

        $this->assertDatabaseHas('viaggi', [
            'offerta_id' => $offerta->id,
            'origine' => 'Genova',
            'destinazione' => 'Venezia',
            'stato' => 'bozza',
            'prezzo_concordato' => 300.00,
        ]);

        $this->assertInstanceOf(Viaggio::class, $offerta->fresh()->viaggio);
    }

    public function test_offerta_rifiutata(): void
    {
        $cliente = Soggetto::create([
            'ragione_sociale' => 'Trasporti Blu srl',
            'partita_iva' => 'IT11223344556',
            'is_cliente' => true,
            'attivo' => true,
        ]);

        $offerta = Offerta::create([
            'soggetto_id' => $cliente->id,
            'origine' => 'Bologna',
            'destinazione' => 'Firenze',
            'prezzo_base' => 100.00,
            'tipo_prezzo' => 'viaggio',
            'data_validita' => now()->addDays(30),
            'stato' => 'bozza',
        ]);

        $this->post('/offerte/' . $offerta->id . '/rifiuta')
            ->assertRedirect('/offerte/' . $offerta->id);

        $this->assertDatabaseHas('offerte', [
            'id' => $offerta->id,
            'stato' => 'rifiutata',
        ]);
    }

    public function test_offerta_non_accettabile_se_già_accettata(): void
    {
        $cliente = Soggetto::create([
            'ragione_sociale' => 'Test srl',
            'partita_iva' => 'IT00000000000',
            'is_cliente' => true,
            'attivo' => true,
        ]);

        $offerta = Offerta::create([
            'soggetto_id' => $cliente->id,
            'origine' => 'A',
            'destinazione' => 'B',
            'prezzo_base' => 100.00,
            'tipo_prezzo' => 'viaggio',
            'data_validita' => now()->addDays(30),
            'stato' => 'accettata',
        ]);

        $response = $this->post('/offerte/' . $offerta->id . '/accetta');

        $response->assertSessionHasErrors('stato');
        $this->assertDatabaseHas('offerte', [
            'id' => $offerta->id,
            'stato' => 'accettata',
        ]);
    }
}

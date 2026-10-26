<?php

namespace Tests\Feature;

use App\Models\Luogo;
use App\Models\Offerta;
use App\Models\Soggetto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OffertaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_elenco_offerte_richiede_autenticazione(): void
    {
        $this->get('/offerte')->assertRedirect('/login');
    }

    public function test_offerta_viene_creata_come_bozza(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $partenza = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);
        $destinazione = Luogo::factory()->create(['nome' => 'Roma', 'comune' => 'Roma', 'attivo' => true]);

        $this->post('/offerte', [
            'data_offerta' => '2026-10-22',
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'tipo_mezzo' => '4 ASSI',
            'tipo_prezzo' => 'a_tonnellata',
            'prezzo' => 20.00,
            'minimo_fatturabile' => 25.00,
            'fermo_orario' => 75.00,
            'fermo_franchigia_ore' => 1.0,
            'pagamento' => 'RB 60+10 gg. d.f.f.m.',
            'azione' => 'bozza',
        ])->assertRedirect();

        $this->assertDatabaseHas('offerte', [
            'stato' => 'bozza',
            'tipo_mezzo' => '4 ASSI',
            'tipo_prezzo' => 'a_tonnellata',
            'prezzo' => 20.00,
            'minimo_fatturabile' => 25.00,
            'numero' => null,
        ]);
    }

    public function test_offerta_pubblicata_riceve_numero_progressivo(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $partenza = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);
        $destinazione = Luogo::factory()->create(['nome' => 'Roma', 'comune' => 'Roma', 'attivo' => true]);

        $offerta = Offerta::factory()->make([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'bozza',
        ]);
        $offerta->save();

        $this->post('/offerte/' . $offerta->id . '/pubblica')
            ->assertRedirect('/offerte/' . $offerta->id)
            ->assertSessionHas('ok');

        $this->assertDatabaseHas('offerte', [
            'id' => $offerta->id,
            'stato' => 'attiva',
            'numero' => 1,
        ]);

        $this->assertNotNull($offerta->fresh()->pubblicata_il);
    }

    public function test_seconda_offerta_pubblicata_riceve_numero_incrementato(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $partenza = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);
        $destinazione = Luogo::factory()->create(['nome' => 'Roma', 'comune' => 'Roma', 'attivo' => true]);

        $offerta1 = Offerta::factory()->make([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'bozza',
        ]);
        $offerta1->save();
        $this->post('/offerte/' . $offerta1->id . '/pubblica');

        $offerta2 = Offerta::factory()->make([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'bozza',
        ]);
        $offerta2->save();
        $this->post('/offerte/' . $offerta2->id . '/pubblica');

        $this->assertDatabaseHas('offerte', ['id' => $offerta1->id, 'numero' => 1]);
        $this->assertDatabaseHas('offerte', ['id' => $offerta2->id, 'numero' => 2]);
    }

    public function test_offerta_pubblicata_non_si_può_modificare(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $partenza = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);
        $destinazione = Luogo::factory()->create(['nome' => 'Roma', 'comune' => 'Roma', 'attivo' => true]);

        $offerta = Offerta::factory()->make([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'attiva',
            'numero' => 1,
        ]);
        $offerta->save();

        $this->put('/offerte/' . $offerta->id, [
            'data_offerta' => '2026-10-22',
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'tipo_mezzo' => 'BILICO',
            'tipo_prezzo' => 'a_tonnellata',
            'prezzo' => 30.00,
            'fermo_orario' => 75.00,
            'fermo_franchigia_ore' => 1.0,
            'pagamento' => 'RB 60+10 gg. d.f.f.m.',
        ])->assertRedirect('/offerte/' . $offerta->id)->assertSessionHas('errore');
    }

    public function test_offerta_pubblicata_non_si_può_eliminare(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $partenza = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);
        $destinazione = Luogo::factory()->create(['nome' => 'Roma', 'comune' => 'Roma', 'attivo' => true]);

        $offerta = Offerta::factory()->make([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'attiva',
            'numero' => 1,
        ]);
        $offerta->save();

        $this->delete('/offerte/' . $offerta->id)
            ->assertRedirect('/offerte/' . $offerta->id)
            ->assertSessionHas('errore');

        $this->assertDatabaseHas('offerte', ['id' => $offerta->id]);
    }

    public function test_bozza_si_può_eliminare(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $partenza = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);
        $destinazione = Luogo::factory()->create(['nome' => 'Roma', 'comune' => 'Roma', 'attivo' => true]);

        $offerta = Offerta::factory()->make([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'bozza',
        ]);
        $offerta->save();

        $this->delete('/offerte/' . $offerta->id)
            ->assertRedirect('/offerte?stato=bozze')
            ->assertSessionHas('ok');

        $this->assertDatabaseMissing('offerte', ['id' => $offerta->id]);
    }

    public function test_copia_offerta_crea_nuova_bozza(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $partenza = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);
        $destinazione = Luogo::factory()->create(['nome' => 'Roma', 'comune' => 'Roma', 'attivo' => true]);

        $originale = Offerta::factory()->make([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'attiva',
            'numero' => 5,
            'prezzo' => 20.00,
        ]);
        $originale->save();

        $this->post('/offerte/' . $originale->id . '/copia')
            ->assertRedirect()->assertSessionHas('ok');

        $this->assertDatabaseHas('offerte', [
            'offerta_origine_id' => $originale->id,
            'stato' => 'bozza',
            'numero' => null,
            'prezzo' => 20.00,
        ]);
    }

    public function test_offerta_attiva_si_può_archiviare(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $partenza = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);
        $destinazione = Luogo::factory()->create(['nome' => 'Roma', 'comune' => 'Roma', 'attivo' => true]);

        $offerta = Offerta::factory()->make([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'attiva',
            'numero' => 1,
        ]);
        $offerta->save();

        $this->post('/offerte/' . $offerta->id . '/archivia')
            ->assertRedirect()->assertSessionHas('ok');

        $this->assertDatabaseHas('offerte', ['id' => $offerta->id, 'stato' => 'archiviata']);
    }

    public function test_offerta_archiviata_si_può_riattivare(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $partenza = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);
        $destinazione = Luogo::factory()->create(['nome' => 'Roma', 'comune' => 'Roma', 'attivo' => true]);

        $offerta = Offerta::factory()->make([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'archiviata',
            'numero' => 1,
        ]);
        $offerta->save();

        $this->post('/offerte/' . $offerta->id . '/riattiva')
            ->assertRedirect()->assertSessionHas('ok');

        $this->assertDatabaseHas('offerte', ['id' => $offerta->id, 'stato' => 'attiva']);
    }

    public function test_elenco_offerta_filtra_per_stato(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $partenza = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);
        $destinazione = Luogo::factory()->create(['nome' => 'Roma', 'comune' => 'Roma', 'attivo' => true]);

        Offerta::factory()->count(3)->create([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'attiva',
        ]);

        Offerta::factory()->count(2)->create([
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $partenza->id,
            'destinazione_luogo_id' => $destinazione->id,
            'stato' => 'bozza',
        ]);

        $response = $this->get('/offerte?stato=bozze');

        $response->assertStatus(200);
        $response->assertSee('Bozze');
    }

    public function test_validazione_partenza_diversa_da_destinazione(): void
    {
        $cliente = Soggetto::factory()->create(['is_cliente' => true, 'attivo' => true]);
        $luogo = Luogo::factory()->create(['nome' => 'Milano', 'comune' => 'Milano', 'attivo' => true]);

        $this->post('/offerte', [
            'data_offerta' => '2026-10-22',
            'cliente_id' => $cliente->id,
            'partenza_luogo_id' => $luogo->id,
            'destinazione_luogo_id' => $luogo->id,
            'tipo_mezzo' => '4 ASSI',
            'tipo_prezzo' => 'a_tonnellata',
            'prezzo' => 20.00,
            'minimo_fatturabile' => 25.00,
            'fermo_orario' => 75.00,
            'fermo_franchigia_ore' => 1.0,
            'pagamento' => 'RB 60+10 gg. d.f.f.m.',
        ])->assertSessionHasErrors('destinazione_luogo_id');
    }
}

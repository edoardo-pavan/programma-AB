<?php

namespace Tests\Feature;

use App\Models\Dipendente;
use App\Models\Documento;
use App\Models\Mezzo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(\App\Models\User::factory()->create());
    }

    public function test_documento_viene_creato_allegato_al_mezzo(): void
    {
        $mezzo = Mezzo::create(['tipo' => 'trattore', 'targa' => 'AA000BB', 'attivo' => true]);

        $this->post('/mezzi/' . $mezzo->id . '/documenti', [
            'doc' => [
                'tipo' => 'assicurazione',
                'numero' => 'POL-123',
                'data_emissione' => '2024-01-01',
                'data_scadenza' => '2025-01-01',
                'note' => 'Polizza RCA',
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('documenti', [
            'documentabile_type' => 'mezzo',
            'documentabile_id' => $mezzo->id,
            'tipo' => 'assicurazione',
            'numero' => 'POL-123',
        ]);
    }

    public function test_documento_viene_creato_allegato_al_dipendente(): void
    {
        $dipendente = Dipendente::create([
            'nome' => 'Mario',
            'cognome' => 'Rossi',
            'codice_fiscale' => 'RSSMRA80A01H501U',
            'mansione' => 'autista',
            'attivo' => true,
        ]);

        $this->post('/dipendenti/' . $dipendente->id . '/documenti', [
            'doc' => [
                'tipo' => 'patente',
                'numero' => 'PT123456',
                'data_emissione' => '2020-01-01',
                'data_scadenza' => '2030-01-01',
                'note' => 'Patente B',
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('documenti', [
            'documentabile_type' => 'dipendente',
            'documentabile_id' => $dipendente->id,
            'tipo' => 'patente',
        ]);
    }

    public function test_documento_suddiviso_separa_corrente_e_storico(): void
    {
        $mezzo = Mezzo::create(['tipo' => 'trattore', 'targa' => 'AA000BB', 'attivo' => true]);

        Documento::create([
            'documentabile_type' => 'mezzo',
            'documentabile_id' => $mezzo->id,
            'tipo' => 'assicurazione',
            'data_scadenza' => now()->addDays(10),
            'titolo' => 'Polizza vecchia',
        ]);

        Documento::create([
            'documentabile_type' => 'mezzo',
            'documentabile_id' => $mezzo->id,
            'tipo' => 'assicurazione',
            'data_scadenza' => now()->addDays(20),
            'titolo' => 'Polizza nuova',
        ]);

        $sezioni = $mezzo->documentiSuddivisi();

        $this->assertCount(1, $sezioni['correnti']);
        $this->assertCount(1, $sezioni['storico']);
        $this->assertSame('Polizza nuova', $sezioni['correnti']->first()->titolo);
        $this->assertSame('Polizza vecchia', $sezioni['storico']->first()->titolo);
    }
}

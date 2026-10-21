<?php

namespace Tests\Unit;

use App\Models\Dipendente;
use App\Models\Documento;
use App\Models\Mezzo;
use App\Services\ScadenzeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScadenzeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_correnti_restituisce_solo_documenti_con_scadenza(): void
    {
        $mezzo = Mezzo::create(['tipo' => 'trattore', 'targa' => 'AA000BB', 'attivo' => true]);

        Documento::create([
            'documentabile_type' => Mezzo::class,
            'documentabile_id' => $mezzo->id,
            'tipo' => 'assicurazione',
            'data_scadenza' => now()->addDays(10),
        ]);

        Documento::create([
            'documentabile_type' => Mezzo::class,
            'documentabile_id' => $mezzo->id,
            'tipo' => 'bollo',
            'data_scadenza' => null,
        ]);

        $correnti = ScadenzeService::correnti();

        $this->assertCount(1, $correnti);
        $this->assertSame('assicurazione', $correnti->first()->tipo);
    }

    public function test_correnti_esclude_proprietari_non_attivi(): void
    {
        $mezzoInattivo = Mezzo::create(['tipo' => 'trattore', 'targa' => 'AA000BB', 'attivo' => false]);
        $mezzoAttivo = Mezzo::create(['tipo' => 'motrice', 'targa' => 'AA001CC', 'attivo' => true]);

        Documento::create([
            'documentabile_type' => Mezzo::class,
            'documentabile_id' => $mezzoInattivo->id,
            'tipo' => 'assicurazione',
            'data_scadenza' => now()->addDays(10),
        ]);

        Documento::create([
            'documentabile_type' => Mezzo::class,
            'documentabile_id' => $mezzoAttivo->id,
            'tipo' => 'assicurazione',
            'data_scadenza' => now()->addDays(20),
        ]);

        $correnti = ScadenzeService::correnti();

        $this->assertCount(1, $correnti);
        $this->assertSame($mezzoAttivo->id, $correnti->first()->documentabile_id);
    }

    public function test_correnti_ordina_per_urgenza(): void
    {
        $mezzo = Mezzo::create(['tipo' => 'trattore', 'targa' => 'AA000BB', 'attivo' => true]);

        Documento::create([
            'documentabile_type' => Mezzo::class,
            'documentabile_id' => $mezzo->id,
            'tipo' => 'assicurazione',
            'data_scadenza' => now()->addDays(60),
        ]);

        Documento::create([
            'documentabile_type' => Mezzo::class,
            'documentabile_id' => $mezzo->id,
            'tipo' => 'bollo',
            'data_scadenza' => now()->addDays(5),
        ]);

        $correnti = ScadenzeService::correnti();

        $this->assertCount(2, $correnti);
        $this->assertSame('bollo', $correnti->first()->tipo);
        $this->assertSame('assicurazione', $correnti->last()->tipo);
    }
}

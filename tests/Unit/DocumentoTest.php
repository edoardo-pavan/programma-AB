<?php

namespace Tests\Unit;

use App\Models\Documento;
use PHPUnit\Framework\TestCase;

class DocumentoTest extends TestCase
{
    public function test_stato_scaduto_quando_giorni_negativi(): void
    {
        $documento = new Documento(['data_scadenza' => now()->subDay()]);

        $this->assertSame('scaduto', $documento->stato);
    }

    public function test_stato_in_scadenza_entro_30_giorni(): void
    {
        $documento = new Documento(['data_scadenza' => now()->addDays(10)]);

        $this->assertSame('in_scadenza', $documento->stato);
    }

    public function test_stato_valido_oltre_30_giorni(): void
    {
        $documento = new Documento(['data_scadenza' => now()->addDays(60)]);

        $this->assertSame('valido', $documento->stato);
    }

    public function test_stato_senza_scadenza_quando_data_null(): void
    {
        $documento = new Documento(['data_scadenza' => null]);

        $this->assertSame('senza_scadenza', $documento->stato);
    }

    public function test_classe_badge_per_stato(): void
    {
        $scaduto = new Documento(['data_scadenza' => now()->subDay()]);
        $inScadenza = new Documento(['data_scadenza' => now()->addDays(10)]);
        $valido = new Documento(['data_scadenza' => now()->addDays(60)]);
        $nessuna = new Documento(['data_scadenza' => null]);

        $this->assertSame('text-bg-danger', $scaduto->classe_badge);
        $this->assertSame('text-bg-warning', $inScadenza->classe_badge);
        $this->assertSame('text-bg-success', $valido->classe_badge);
        $this->assertSame('text-bg-secondary', $nessuna->classe_badge);
    }

    public function test_testo_scadenza_scaduto_da_piu_giorni(): void
    {
        $documento = new Documento(['data_scadenza' => now()->subDays(5)]);

        $this->assertSame('Scaduto da 5 giorni', $documento->testo_scadenza);
    }

    public function test_testo_scadenza_scade_oggi(): void
    {
        $documento = new Documento(['data_scadenza' => now()]);

        $this->assertSame('Scade oggi', $documento->testo_scadenza);
    }

    public function test_giorni_mancanti_null_quando_senza_scadenza(): void
    {
        $documento = new Documento(['data_scadenza' => null]);

        $this->assertNull($documento->giorni_mancanti);
    }
}

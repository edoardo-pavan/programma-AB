<?php

namespace Tests\Unit;

use App\Models\Offerta;
use PHPUnit\Framework\TestCase;

class OffertaPrezzoTest extends TestCase
{
    public function test_tipo_prezzo_viaggio_non_ha_minimo_fatturabile(): void
    {
        $offerta = new Offerta([
            'prezzo' => 150.00,
            'tipo_prezzo' => 'a_viaggio',
            'fermo_orario' => 2.0,
            'fermo_franchigia_ore' => 1.0,
            'minimo_fatturabile' => null,
        ]);

        $this->assertNull($offerta->minimo_fatturabile);
        $this->assertSame('€ 150,00/viaggio', $offerta->prezzo_formattato);
    }

    public function test_tipo_prezzo_tonnellata_con_minimo_fatturabile(): void
    {
        $offerta = new Offerta([
            'prezzo' => 20.00,
            'tipo_prezzo' => 'a_tonnellata',
            'minimo_fatturabile' => 30.00,
            'fermo_orario' => 0,
            'fermo_franchigia_ore' => 1.0,
        ]);

        $this->assertSame('30 t', $offerta->testo_minimo);
        $this->assertSame('€ 20,00/t', $offerta->prezzo_formattato);
    }

    public function test_minimo_predefinito_per_tipo_mezzo(): void
    {
        $this->assertSame(30, Offerta::MINIMO_PREDEFINITO['BILICO']);
        $this->assertSame(30, Offerta::MINIMO_PREDEFINITO['BILICO RIBALTABILE']);
        $this->assertSame(25, Offerta::MINIMO_PREDEFINITO['4 ASSI']);
    }

    public function test_fermo_predefinito_e_franchigia(): void
    {
        $this->assertSame(75, Offerta::FERMO_PREDEFINITO);
        $this->assertSame(1, Offerta::FRANCHIGIA_PREDEFINITA);
    }

    public function test_testo_fermo_con_franchigia_un_ora(): void
    {
        $offerta = new Offerta([
            'fermo_orario' => 75.00,
            'fermo_franchigia_ore' => 1.0,
        ]);

        $this->assertSame('€ 75,00/h dopo la 1ª ora', $offerta->testo_fermo);
    }

    public function test_testo_fermo_senza_franchigia(): void
    {
        $offerta = new Offerta([
            'fermo_orario' => 50.00,
            'fermo_franchigia_ore' => 0,
        ]);

        $this->assertSame('€ 50,00/h', $offerta->testo_fermo);
    }

    public function test_numero_formattato_bozza(): void
    {
        $offerta = new Offerta(['numero' => null]);

        $this->assertSame('Bozza', $offerta->numero_formattato);
    }

    public function test_numero_formattato_pubblicata(): void
    {
        $offerta = new Offerta(['numero' => 7]);

        $this->assertSame('00007', $offerta->numero_formattato);
    }

    public function test_stati_offerta(): void
    {
        $this->assertArrayHasKey('bozza', Offerta::STATI);
        $this->assertArrayHasKey('attiva', Offerta::STATI);
        $this->assertArrayHasKey('scaduta', Offerta::STATI);
        $this->assertArrayHasKey('archiviata', Offerta::STATI);
    }

    public function test_classe_stato(): void
    {
        $bozza = new Offerta(['stato' => 'bozza']);
        $attiva = new Offerta(['stato' => 'attiva']);
        $scaduta = new Offerta(['stato' => 'scaduta']);
        $archiviata = new Offerta(['stato' => 'archiviata']);

        $this->assertSame('text-bg-secondary', $bozza->classe_stato);
        $this->assertSame('text-bg-success', $attiva->classe_stato);
        $this->assertSame('text-bg-warning', $scaduta->classe_stato);
        $this->assertSame('text-bg-dark', $archiviata->classe_stato);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Offerta;
use PHPUnit\Framework\TestCase;

class OffertaPrezzoTest extends TestCase
{
    public function test_tipo_prezzo_viaggio_totale_uguale_prezzo_base_piu_fermo_piu_supplementi(): void
    {
        $offerta = new Offerta([
            'prezzo_base' => 150.00,
            'tipo_prezzo' => 'viaggio',
            'fermo_macchina_ore' => 2.0,
            'prezzo_fermo_macchina_ora' => 40.00,
            'supplementi' => 25.00,
        ]);

        $this->assertSame(255.00, $offerta->calcolaTotale());
    }

    public function test_tipo_prezzo_tonnellata_usa_peso_minimo_fatturabile_se_peso_min_inesistente(): void
    {
        $offerta = new Offerta([
            'prezzo_base' => 20.00,
            'tipo_prezzo' => 'tonnellata',
            'peso_minimo_fatturabile' => 10.00,
            'peso_min' => null,
            'fermo_macchina_ore' => 0,
            'prezzo_fermo_macchina_ora' => 0,
            'supplementi' => 0,
        ]);

        // 10 t × 20 €/t = 200 €
        $this->assertSame(200.00, $offerta->calcolaTotale());
    }

    public function test_tipo_prezzo_tonnellata_usa_peso_min_se_superiore_al_minimo_fatturabile(): void
    {
        $offerta = new Offerta([
            'prezzo_base' => 20.00,
            'tipo_prezzo' => 'tonnellata',
            'peso_minimo_fatturabile' => 10.00,
            'peso_min' => 18.00,
            'fermo_macchina_ore' => 0,
            'prezzo_fermo_macchina_ora' => 0,
            'supplementi' => 0,
        ]);

        // 18 t × 20 €/t = 360 €
        $this->assertSame(360.00, $offerta->calcolaTotale());
    }

    public function test_fermo_macchina_si_somma_al_totale(): void
    {
        $offerta = new Offerta([
            'prezzo_base' => 100.00,
            'tipo_prezzo' => 'viaggio',
            'fermo_macchina_ore' => 3.5,
            'prezzo_fermo_macchina_ora' => 50.00,
            'supplementi' => 0,
        ]);

        // 100 + (3.5 × 50) = 275
        $this->assertSame(275.00, $offerta->calcolaTotale());
    }

    public function test_supplementi_si_sommano_al_totale(): void
    {
        $offerta = new Offerta([
            'prezzo_base' => 100.00,
            'tipo_prezzo' => 'viaggio',
            'fermo_macchina_ore' => 0,
            'prezzo_fermo_macchina_ora' => 0,
            'supplementi' => 75.00,
        ]);

        $this->assertSame(175.00, $offerta->calcolaTotale());
    }

    public function test_peso_minimo_fatturabile_default_zero(): void
    {
        $offerta = new Offerta([
            'prezzo_base' => 100.00,
            'tipo_prezzo' => 'viaggio',
        ]);

        $this->assertSame(0, $offerta->peso_minimo_fatturabile);
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\HaDocumenti;
use Illuminate\Database\Eloquent\Model;

class Mezzo extends Model
{
    use HaDocumenti;

    protected $table = 'mezzi';

    protected $guarded = [];

    protected $casts = [
        'adr' => 'boolean',
        'attivo' => 'boolean',
        'data_immatricolazione' => 'date',
    ];

    public const TIPI = [
        'trattore' => 'Trattore',
        'motrice' => 'Motrice',
        'semirimorchio' => 'Semirimorchio',
        'veicolo' => 'Veicolo',
        'altro' => 'Altro',
    ];

    public const SOTTOTIPI = [
        'ribaltabile' => 'Ribaltabile',
        'centina' => 'Centina',
        'cisterna' => 'Cisterna',
        'autovettura' => 'Autovettura',
        'furgone' => 'Furgone',
        'altro' => 'Altro',
    ];

    /** Nome da mostrare: targa, oppure marca e modello se manca la targa. */
    public function getEtichettaAttribute(): string
    {
        if ($this->targa) {
            return $this->targa;
        }

        $descrizione = trim($this->marca . ' ' . $this->modello);

        return $descrizione !== '' ? $descrizione : 'Mezzo senza targa';
    }

    /** Tipi di documento che si possono allegare a questo mezzo. */
    public function tipiDocumento(): array
    {
        $tipi = [];

        // Le macchine non stradali ("Altro") non hanno il libretto di circolazione
        if ($this->tipo !== 'altro') {
            $tipi[] = 'libretto';
        }

        $tipi[] = 'assicurazione';
        $tipi[] = 'bollo';

        if (in_array($this->tipo, ['trattore', 'motrice'], true)) {
            $tipi[] = 'revisione_tachigrafo';
        }

        if ($this->adr) {
            $tipi[] = 'revisione_adr';
        }

        $tipi[] = 'sinistro';
        $tipi[] = 'altro';

        return Documento::etichette($tipi);
    }
}
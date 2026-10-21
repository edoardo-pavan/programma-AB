<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Documento extends Model
{
    protected $table = 'documenti';

    protected $guarded = [];

    protected $casts = [
        'data_emissione' => 'date',
        'data_scadenza' => 'date',
    ];

    /** Entro quanti giorni dalla scadenza compare l'avviso arancione. */
    public const GIORNI_AVVISO = 30;

    /**
     * Regole di ogni tipo di documento (il modulo si adatta a queste):
     *  scadenza  : 'si' (obbligatoria), 'facoltativa', 'no' (il campo non compare)
     *  file      : 'obbligatorio', 'facoltativo', 'no'
     *  titolo    : 'obbligatorio', 'facoltativo', null (il campo non compare)
     *  data      : etichetta del campo data, oppure null se non serve
     *  numero    : etichetta del campo numero, oppure null se non serve
     */
    public const TIPI = [
        // Mezzi
        'libretto' => [
            'etichetta' => 'Libretto di circolazione',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza revisione',
            'data' => 'Data ultima revisione', 'numero' => null, 'titolo' => null,
            'file' => 'obbligatorio', 'immatricolazione' => true,
            'aiuto' => 'Il libretto non scade: scade la revisione. Dopo ogni revisione carica qui il libretto aggiornato, la versione precedente passa nello storico.',
        ],
        'assicurazione' => [
            'etichetta' => 'Assicurazione',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza polizza',
            'data' => 'Data inizio polizza', 'numero' => 'Numero polizza', 'titolo' => null,
            'file' => 'facoltativo', 'immatricolazione' => false,
            'aiuto' => 'Per rinnovare carica la nuova polizza: la precedente passa nello storico.',
        ],
        'bollo' => [
            'etichetta' => 'Bollo',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza bollo',
            'data' => null, 'numero' => null, 'titolo' => null,
            'file' => 'no', 'immatricolazione' => false,
            'aiuto' => 'Solo promemoria di scadenza: non serve nessun file.',
        ],
        'revisione_tachigrafo' => [
            'etichetta' => 'Revisione tachigrafo',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza revisione tachigrafo',
            'data' => 'Data ultima revisione', 'numero' => null, 'titolo' => null,
            'file' => 'facoltativo', 'immatricolazione' => false, 'aiuto' => null,
        ],
        'revisione_adr' => [
            'etichetta' => 'Revisione ADR',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza revisione ADR',
            'data' => 'Data ultima revisione', 'numero' => null, 'titolo' => null,
            'file' => 'facoltativo', 'immatricolazione' => false, 'aiuto' => null,
        ],
        'sinistro' => [
            'etichetta' => 'Sinistro',
            'scadenza' => 'no', 'etichetta_scadenza' => null,
            'data' => 'Data del sinistro', 'numero' => null, 'titolo' => 'facoltativo',
            'file' => 'facoltativo', 'immatricolazione' => false,
            'aiuto' => 'Nel titolo puoi scrivere una breve descrizione. Nessuna scadenza.',
        ],

        // Dipendenti
        'patente' => [
            'etichetta' => 'Patente',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza patente',
            'data' => 'Data rilascio', 'numero' => 'Numero patente', 'titolo' => null,
            'file' => 'facoltativo', 'immatricolazione' => false, 'aiuto' => null,
        ],
        'cqc' => [
            'etichetta' => 'CQC',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza CQC',
            'data' => 'Data rilascio', 'numero' => null, 'titolo' => null,
            'file' => 'facoltativo', 'immatricolazione' => false, 'aiuto' => null,
        ],
        'patentino_adr' => [
            'etichetta' => 'Certificato ADR (patentino)',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza certificato ADR',
            'data' => 'Data rilascio', 'numero' => 'Numero certificato', 'titolo' => null,
            'file' => 'facoltativo', 'immatricolazione' => false, 'aiuto' => null,
        ],
        'carta_tachigrafica' => [
            'etichetta' => 'Carta tachigrafica',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza carta',
            'data' => 'Data rilascio', 'numero' => 'Numero carta', 'titolo' => null,
            'file' => 'facoltativo', 'immatricolazione' => false, 'aiuto' => null,
        ],
        'visita_medica' => [
            'etichetta' => 'Visita medica',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza idoneità',
            'data' => 'Data visita', 'numero' => null, 'titolo' => null,
            'file' => 'facoltativo', 'immatricolazione' => false, 'aiuto' => null,
        ],
        'corso_sicurezza' => [
            'etichetta' => 'Corso sicurezza',
            'scadenza' => 'si', 'etichetta_scadenza' => 'Scadenza attestato',
            'data' => 'Data corso', 'numero' => null, 'titolo' => 'obbligatorio',
            'file' => 'facoltativo', 'immatricolazione' => false,
            'aiuto' => 'Nel titolo scrivi il corso (es. Antincendio): corsi diversi hanno scadenze separate.',
        ],

        // Comune
        'altro' => [
            'etichetta' => 'Altro',
            'scadenza' => 'facoltativa', 'etichetta_scadenza' => 'Scadenza',
            'data' => 'Data del documento', 'numero' => null, 'titolo' => 'obbligatorio',
            'file' => 'facoltativo', 'immatricolazione' => false, 'aiuto' => null,
        ],
    ];

    /** Restituisce [chiave => etichetta] per le chiavi indicate. */
    public static function etichette(array $chiavi): array
    {
        $risultato = [];

        foreach ($chiavi as $chiave) {
            $risultato[$chiave] = self::TIPI[$chiave]['etichetta'];
        }

        return $risultato;
    }

    public function documentabile(): MorphTo
    {
        return $this->morphTo();
    }

    public function getEtichettaTipoAttribute(): string
    {
        $base = self::TIPI[$this->tipo]['etichetta'] ?? $this->tipo;

        return $this->titolo ? $base . ': ' . $this->titolo : $base;
    }

    public function getEtichettaScadenzaAttribute(): string
    {
        return self::TIPI[$this->tipo]['etichetta_scadenza'] ?? 'Scadenza';
    }

    public function getEtichettaDataAttribute(): string
    {
        return self::TIPI[$this->tipo]['data'] ?? 'Data';
    }

    public function getGiorniMancantiAttribute(): ?int
    {
        if (! $this->data_scadenza) {
            return null;
        }

        return (int) round(today()->diffInDays($this->data_scadenza, false));
    }

    public function getStatoAttribute(): string
    {
        $giorni = $this->giorni_mancanti;

        if ($giorni === null) {
            return 'senza_scadenza';
        }

        if ($giorni < 0) {
            return 'scaduto';
        }

        return $giorni <= self::GIORNI_AVVISO ? 'in_scadenza' : 'valido';
    }

    public function getTestoScadenzaAttribute(): string
    {
        $g = $this->giorni_mancanti;

        return match (true) {
            $g === null => 'Nessuna scadenza',
            $g < -1 => 'Scaduto da ' . abs($g) . ' giorni',
            $g === -1 => 'Scaduto da 1 giorno',
            $g === 0 => 'Scade oggi',
            $g === 1 => 'Scade domani',
            default => 'Scade tra ' . $g . ' giorni',
        };
    }

    public function getClasseBadgeAttribute(): string
    {
        return match ($this->stato) {
            'scaduto' => 'text-bg-danger',
            'in_scadenza' => 'text-bg-warning',
            'valido' => 'text-bg-success',
            default => 'text-bg-secondary',
        };
    }
}
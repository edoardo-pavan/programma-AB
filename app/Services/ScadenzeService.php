<?php

namespace App\Services;

use App\Models\Documento;
use Illuminate\Support\Collection;

class ScadenzeService
{
    /**
     * Tutte le scadenze valide oggi, dalla più urgente alla più lontana.
     * Per ogni documento conta solo la versione con la scadenza più lontana
     * (le precedenti sono storico). Mezzi e dipendenti non attivi sono esclusi.
     */
    public static function correnti(): Collection
    {
        $documenti = Documento::with('documentabile')
            ->whereNotNull('data_scadenza')
            ->orderByDesc('data_scadenza')
            ->orderByDesc('id')
            ->get();

        $visti = [];
        $correnti = collect();

        foreach ($documenti as $documento) {
            $proprietario = $documento->documentabile;

            if (! $proprietario || ! $proprietario->attivo) {
                continue;
            }

            $chiave = $documento->documentabile_type . '|' . $documento->documentabile_id
                . '|' . $documento->tipo . '|' . mb_strtolower((string) $documento->titolo);

            if (isset($visti[$chiave])) {
                continue;
            }

            $visti[$chiave] = true;
            $correnti->push($documento);
        }

        return $correnti->sortBy(fn ($d) => $d->data_scadenza->timestamp)->values();
    }
}

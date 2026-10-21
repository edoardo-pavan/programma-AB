<?php

namespace App\Models\Concerns;

use App\Models\Documento;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HaDocumenti
{
    public function documenti(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentabile');
    }

    /**
     * Divide i documenti in "correnti" (quelli validi oggi, i più urgenti
     * per primi) e "storico" (versioni sostituite da una più recente).
     */
    public function documentiSuddivisi(): array
    {
        $tutti = $this->documenti()
            ->orderByDesc('data_scadenza')
            ->orderByDesc('id')
            ->get();

        $visti = [];
        $correnti = collect();
        $storico = collect();

        foreach ($tutti as $documento) {
            if ($documento->data_scadenza === null) {
                $correnti->push($documento);
                continue;
            }

            $chiave = $documento->tipo . '|' . mb_strtolower((string) $documento->titolo);

            if (isset($visti[$chiave])) {
                $storico->push($documento);
            } else {
                $visti[$chiave] = true;
                $correnti->push($documento);
            }
        }

        $correnti = $correnti
            ->sortBy(fn ($d) => $d->data_scadenza?->timestamp ?? PHP_INT_MAX)
            ->values();

        return ['correnti' => $correnti, 'storico' => $storico];
    }
}
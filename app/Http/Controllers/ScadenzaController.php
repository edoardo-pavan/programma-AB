<?php

namespace App\Http\Controllers;

use App\Models\Dipendente;
use App\Models\Documento;
use App\Models\Mezzo;
use App\Services\ScadenzeService;
use Illuminate\Http\Request;

class ScadenzaController extends Controller
{
    public function index(Request $request)
    {
        $tutte = ScadenzeService::correnti();

        $conteggi = [
            'scaduti' => $tutte->where('stato', 'scaduto')->count(),
            'in_scadenza' => $tutte->where('stato', 'in_scadenza')->count(),
            'validi' => $tutte->where('stato', 'valido')->count(),
        ];

        $tipiPresenti = $tutte->pluck('tipo')->unique()->sort()
            ->mapWithKeys(fn ($t) => [$t => Documento::TIPI[$t]['etichetta'] ?? $t]);

        $stato = $request->input('stato', 'da_gestire');
        $entita = $request->input('entita');
        $tipo = $request->input('tipo');

        $scadenze = $tutte->filter(function ($d) use ($stato, $entita, $tipo) {
            $okStato = match ($stato) {
                'tutti' => true,
                'scaduti' => $d->stato === 'scaduto',
                'in_scadenza' => $d->stato === 'in_scadenza',
                'validi' => $d->stato === 'valido',
                default => in_array($d->stato, ['scaduto', 'in_scadenza'], true),
            };

            $okEntita = match ($entita) {
                'mezzi' => $d->documentabile instanceof Mezzo,
                'dipendenti' => $d->documentabile instanceof Dipendente,
                default => true,
            };

            $okTipo = ! $tipo || $d->tipo === $tipo;

            return $okStato && $okEntita && $okTipo;
        })->values();

        return view('scadenze.index', compact('scadenze', 'conteggi', 'tipiPresenti', 'stato'));
    }
}

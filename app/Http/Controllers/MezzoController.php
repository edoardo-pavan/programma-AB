<?php

namespace App\Http\Controllers;

use App\Models\Mezzo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MezzoController extends Controller
{
    public function index(Request $request)
    {
        $q = Mezzo::query();

        if ($cerca = $request->input('cerca')) {
            $q->where(function ($w) use ($cerca) {
                $w->where('targa', 'like', "%{$cerca}%")
                  ->orWhere('marca', 'like', "%{$cerca}%")
                  ->orWhere('modello', 'like', "%{$cerca}%");
            });
        }

        if ($tipo = $request->input('tipo')) {
            $q->where('tipo', $tipo);
        }

        if ($request->input('stato') === 'attivi') {
            $q->where('attivo', true);
        } elseif ($request->input('stato') === 'non_attivi') {
            $q->where('attivo', false);
        }

        $mezzi = $q->orderBy('tipo')->orderBy('targa')->paginate(20)->withQueryString();

        return view('mezzi.index', compact('mezzi'));
    }

    public function create()
    {
        return view('mezzi.create', [
            'mezzo' => new Mezzo(['attivo' => true]),
        ]);
    }

    public function store(Request $request)
    {
        Mezzo::create($this->validati($request));

        return redirect()->route('mezzi.index')->with('ok', 'Mezzo creato.');
    }

    public function edit(Mezzo $mezzo)
    {
        return view('mezzi.edit', compact('mezzo'));
    }

    public function update(Request $request, Mezzo $mezzo)
    {
        $mezzo->update($this->validati($request, $mezzo));

        return redirect()->route('mezzi.index')->with('ok', 'Mezzo aggiornato.');
    }

    private function validati(Request $request, ?Mezzo $mezzo = null): array
    {
        // Targa: maiuscole, senza spazi, null se vuota
        $targa = strtoupper(str_replace(' ', '', (string) $request->input('targa')));
        $request->merge(['targa' => $targa !== '' ? $targa : null]);

        $dati = $request->validate([
            'tipo' => ['required', Rule::in(array_keys(Mezzo::TIPI))],
            'sottotipo' => ['nullable', Rule::in(array_keys(Mezzo::SOTTOTIPI))],
            'targa' => [
                // Obbligatoria per tutti i tipi tranne "Altro"
                Rule::requiredIf($request->input('tipo') !== 'altro'),
                'nullable',
                'string',
                'max:15',
                Rule::unique('mezzi', 'targa')->ignore($mezzo?->id),
            ],
            'marca' => ['nullable', 'string', 'max:100'],
            'modello' => ['nullable', 'string', 'max:100'],
            'anno' => ['nullable', 'integer', 'between:1980,' . (now()->year + 1)],
            'telaio' => ['nullable', 'string', 'max:30'],
            'portata_t' => ['nullable', 'numeric', 'between:0,99'],
            'note' => ['nullable', 'string'],
        ], [], [
            'portata_t' => 'portata',
        ]);

        $dati['adr'] = $request->boolean('adr');
        $dati['attivo'] = $request->boolean('attivo');

        return $dati;
    }
}
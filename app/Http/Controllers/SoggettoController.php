<?php

namespace App\Http\Controllers;

use App\Models\Soggetto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SoggettoController extends Controller
{
    public function index(Request $request)
    {
        $q = Soggetto::query();

        if ($cerca = $request->input('cerca')) {
            $q->where(function ($w) use ($cerca) {
                $w->where('ragione_sociale', 'like', "%{$cerca}%")
                  ->orWhere('partita_iva', 'like', "%{$cerca}%")
                  ->orWhere('comune', 'like', "%{$cerca}%");
            });
        }

        if ($request->input('tipo') === 'clienti') {
            $q->where('is_cliente', true);
        } elseif ($request->input('tipo') === 'fornitori') {
            $q->where('is_fornitore', true);
        }

        $soggetti = $q->orderBy('ragione_sociale')->paginate(20)->withQueryString();

        return view('soggetti.index', compact('soggetti'));
    }

    public function create()
    {
        return view('soggetti.create', [
            'soggetto' => new Soggetto(['attivo' => true, 'is_cliente' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $dati = $this->validati($request);

        if (! $dati['is_cliente'] && ! $dati['is_fornitore']) {
            return back()->withInput()->withErrors(['is_cliente' => 'Seleziona almeno cliente o fornitore.']);
        }

        Soggetto::create($dati);

        return redirect()->route('soggetti.index')->with('ok', 'Soggetto creato.');
    }

    public function edit(Soggetto $soggetto)
    {
            return view('soggetti.edit', compact('soggetto'));
        }

        public function update(Request $request, Soggetto $soggetto)
        {
        $dati = $this->validati($request, $soggetto);

        if (! $dati['is_cliente'] && ! $dati['is_fornitore']) {
            return back()->withInput()->withErrors([
                'is_cliente' => 'Seleziona almeno cliente o fornitore.',
            ]);
        }

        // Conserva i dati commerciali quando i campi non sono inviati.
        foreach (['codice_sdi', 'condizioni_pagamento', 'categoria_fornitore'] as $campo) {
            if (! $request->exists($campo)) {
                unset($dati[$campo]);
            }
        }

        $soggetto->update($dati);

        return redirect()->route('soggetti.index')
            ->with('ok', 'Soggetto aggiornato.');
    }

    private function validati(Request $request, ?Soggetto $soggetto = null): array
    {
        $dati = $request->validate([
            'ragione_sociale' => ['required', 'string', 'max:255'],
            'partita_iva' => ['nullable', 'string', 'max:20', Rule::unique('soggetti', 'partita_iva')->ignore($soggetto?->id)],
            'codice_fiscale' => ['nullable', 'string', 'max:20'],
            'codice_sdi' => ['nullable', 'string', 'max:10'],
            'pec' => ['nullable', 'email', 'max:255'],
            'indirizzo' => ['nullable', 'string', 'max:255'],
            'cap' => ['nullable', 'string', 'max:10'],
            'comune' => ['nullable', 'string', 'max:255'],
            'provincia' => ['nullable', 'string', 'size:2'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'condizioni_pagamento' => ['nullable', 'string', 'max:255'],
            'categoria_fornitore' => ['nullable', Rule::in(Soggetto::CATEGORIE_FORNITORE)],
            'note' => ['nullable', 'string'],
        ]);

        $dati['is_cliente'] = $request->boolean('is_cliente');
        $dati['is_fornitore'] = $request->boolean('is_fornitore');
        $dati['attivo'] = $request->boolean('attivo');

        return $dati;
    }
}
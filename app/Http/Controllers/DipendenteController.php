<?php

namespace App\Http\Controllers;

use App\Models\Dipendente;
use App\Models\Mezzo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DipendenteController extends Controller
{
    public function index(Request $request)
    {
        $q = Dipendente::with(['trattore', 'rimorchio']);

        if ($cerca = $request->input('cerca')) {
            $q->where(function ($w) use ($cerca) {
                $w->where('nome', 'like', "%{$cerca}%")
                  ->orWhere('cognome', 'like', "%{$cerca}%")
                  ->orWhere('codice_fiscale', 'like', "%{$cerca}%");
            });
        }

        if ($mansione = $request->input('mansione')) {
            $q->where('mansione', $mansione);
        }

        if ($request->input('stato') === 'attivi') {
            $q->where('attivo', true);
        } elseif ($request->input('stato') === 'non_attivi') {
            $q->where('attivo', false);
        }

        $dipendenti = $q->orderBy('cognome')->orderBy('nome')->paginate(20)->withQueryString();

        return view('dipendenti.index', compact('dipendenti'));
    }

    public function create()
    {
        $dipendente = new Dipendente(['attivo' => true, 'mansione' => 'autista']);

        return view('dipendenti.create', array_merge(
            ['dipendente' => $dipendente],
            $this->mezziPerForm($dipendente)
        ));
    }

    public function store(Request $request)
    {
        Dipendente::create($this->validati($request));

        return redirect()->route('dipendenti.index')->with('ok', 'Dipendente creato.');
    }

    public function edit(Dipendente $dipendente)
    {
        return view('dipendenti.edit', array_merge(
            ['dipendente' => $dipendente],
            $this->mezziPerForm($dipendente)
        ));
    }

    public function update(Request $request, Dipendente $dipendente)
    {
        $dipendente->update($this->validati($request, $dipendente));

        return redirect()->route('dipendenti.index')->with('ok', 'Dipendente aggiornato.');
    }

    /**
     * Mezzi selezionabili nel modulo: solo quelli attivi,
     * più quello già assegnato (anche se nel frattempo è stato disattivato).
     */
    private function mezziPerForm(Dipendente $dipendente): array
    {
        $elenco = function (array $tipi, ?int $corrente) {
            return Mezzo::whereIn('tipo', $tipi)
                ->where(fn ($w) => $w->where('attivo', true)->orWhere('id', $corrente))
                ->orderBy('targa')
                ->get();
        };

        return [
            'trattori' => $elenco(['trattore', 'motrice'], $dipendente->trattore_id),
            'rimorchi' => $elenco(['semirimorchio'], $dipendente->rimorchio_id),
        ];
    }

    private function validati(Request $request, ?Dipendente $dipendente = null): array
    {
        // Codice fiscale: maiuscolo, senza spazi, null se vuoto
        $cf = strtoupper(str_replace(' ', '', (string) $request->input('codice_fiscale')));
        $request->merge(['codice_fiscale' => $cf !== '' ? $cf : null]);

        $dati = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'cognome' => ['required', 'string', 'max:100'],
            'codice_fiscale' => ['nullable', 'string', 'size:16', Rule::unique('dipendenti', 'codice_fiscale')->ignore($dipendente?->id)],
            'data_nascita' => ['nullable', 'date', 'before:today'],
            'luogo_nascita' => ['nullable', 'string', 'max:255'],
            'indirizzo' => ['nullable', 'string', 'max:255'],
            'cap' => ['nullable', 'string', 'max:10'],
            'comune' => ['nullable', 'string', 'max:255'],
            'provincia' => ['nullable', 'string', 'size:2'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'mansione' => ['required', Rule::in(array_keys(Dipendente::MANSIONI))],
            'data_assunzione' => ['nullable', 'date'],
            'data_cessazione' => [
                'nullable', 'date',
                Rule::when($request->filled('data_assunzione'), 'after_or_equal:data_assunzione'),
            ],
            'trattore_id' => ['nullable', Rule::exists('mezzi', 'id')->whereIn('tipo', ['trattore', 'motrice'])],
            'rimorchio_id' => ['nullable', Rule::exists('mezzi', 'id')->where('tipo', 'semirimorchio')],
            'note' => ['nullable', 'string'],
        ], [], [
            'codice_fiscale' => 'codice fiscale',
            'data_nascita' => 'data di nascita',
            'luogo_nascita' => 'luogo di nascita',
            'data_assunzione' => 'data di assunzione',
            'data_cessazione' => 'data di cessazione',
            'trattore_id' => 'trattore',
            'rimorchio_id' => 'rimorchio',
        ]);

        $dati['attivo'] = $request->boolean('attivo');

        // Trattore e rimorchio abituali hanno senso solo per gli autisti
        if ($dati['mansione'] !== 'autista') {
            $dati['trattore_id'] = null;
            $dati['rimorchio_id'] = null;
        }

        return $dati;
    }
}
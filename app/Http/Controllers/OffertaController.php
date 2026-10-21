<?php

namespace App\Http\Controllers;

use App\Models\Offerta;
use App\Models\Soggetto;
use App\Models\Viaggio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OffertaController extends Controller
{
    public function index(Request $request)
    {
        $q = Offerta::with('soggetto');

        if ($cerca = $request->input('cerca')) {
            $q->where(function ($w) use ($cerca) {
                $w->where('origine', 'like', "%{$cerca}%")
                  ->orWhere('destinazione', 'like', "%{$cerca}%")
                  ->orWhere('merce', 'like', "%{$cerca}%");
            });
        }

        if ($stato = $request->input('stato')) {
            $q->where('stato', $stato);
        }

        if ($soggetto = $request->input('soggetto_id')) {
            $q->where('soggetto_id', $soggetto);
        }

        $offerte = $q->orderByDesc('created_at')->paginate(20)->withQueryString();

        $soggetti = Soggetto::where('is_cliente', true)->orderBy('ragione_sociale')->get();

        $stati = [
            'bozza' => 'Bozza',
            'inviata' => 'Inviata',
            'accettata' => 'Accettata',
            'rifiutata' => 'Rifiutata',
            'scaduta' => 'Scaduta',
        ];

        return view('offerte.index', compact('offerte', 'soggetti', 'stati', 'stato'));
    }

    public function create()
    {
        $clienti = Soggetto::where('is_cliente', true)->orderBy('ragione_sociale')->get();
        $tipiPrezzo = [
            'viaggio' => 'A viaggio',
            'tonnellata' => 'A tonnellata',
        ];

        return view('offerte.create', compact('clienti', 'tipiPrezzo'));
    }

    public function store(Request $request)
    {
        $dati = $request->validate([
            'soggetto_id' => ['required', 'exists:soggetti,id'],
            'origine' => ['required', 'string', 'max:255'],
            'destinazione' => ['required', 'string', 'max:255'],
            'merce' => ['nullable', 'string', 'max:255'],
            'tipo_mezzo' => ['nullable', 'string', 'max:100'],
            'peso_min' => ['nullable', 'numeric', 'min:0'],
            'peso_max' => ['nullable', 'numeric', 'min:0'],
            'peso_minimo_fatturabile' => ['nullable', 'numeric', 'min:0'],
            'prezzo_base' => ['required', 'numeric', 'min:0'],
            'tipo_prezzo' => ['required', Rule::in(['viaggio', 'tonnellata'])],
            'fermo_macchina_ore' => ['nullable', 'numeric', 'min:0'],
            'prezzo_fermo_macchina_ora' => ['nullable', 'numeric', 'min:0'],
            'supplementi' => ['nullable', 'numeric', 'min:0'],
            'data_validita' => ['required', 'date', 'after_or_equal:today'],
            'stato' => ['required', Rule::in(['bozza', 'inviata'])],
            'note' => ['nullable', 'string'],
        ]);

        $dati['peso_minimo_fatturabile'] = $dati['peso_minimo_fatturabile'] ?? 0;
        $dati['fermo_macchina_ore'] = $dati['fermo_macchina_ore'] ?? 0;
        $dati['prezzo_fermo_macchina_ora'] = $dati['prezzo_fermo_macchina_ora'] ?? 0;
        $dati['supplementi'] = $dati['supplementi'] ?? 0;
        $dati['totale'] = (new Offerta($dati))->calcolaTotale();

        $offerta = Offerta::create($dati);

        return redirect()->route('offerte.show', $offerta)
            ->with('ok', 'Offerta creata.');
    }

    public function show(Offerta $offerta)
    {
        $offerta->load('soggetto', 'viaggio');

        return view('offerte.show', compact('offerta'));
    }

    public function edit(Offerta $offerta)
    {
        $clienti = Soggetto::where('is_cliente', true)->orderBy('ragione_sociale')->get();
        $tipiPrezzo = [
            'viaggio' => 'A viaggio',
            'tonnellata' => 'A tonnellata',
        ];

        return view('offerte.edit', compact('offerta', 'clienti', 'tipiPrezzo'));
    }

    public function update(Request $request, Offerta $offerta)
    {
        $dati = $request->validate([
            'soggetto_id' => ['required', 'exists:soggetti,id'],
            'origine' => ['required', 'string', 'max:255'],
            'destinazione' => ['required', 'string', 'max:255'],
            'merce' => ['nullable', 'string', 'max:255'],
            'tipo_mezzo' => ['nullable', 'string', 'max:100'],
            'peso_min' => ['nullable', 'numeric', 'min:0'],
            'peso_max' => ['nullable', 'numeric', 'min:0'],
            'peso_minimo_fatturabile' => ['nullable', 'numeric', 'min:0'],
            'prezzo_base' => ['required', 'numeric', 'min:0'],
            'tipo_prezzo' => ['required', Rule::in(['viaggio', 'tonnellata'])],
            'fermo_macchina_ore' => ['nullable', 'numeric', 'min:0'],
            'prezzo_fermo_macchina_ora' => ['nullable', 'numeric', 'min:0'],
            'supplementi' => ['nullable', 'numeric', 'min:0'],
            'data_validita' => ['required', 'date', 'after_or_equal:today'],
            'stato' => ['required', Rule::in(['bozza', 'inviata', 'rifiutata'])],
            'note' => ['nullable', 'string'],
        ]);

        $dati['peso_minimo_fatturabile'] = $dati['peso_minimo_fatturabile'] ?? 0;
        $dati['fermo_macchina_ore'] = $dati['fermo_macchina_ore'] ?? 0;
        $dati['prezzo_fermo_macchina_ora'] = $dati['prezzo_fermo_macchina_ora'] ?? 0;
        $dati['supplementi'] = $dati['supplementi'] ?? 0;
        $dati['totale'] = (new Offerta($dati))->calcolaTotale();

        $offerta->update($dati);

        return redirect()->route('offerte.show', $offerta)
            ->with('ok', 'Offerta aggiornata.');
    }

    public function destroy(Offerta $offerta)
    {
        if ($offerta->stato === 'accettata') {
            return back()->withErrors(['stato' => 'Non è possibile eliminare un\'offerta già accettata.']);
        }

        $offerta->delete();

        return redirect()->route('offerte.index')->with('ok', 'Offerta eliminata.');
    }

    public function accetta(Offerta $offerta)
    {
        if (! $offerta->può_essere_accettata) {
            return back()->withErrors(['stato' => 'L\'offerta non può essere accettata.']);
        }

        $offerta->update(['stato' => 'accettata']);

        Viaggio::create([
            'offerta_id' => $offerta->id,
            'mezzo_id' => null,
            'dipendente_id' => null,
            'soggetto_id' => $offerta->soggetto_id,
            'data_viaggio' => null,
            'origine' => $offerta->origine,
            'destinazione' => $offerta->destinazione,
            'km_previsti' => null,
            'peso_trasportato' => null,
            'stato' => 'bozza',
            'tipo_prezzo' => $offerta->tipo_prezzo,
            'prezzo_concordato' => $offerta->totale,
            'note' => 'Generato da offerta #' . $offerta->id,
        ]);

        return redirect()->route('offerte.show', $offerta)
            ->with('ok', 'Offerta accettata. Creato viaggio bozza.');
    }

    public function rifiuta(Offerta $offerta)
    {
        if (! $offerta->può_essere_accettata) {
            return back()->withErrors(['stato' => 'L\'offerta non può essere rifiutata.']);
        }

        $offerta->update(['stato' => 'rifiutata']);

        return redirect()->route('offerte.show', $offerta)
            ->with('ok', 'Offerta rifiutata.');
    }
}

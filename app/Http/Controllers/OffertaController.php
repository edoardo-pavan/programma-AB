<?php

namespace App\Http\Controllers;

use App\Models\Luogo;
use App\Models\Offerta;
use App\Models\Soggetto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OffertaController extends Controller
{
    public function index(Request $request)
    {
        $stato = $request->input('stato', 'attive');
        $q = Offerta::with(['cliente', 'partenza', 'destinazione']);

        if ($stato === 'bozze') {
            $q->where('stato', 'bozza');
        } elseif ($stato === 'attive') {
            $q->where('stato', 'attiva');
        } elseif ($stato === 'archiviate') {
            $q->whereIn('stato', ['archiviata', 'scaduta']);
        } elseif ($stato === 'tutte') {
            // nessun filtro aggiuntivo
        }

        if ($mezzo = $request->input('mezzo')) {
            $q->where('tipo_mezzo', $mezzo);
        }

        if ($cerca = trim((string) $request->input('cerca'))) {
            $q->where(function ($w) use ($cerca) {
                if (ctype_digit($cerca)) {
                    $w->orWhere('numero', (int) $cerca);
                }

                $w->orWhereHas('cliente', fn ($c) => $c->where('ragione_sociale', 'like', "%{$cerca}%"))
                  ->orWhereHas('partenza', fn ($l) => $l->where('nome', 'like', "%{$cerca}%")->orWhere('comune', 'like', "%{$cerca}%"))
                  ->orWhereHas('destinazione', fn ($l) => $l->where('nome', 'like', "%{$cerca}%")->orWhere('comune', 'like', "%{$cerca}%"))
                  ->orWhere('codici_cer', 'like', "%{$cerca}%")
                  ->orWhere('descrizione_materiale', 'like', "%{$cerca}%");
            });
        }

        $offerte = $q->orderByRaw('numero IS NULL DESC')
            ->orderByDesc('numero')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('offerte.index', compact('offerte', 'stato'));
    }

    public function create()
    {
        $offerta = new Offerta([
            'data_offerta' => today(),
            'tipo_prezzo' => 'a_tonnellata',
            'minimo_fatturabile' => 30,
            'fermo_orario' => Offerta::FERMO_PREDEFINITO,
            'fermo_franchigia_ore' => Offerta::FRANCHIGIA_PREDEFINITA,
            'iva_esclusa' => true,
            'pagamento' => Offerta::PAGAMENTO_PREDEFINITO,
        ]);

        return view('offerte.create', $this->datiForm($offerta));
    }

    public function store(Request $request)
    {
        $offerta = new Offerta($this->validati($request));
        $offerta->stato = 'bozza';
        $offerta->creata_da = $request->user()->id;
        $offerta->save();

        return $this->dopoSalvataggio($request, $offerta);
    }

    public function show(Offerta $offerta)
    {
        $offerta->load(['cliente', 'partenza', 'destinazione', 'origine', 'derivate', 'autore']);

        return view('offerte.show', compact('offerta'));
    }

    public function edit(Offerta $offerta)
    {
        if (! $offerta->isBozza()) {
            return $this->nonModificabile($offerta);
        }

        return view('offerte.edit', $this->datiForm($offerta));
    }

    public function update(Request $request, Offerta $offerta)
    {
        if (! $offerta->isBozza()) {
            return $this->nonModificabile($offerta);
        }

        $offerta->update($this->validati($request));

        return $this->dopoSalvataggio($request, $offerta);
    }

    public function destroy(Offerta $offerta)
    {
        if (! $offerta->isBozza()) {
            return redirect()->route('offerte.show', $offerta)
                ->with('errore', 'Solo le bozze si possono eliminare. Un\'offerta pubblicata si può archiviare.');
        }

        $offerta->delete();

        return redirect()->route('offerte.index', ['stato' => 'bozze'])->with('ok', 'Bozza eliminata.');
    }

    public function pubblica(Offerta $offerta)
    {
        if (! $offerta->isBozza()) {
            return redirect()->route('offerte.show', $offerta)->with('errore', 'Questa offerta è già stata pubblicata.');
        }

        $offerta->pubblica();

        return redirect()->route('offerte.show', $offerta)
            ->with('ok', 'Offerta n. ' . $offerta->numero_formattato . ' pubblicata.');
    }

    public function copia(Request $request, Offerta $offerta)
    {
        $copia = $offerta->replicate([
            'numero', 'stato', 'pubblicata_il', 'creata_da', 'offerta_origine_id', 'note_copia',
        ]);
        $copia->stato = 'bozza';
        $copia->data_offerta = today();
        $copia->offerta_origine_id = $offerta->id;
        $copia->creata_da = $request->user()->id;
        $copia->save();

        return redirect()->route('offerte.edit', $copia)
            ->with('ok', 'Copia creata come bozza. Modifica quello che serve e pubblicala.');
    }

    public function archivia(Offerta $offerta)
    {
        if ($offerta->stato === 'attiva') {
            $offerta->update(['stato' => 'archiviata']);
        }

        return redirect()->route('offerte.show', $offerta)->with('ok', 'Offerta archiviata.');
    }

    public function riattiva(Offerta $offerta)
    {
        if (in_array($offerta->stato, ['archiviata', 'scaduta'], true)) {
            $offerta->update(['stato' => 'attiva']);
        }

        return redirect()->route('offerte.show', $offerta)->with('ok', 'Offerta riattivata.');
    }

    private function nonModificabile(Offerta $offerta)
    {
        return redirect()->route('offerte.show', $offerta)
            ->with('errore', 'Un\'offerta pubblicata non si può modificare: crea una copia e cambia quella.');
    }

    private function dopoSalvataggio(Request $request, Offerta $offerta)
    {
        if ($request->input('azione') === 'pubblica') {
            $offerta->pubblica();

            return redirect()->route('offerte.show', $offerta)
                ->with('ok', 'Offerta n. ' . $offerta->numero_formattato . ' pubblicata.');
        }

        return redirect()->route('offerte.edit', $offerta)->with('ok', 'Bozza salvata.');
    }

    private function datiForm(Offerta $offerta): array
    {
        $clienti = Soggetto::where('is_cliente', true)
            ->where(fn ($w) => $w->where('attivo', true)->orWhere('id', $offerta->cliente_id))
            ->orderBy('ragione_sociale')
            ->get();

        $luoghiUsati = array_filter([$offerta->partenza_luogo_id, $offerta->destinazione_luogo_id]);

        $luoghi = Luogo::where(fn ($w) => $w->where('attivo', true)->orWhereIn('id', $luoghiUsati))
            ->orderBy('nome')
            ->get();

        return compact('offerta', 'clienti', 'luoghi');
    }

    private function validati(Request $request): array
    {
        $dati = $request->validate([
            'data_offerta' => ['required', 'date'],
            'cliente_id' => ['required', Rule::exists('soggetti', 'id')->where('is_cliente', true)],
            'partenza_luogo_id' => ['required', 'exists:luoghi,id'],
            'destinazione_luogo_id' => ['required', 'exists:luoghi,id', 'different:partenza_luogo_id'],
            'tipo_mezzo' => ['required', Rule::in(array_keys(Offerta::TIPI_MEZZO))],
            'tipo_prezzo' => ['required', Rule::in(array_keys(Offerta::TIPI_PREZZO))],
            'prezzo' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'prezzo_padroncini' => ['nullable', 'numeric', 'gt:0', 'max:99999999'],
            'minimo_fatturabile' => [
                Rule::requiredIf($request->input('tipo_prezzo') === 'a_tonnellata'),
                'nullable', 'numeric', 'gt:0', 'max:999',
            ],
            'fermo_orario' => ['required', 'numeric', 'min:0', 'max:99999'],
            'fermo_franchigia_ore' => ['required', 'numeric', 'min:0', 'max:24'],
            'pagamento' => ['required', 'string', 'max:200'],
            'codici_cer' => ['nullable', 'string', 'max:500'],
            'descrizione_materiale' => ['nullable', 'string', 'max:500'],
            'tonnellaggio_stimato' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:2000'],
            'note_copia' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'data_offerta' => 'data dell\'offerta',
            'cliente_id' => 'cliente',
            'partenza_luogo_id' => 'partenza',
            'destinazione_luogo_id' => 'destinazione',
            'tipo_mezzo' => 'tipo di mezzo',
            'tipo_prezzo' => 'tipo di prezzo',
            'prezzo_padroncini' => 'prezzo padroncini',
            'minimo_fatturabile' => 'minimo fatturabile',
            'fermo_orario' => 'fermo macchina',
            'fermo_franchigia_ore' => 'ore di fermo gratuite',
            'codici_cer' => 'codici CER',
            'descrizione_materiale' => 'descrizione del materiale',
            'tonnellaggio_stimato' => 'tonnellaggio stimato',
            'note_copia' => 'note sulla copia',
        ]);

        $dati['iva_esclusa'] = $request->boolean('iva_esclusa');

        if ($dati['tipo_prezzo'] !== 'a_tonnellata') {
            $dati['minimo_fatturabile'] = null;
        }

        return $dati;
    }
}

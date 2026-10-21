@extends('layouts.gestionale')

@section('titolo', 'Scadenze')

@section('contenuto')
    <h1 class="h4 mb-3">Scadenze</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="bovi-card bovi-kpi">
                <div class="icona" style="background: #dc2626"><i class="bi bi-exclamation-octagon"></i></div>
                <div>
                    <div class="etichetta">Scadute</div>
                    <div class="valore">{{ $conteggi['scaduti'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bovi-card bovi-kpi">
                <div class="icona" style="background: #f97316"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="etichetta">In scadenza (entro {{ \App\Models\Documento::GIORNI_AVVISO }} giorni)</div>
                    <div class="valore">{{ $conteggi['in_scadenza'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bovi-card bovi-kpi">
                <div class="icona" style="background: #16a34a"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="etichetta">Valide</div>
                    <div class="valore">{{ $conteggi['validi'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <select name="stato" class="form-select" onchange="this.form.submit()">
                <option value="da_gestire" @selected($stato === 'da_gestire')>Da gestire (scadute e in scadenza)</option>
                <option value="scaduti" @selected($stato === 'scaduti')>Solo scadute</option>
                <option value="in_scadenza" @selected($stato === 'in_scadenza')>Solo in scadenza</option>
                <option value="validi" @selected($stato === 'validi')>Solo valide</option>
                <option value="tutti" @selected($stato === 'tutti')>Tutte</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="entita" class="form-select" onchange="this.form.submit()">
                <option value="">Mezzi e dipendenti</option>
                <option value="mezzi" @selected(request('entita') === 'mezzi')>Solo mezzi</option>
                <option value="dipendenti" @selected(request('entita') === 'dipendenti')>Solo dipendenti</option>
            </select>
        </div>
        <div class="col-md-5">
            <select name="tipo" class="form-select" onchange="this.form.submit()">
                <option value="">Tutti i tipi di documento</option>
                @foreach ($tipiPresenti as $chiave => $etichetta)
                    <option value="{{ $chiave }}" @selected(request('tipo') === $chiave)>{{ $etichetta }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="bovi-card p-0">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Scadenza</th>
                    <th>Documento</th>
                    <th>Di chi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($scadenze as $d)
                    @php
                        $p = $d->documentabile;
                        $èMezzo = $p instanceof \App\Models\Mezzo;
                        $nome = $èMezzo ? $p->etichetta : $p->nome_completo;
                        $url = ($èMezzo ? route('mezzi.edit', $p) : route('dipendenti.edit', $p)) . '#documenti';
                    @endphp
                    <tr>
                        <td>
                            <div>{{ $d->data_scadenza->format('d/m/Y') }}</div>
                            <span class="badge {{ $d->classe_badge }}">{{ $d->testo_scadenza }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $d->etichetta_tipo }}</div>
                            <small class="text-muted">{{ $d->etichetta_scadenza }}</small>
                        </td>
                        <td>
                            <a href="{{ $url }}" class="text-decoration-none">
                                <i class="bi {{ $èMezzo ? 'bi-truck' : 'bi-person' }}"></i> {{ $nome }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            Nessuna scadenza da mostrare con questi filtri.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

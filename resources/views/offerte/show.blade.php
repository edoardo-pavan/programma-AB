@extends('layouts.gestionale')

@section('titolo', 'Offerta #' . $offerta->id)

@section('contenuto')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Offerta #{{ $offerta->id }}</h1>
        <a href="{{ route('offerte.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Torna indietro
        </a>
    </div>

    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <div class="bovi-card mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="bovi-kpi">
                    <div class="icona" style="background: #2563eb"><i class="bi bi-currency-euro"></i></div>
                    <div>
                        <div class="etichetta">Totale</div>
                        <div class="valore">€ {{ number_format($offerta->totale, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bovi-kpi">
                    <div class="icona" style="background: #16a34a"><i class="bi bi-truck"></i></div>
                    <div>
                        <div class="etichetta">Tipo prezzo</div>
                        <div class="valore">{{ $offerta->tipo_prezzo === 'viaggio' ? 'A viaggio' : 'A tonnellata' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bovi-kpi">
                    <div class="icona" style="background: #f97316"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="etichetta">Fermo macchina</div>
                        <div class="valore">{{ $offerta->fermo_macchina_ore }} ore</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="bovi-card">
                <h2 class="h6 mb-3">Dettagli offerta</h2>
                <table class="table table-borderless align-middle">
                    <tr>
                        <th class="w-25">Cliente</th>
                        <td>{{ $offerta->soggetto->ragione_sociale }}</td>
                    </tr>
                    <tr>
                        <th>Tratta</th>
                        <td>{{ $offerta->origine }} → {{ $offerta->destinazione }}</td>
                    </tr>
                    <tr>
                        <th>Merce</th>
                        <td>{{ $offerta->merce ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tipo mezzo</th>
                        <td>{{ $offerta->tipo_mezzo ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Peso min</th>
                        <td>{{ $offerta->peso_min ?? '-' }} t</td>
                    </tr>
                    <tr>
                        <th>Peso max</th>
                        <td>{{ $offerta->peso_max ?? '-' }} t</td>
                    </tr>
                    <tr>
                        <th>Peso minimo fatturabile</th>
                        <td>{{ $offerta->peso_minimo_fatturabile }} t</td>
                    </tr>
                    <tr>
                        <th>Prezzo base</th>
                        <td>€ {{ number_format($offerta->prezzo_base, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Fermo macchina</th>
                        <td>{{ $offerta->fermo_macchina_ore }} ore × € {{ number_format($offerta->prezzo_fermo_macchina_ora, 2, ',', '.') }}/ora</td>
                    </tr>
                    <tr>
                        <th>Supplementi</th>
                        <td>€ {{ number_format($offerta->supplementi, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Data validità</th>
                        <td>{{ $offerta->data_validita->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Stato</th>
                        <td>
                            <span class="badge text-bg-{{ $offerta->stato === 'accettata' ? 'success' : ($offerta->stato === 'rifiutata' || $offerta->stato === 'scaduta' ? 'danger' : ($offerta->stato === 'inviata' ? 'info' : 'secondary')) }}">
                                {{ ucfirst($offerta->stato) }}
                            </span>
                        </td>
                    </tr>
                    @if ($offerta->note)
                        <tr>
                            <th>Note</th>
                            <td>{{ $offerta->note }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="bovi-card">
                <h2 class="h6 mb-3">Azioni</h2>

                @if ($offerta->può_essere_accettata)
                    <form method="POST" action="{{ route('offerte.accetta', $offerta) }}"
                          onsubmit="return confirm('Accettare questa offerta? Verrà creato un viaggio bozza.');">
                        @csrf
                        <button class="btn btn-success w-100 mb-2" type="submit">
                            <i class="bi bi-check2-circle"></i> Accetta
                        </button>
                    </form>

                    <form method="POST" action="{{ route('offerte.rifiuta', $offerta) }}"
                          onsubmit="return confirm('Rifiutare questa offerta?');">
                        @csrf
                        <button class="btn btn-danger w-100 mb-2" type="submit">
                            <i class="bi bi-x-circle"></i> Rifiuta
                        </button>
                    </form>
                @endif

                @if ($offerta->viaggio)
                    <a href="{{ route('viaggi.edit', $offerta->viaggio) }}" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-truck"></i> Vai al viaggio
                    </a>
                @endif

                <a href="{{ route('offerte.edit', $offerta) }}" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-pencil"></i> Modifica
                </a>

                @if ($offerta->stato !== 'accettata' && $offerta->stato !== 'rifiutata')
                    <form method="POST" action="{{ route('offerte.destroy', $offerta) }}"
                          onsubmit="return confirm('Eliminare questa offerta? Non si può annullare.');" class="d-inline w-100">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger w-100" type="submit">
                            <i class="bi bi-trash"></i> Elimina
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@extends('layouts.gestionale')

@section('titolo', 'Offerta ' . $offerta->numero_formattato)

@section('contenuto')
    @php
        $luogo = fn ($l) => $l->nome . ($l->comune ? ' (' . $l->comune . ')' : '');
    @endphp

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">
                @if ($offerta->numero) Offerta n. {{ $offerta->numero_formattato }} @else Bozza di offerta @endif
                <span class="badge {{ $offerta->classe_stato }}">{{ $offerta->etichetta_stato }}</span>
            </h1>
            <div class="text-muted">
                del {{ $offerta->data_offerta->format('d/m/Y') }} · creata da {{ $offerta->autore->name }}
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            @if ($offerta->isBozza())
                <a href="{{ route('offerte.edit', $offerta) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Modifica
                </a>
                <form method="POST" action="{{ route('offerte.pubblica', $offerta) }}"
                      onsubmit="return confirm('Una volta pubblicata l\'offerta non si potrà più modificare. Continuare?');">
                    @csrf
                    <button class="btn btn-primary"><i class="bi bi-send-check"></i> Pubblica</button>
                </form>
                <form method="POST" action="{{ route('offerte.destroy', $offerta) }}"
                      onsubmit="return confirm('Eliminare questa bozza? Non si può annullare.');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Elimina</button>
                </form>
            @else
                <form method="POST" action="{{ route('offerte.copia', $offerta) }}">
                    @csrf
                    <button class="btn btn-outline-primary"><i class="bi bi-files"></i> Crea copia</button>
                </form>
                @if ($offerta->stato === 'attiva')
                    <form method="POST" action="{{ route('offerte.archivia', $offerta) }}"
                          onsubmit="return confirm('Archiviare l\'offerta? Non verrà più proposta per i nuovi viaggi.');">
                        @csrf
                        <button class="btn btn-outline-secondary"><i class="bi bi-archive"></i> Archivia</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('offerte.riattiva', $offerta) }}">
                        @csrf
                        <button class="btn btn-outline-success"><i class="bi bi-arrow-counterclockwise"></i> Riattiva</button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif
    @if (session('errore'))
        <div class="alert alert-danger">{{ session('errore') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="bovi-card h-100">
                <h2 class="h6 mb-3">Cliente e tratta</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted fw-normal">Cliente</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $offerta->cliente->ragione_sociale }}</dd>
                    <dt class="col-sm-4 text-muted fw-normal">Partenza</dt>
                    <dd class="col-sm-8">
                        {{ $luogo($offerta->partenza) }}
                        @if ($offerta->partenza->indirizzo) <div class="small text-muted">{{ $offerta->partenza->indirizzo }}</div> @endif
                    </dd>
                    <dt class="col-sm-4 text-muted fw-normal">Destinazione</dt>
                    <dd class="col-sm-8">{{ $luogo($offerta->destinazione) }}</dd>
                    <dt class="col-sm-4 text-muted fw-normal">Mezzo</dt>
                    <dd class="col-sm-8">{{ \App\Models\Offerta::TIPI_MEZZO[$offerta->tipo_mezzo] ?? $offerta->tipo_mezzo }}</dd>
                </dl>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="bovi-card h-100">
                <h2 class="h6 mb-3">Prezzi e condizioni</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted fw-normal">Prezzo</dt>
                    <dd class="col-sm-7 fw-semibold fs-5">{{ $offerta->prezzo_formattato }}</dd>
                    @if ($offerta->prezzo_padroncini)
                        <dt class="col-sm-5 text-muted fw-normal">Prezzo padroncini</dt>
                        <dd class="col-sm-7">€ {{ number_format((float) $offerta->prezzo_padroncini, 2, ',', '.') }}</dd>
                    @endif
                    @if ($offerta->testo_minimo)
                        <dt class="col-sm-5 text-muted fw-normal">Minimo fatturabile</dt>
                        <dd class="col-sm-7">{{ $offerta->testo_minimo }}</dd>
                    @endif
                    <dt class="col-sm-5 text-muted fw-normal">Fermo macchina</dt>
                    <dd class="col-sm-7">{{ $offerta->testo_fermo }}</dd>
                    <dt class="col-sm-5 text-muted fw-normal">IVA</dt>
                    <dd class="col-sm-7">{{ $offerta->iva_esclusa ? 'Esclusa' : 'Inclusa' }}</dd>
                    <dt class="col-sm-5 text-muted fw-normal">Pagamento</dt>
                    <dd class="col-sm-7">{{ $offerta->pagamento }}</dd>
                </dl>
            </div>
        </div>

        @if ($offerta->codici_cer || $offerta->descrizione_materiale || $offerta->tonnellaggio_stimato)
            <div class="col-12">
                <div class="bovi-card">
                    <h2 class="h6 mb-3">Materiale</h2>
                    <dl class="row mb-0">
                        @if ($offerta->codici_cer)
                            <dt class="col-sm-3 text-muted fw-normal">Codici CER</dt>
                            <dd class="col-sm-9">{{ $offerta->codici_cer }}</dd>
                        @endif
                        @if ($offerta->descrizione_materiale)
                            <dt class="col-sm-3 text-muted fw-normal">Descrizione</dt>
                            <dd class="col-sm-9">{{ $offerta->descrizione_materiale }}</dd>
                        @endif
                        @if ($offerta->tonnellaggio_stimato)
                            <dt class="col-sm-3 text-muted fw-normal">Tonnellaggio stimato</dt>
                            <dd class="col-sm-9">{{ $offerta->tonnellaggio_stimato }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        @endif

        @if ($offerta->note || $offerta->note_copia)
            <div class="col-12">
                <div class="bovi-card">
                    <h2 class="h6 mb-3">Note</h2>
                    @if ($offerta->note) <p class="mb-2">{{ $offerta->note }}</p> @endif
                    @if ($offerta->note_copia)
                        <p class="mb-0 text-muted"><strong>Sulla copia:</strong> {{ $offerta->note_copia }}</p>
                    @endif
                </div>
            </div>
        @endif

        @if ($offerta->origine || $offerta->derivate->isNotEmpty())
            <div class="col-12">
                <div class="bovi-card">
                    <h2 class="h6 mb-3">Collegamenti</h2>
                    @if ($offerta->origine)
                        <div>Deriva dall'offerta
                            <a href="{{ route('offerte.show', $offerta->origine) }}">n. {{ $offerta->origine->numero_formattato }}</a>
                        </div>
                    @endif
                    @if ($offerta->derivate->isNotEmpty())
                        <div>Copie create:
                            @foreach ($offerta->derivate as $d)
                                <a href="{{ route('offerte.show', $d) }}">{{ $d->numero ? 'n. ' . $d->numero_formattato : 'bozza' }}</a>@if (! $loop->last), @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

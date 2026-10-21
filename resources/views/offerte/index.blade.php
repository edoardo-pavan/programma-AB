@extends('layouts.gestionale')

@section('titolo', 'Offerte')

@section('contenuto')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Offerte</h1>
        <a href="{{ route('offerte.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuova
        </a>
    </div>

    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="search" name="cerca" value="{{ request('cerca') }}"
                   class="form-control" placeholder="Cerca per origine, destinazione o merce">
        </div>
        <div class="col-md-3">
            <select name="stato" class="form-select" onchange="this.form.submit()">
                <option value="">Tutti gli stati</option>
                @foreach ($stati as $chiave => $etichetta)
                    <option value="{{ $chiave }}" @selected($stato === $chiave)>{{ $etichetta }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="soggetto_id" class="form-select" onchange="this.form.submit()">
                <option value="">Tutti i clienti</option>
                @foreach ($soggetti as $s)
                    <option value="{{ $s->id }}" @selected(request('soggetto_id') == $s->id)>{{ $s->ragione_sociale }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <button class="btn btn-outline-secondary w-100">Cerca</button>
        </div>
    </form>

    <div class="bovi-card p-0">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Tratta</th>
                    <th>Merce</th>
                    <th>Tipo</th>
                    <th>Prezzo</th>
                    <th>Scadenza</th>
                    <th>Stato</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($offerte as $o)
                    <tr>
                        <td class="fw-semibold">{{ $o->soggetto->ragione_sociale }}</td>
                        <td>{{ $o->origine }} → {{ $o->destinazione }}</td>
                        <td>{{ $o->merce ?? '-' }}</td>
                        <td>{{ $o->tipo_prezzo === 'viaggio' ? 'Viaggio' : 'Tonnellata' }}</td>
                        <td>€ {{ number_format($o->totale, 2, ',', '.') }}</td>
                        <td>{{ $o->data_validita->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge text-bg-{{ $o->stato === 'accettata' ? 'success' : ($o->stato === 'rifiutata' || $o->stato === 'scaduta' ? 'danger' : ($o->stato === 'inviata' ? 'info' : 'secondary')) }}">
                                {{ ucfirst($o->stato) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('offerte.show', $o) }}" class="btn btn-sm btn-outline-primary">Vedi</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Nessuna offerta.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $offerte->links() }}</div>
@endsection

@extends('layouts.gestionale')

@section('titolo', 'Offerte')

@section('contenuto')
    @php
        $luogo = fn ($l) => $l->nome . ($l->comune ? ' (' . $l->comune . ')' : '');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Offerte</h1>
        <a href="{{ route('offerte.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuova offerta
        </a>
    </div>

    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif
    @if (session('errore'))
        <div class="alert alert-danger">{{ session('errore') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="search" name="cerca" value="{{ request('cerca') }}" class="form-control"
                   placeholder="Cerca per numero, cliente, luogo, CER o materiale">
        </div>
        <div class="col-md-3">
            <select name="stato" class="form-select" onchange="this.form.submit()">
                <option value="attive" @selected($stato === 'attive')>Attive</option>
                <option value="bozze" @selected($stato === 'bozze')>Bozze</option>
                <option value="archiviate" @selected($stato === 'archiviate')>Archiviate</option>
                <option value="tutte" @selected($stato === 'tutte')>Tutte</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="mezzo" class="form-select" onchange="this.form.submit()">
                <option value="">Tutti i mezzi</option>
                @foreach (\App\Models\Offerta::TIPI_MEZZO as $valore => $etichetta)
                    <option value="{{ $valore }}" @selected(request('mezzo') === $valore)>{{ $etichetta }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100">Cerca</button>
        </div>
    </form>

    <div class="bovi-card p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>N.</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Tratta</th>
                        <th>Mezzo</th>
                        <th>Prezzo</th>
                        <th>Stato</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($offerte as $o)
                        <tr>
                            <td class="fw-semibold">{{ $o->numero_formattato }}</td>
                            <td>{{ $o->data_offerta->format('d/m/Y') }}</td>
                            <td>{{ $o->cliente->ragione_sociale }}</td>
                            <td>
                                {{ $luogo($o->partenza) }}
                                <i class="bi bi-arrow-right text-muted"></i>
                                {{ $luogo($o->destinazione) }}
                            </td>
                            <td>{{ \App\Models\Offerta::TIPI_MEZZO[$o->tipo_mezzo] ?? $o->tipo_mezzo }}</td>
                            <td class="text-nowrap">{{ $o->prezzo_formattato }}</td>
                            <td><span class="badge {{ $o->classe_stato }}">{{ $o->etichetta_stato }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('offerte.show', $o) }}" class="btn btn-sm btn-outline-primary">Apri</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Nessuna offerta trovata.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $offerte->links() }}</div>
@endsection

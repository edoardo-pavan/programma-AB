@extends('layouts.gestionale')

@section('titolo', 'Mezzi')

@section('contenuto')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Mezzi</h1>
        <a href="{{ route('mezzi.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuovo
        </a>
    </div>

    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="search" name="cerca" value="{{ request('cerca') }}"
                   class="form-control" placeholder="Cerca per targa, marca o modello">
        </div>
        <div class="col-md-3">
            <select name="tipo" class="form-select" onchange="this.form.submit()">
                <option value="">Tutti i tipi</option>
                @foreach (\App\Models\Mezzo::TIPI as $valore => $etichetta)
                    <option value="{{ $valore }}" @selected(request('tipo') === $valore)>{{ $etichetta }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="stato" class="form-select" onchange="this.form.submit()">
                <option value="">Tutti</option>
                <option value="attivi" @selected(request('stato') === 'attivi')>Attivi</option>
                <option value="non_attivi" @selected(request('stato') === 'non_attivi')>Non attivi</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100">Cerca</button>
        </div>
    </form>

    <div class="bovi-card p-0">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Targa</th>
                    <th>Tipo</th>
                    <th>Marca e modello</th>
                    <th>Portata</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mezzi as $m)
                    <tr class="{{ $m->attivo ? '' : 'text-muted' }}">
                        <td class="fw-semibold">{{ $m->etichetta }}
                            @unless ($m->attivo) <span class="badge text-bg-secondary">non attivo</span> @endunless
                        </td>
                        <td>
                            {{ \App\Models\Mezzo::TIPI[$m->tipo] ?? $m->tipo }}
                            @if ($m->sottotipo)
                                <small class="text-muted">· {{ \App\Models\Mezzo::SOTTOTIPI[$m->sottotipo] ?? $m->sottotipo }}</small>
                            @endif
                            @if ($m->adr) <span class="badge text-bg-warning">ADR</span> @endif
                        </td>
                        <td>{{ trim($m->marca.' '.$m->modello) }}</td>
                        <td>{{ $m->portata_t !== null ? rtrim(rtrim(number_format($m->portata_t, 2, ',', '.'), '0'), ',').' t' : '' }}</td>
                        <td class="text-end">
                            <a href="{{ route('mezzi.edit', $m) }}" class="btn btn-sm btn-outline-primary">Modifica</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Nessun risultato.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $mezzi->links() }}</div>
@endsection
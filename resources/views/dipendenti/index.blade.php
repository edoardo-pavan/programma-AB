@extends('layouts.gestionale')

@section('titolo', 'Dipendenti')

@section('contenuto')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Dipendenti</h1>
        <a href="{{ route('dipendenti.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuovo
        </a>
    </div>

    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="search" name="cerca" value="{{ request('cerca') }}"
                   class="form-control" placeholder="Cerca per nome, cognome o codice fiscale">
        </div>
        <div class="col-md-3">
            <select name="mansione" class="form-select" onchange="this.form.submit()">
                <option value="">Tutte le mansioni</option>
                @foreach (\App\Models\Dipendente::MANSIONI as $valore => $etichetta)
                    <option value="{{ $valore }}" @selected(request('mansione') === $valore)>{{ $etichetta }}</option>
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
                    <th>Nome</th>
                    <th>Mansione</th>
                    <th>Telefono</th>
                    <th>Mezzi abituali</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dipendenti as $d)
                    <tr class="{{ $d->attivo ? '' : 'text-muted' }}">
                        <td class="fw-semibold">{{ $d->nome_completo }}
                            @unless ($d->attivo) <span class="badge text-bg-secondary">non attivo</span> @endunless
                        </td>
                        <td>{{ \App\Models\Dipendente::MANSIONI[$d->mansione] ?? $d->mansione }}</td>
                        <td>{{ $d->telefono }}</td>
                        <td>
                            @if ($d->trattore) {{ $d->trattore->targa }} @endif
                            @if ($d->trattore && $d->rimorchio) <span class="text-muted">/</span> @endif
                            @if ($d->rimorchio) {{ $d->rimorchio->targa }} @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('dipendenti.edit', $d) }}" class="btn btn-sm btn-outline-primary">Modifica</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Nessun risultato.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $dipendenti->links() }}</div>
@endsection
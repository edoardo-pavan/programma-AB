@extends('layouts.gestionale')

@section('titolo', 'Clienti e fornitori')

@section('contenuto')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Clienti e fornitori</h1>
        <a href="{{ route('soggetti.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuovo
        </a>
    </div>

    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="search" name="cerca" value="{{ request('cerca') }}"
                   class="form-control" placeholder="Cerca per nome, P.IVA o comune">
        </div>
        <div class="col-md-3">
            <select name="tipo" class="form-select" onchange="this.form.submit()">
                <option value="">Tutti</option>
                <option value="clienti" @selected(request('tipo') === 'clienti')>Solo clienti</option>
                <option value="fornitori" @selected(request('tipo') === 'fornitori')>Solo fornitori</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-secondary w-100">Cerca</button>
        </div>
    </form>

    <div class="bovi-card p-0">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Ragione sociale</th>
                    <th>P.IVA</th>
                    <th>Comune</th>
                    <th>Tipo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($soggetti as $s)
                    <tr class="{{ $s->attivo ? '' : 'text-muted' }}">
                        <td>{{ $s->ragione_sociale }}
                            @unless ($s->attivo) <span class="badge text-bg-secondary">non attivo</span> @endunless
                        </td>
                        <td>{{ $s->partita_iva }}</td>
                        <td>{{ $s->comune }}</td>
                        <td>
                            @if ($s->is_cliente) <span class="badge text-bg-primary">Cliente</span> @endif
                            @if ($s->is_fornitore) <span class="badge text-bg-warning">Fornitore</span> @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('soggetti.edit', $s) }}" class="btn btn-sm btn-outline-primary">Modifica</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Nessun risultato.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $soggetti->links() }}</div>
@endsection
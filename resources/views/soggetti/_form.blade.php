@php
    $campo = fn ($nome) => old($nome, $soggetto->$nome);
@endphp

@if ($errors->any())
    <div class="alert alert-danger">Controlla i campi evidenziati.</div>
@endif

<div class="bovi-card mb-3">
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Ragione sociale *</label>
            <input name="ragione_sociale" value="{{ $campo('ragione_sociale') }}"
                   class="form-control @error('ragione_sociale') is-invalid @enderror">
            @error('ragione_sociale') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label d-block">Tipo *</label>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input @error('is_cliente') is-invalid @enderror"
                       name="is_cliente" value="1" id="is_cliente" @checked(old('is_cliente', $soggetto->is_cliente))>
                <label class="form-check-label" for="is_cliente">Cliente</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="is_fornitore" value="1"
                       id="is_fornitore" @checked(old('is_fornitore', $soggetto->is_fornitore))>
                <label class="form-check-label" for="is_fornitore">Fornitore</label>
            </div>
            @error('is_cliente') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Partita IVA</label>
            <input name="partita_iva" value="{{ $campo('partita_iva') }}"
                   class="form-control @error('partita_iva') is-invalid @enderror">
            @error('partita_iva') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Codice fiscale</label>
            <input name="codice_fiscale" value="{{ $campo('codice_fiscale') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Codice SDI</label>
            <input name="codice_sdi" value="{{ $campo('codice_sdi') }}" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Indirizzo</label>
            <input name="indirizzo" value="{{ $campo('indirizzo') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">CAP</label>
            <input name="cap" value="{{ $campo('cap') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Comune</label>
            <input name="comune" value="{{ $campo('comune') }}" class="form-control">
        </div>
        <div class="col-md-1">
            <label class="form-label">Prov.</label>
            <input name="provincia" value="{{ $campo('provincia') }}" maxlength="2"
                   class="form-control @error('provincia') is-invalid @enderror">
        </div>

        <div class="col-md-4">
            <label class="form-label">Telefono</label>
            <input name="telefono" value="{{ $campo('telefono') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input name="email" value="{{ $campo('email') }}"
                   class="form-control @error('email') is-invalid @enderror">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">PEC</label>
            <input name="pec" value="{{ $campo('pec') }}"
                   class="form-control @error('pec') is-invalid @enderror">
            @error('pec') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Condizioni di pagamento</label>
            <input name="condizioni_pagamento" value="{{ $campo('condizioni_pagamento') }}"
                   class="form-control" placeholder="es. Bonifico 60 gg fine mese">
        </div>
        <div class="col-md-6">
            <label class="form-label">Categoria fornitore</label>
            <select name="categoria_fornitore" class="form-select">
                <option value="">-</option>
                @foreach (\App\Models\Soggetto::CATEGORIE_FORNITORE as $cat)
                    <option value="{{ $cat }}" @selected($campo('categoria_fornitore') === $cat)>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Note</label>
            <textarea name="note" rows="3" class="form-control">{{ $campo('note') }}</textarea>
        </div>

        <div class="col-12">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="attivo" value="1" id="attivo"
                       @checked(old('attivo', $soggetto->attivo))>
                <label class="form-check-label" for="attivo">Attivo</label>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button class="btn btn-primary" type="submit">Salva</button>
    <a href="{{ route('soggetti.index') }}" class="btn btn-outline-secondary">Annulla</a>
</div>
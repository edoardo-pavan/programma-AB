@php
    $campo = fn ($nome) => old($nome, $mezzo->$nome);
@endphp

@if ($errors->any())
    <div class="alert alert-danger">Controlla i campi evidenziati.</div>
@endif

<div class="bovi-card mb-3">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Targa *</label>
            <input name="targa" value="{{ $campo('targa') }}"
                   class="form-control text-uppercase @error('targa') is-invalid @enderror">
            @error('targa') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipo *</label>
            <select name="tipo" class="form-select @error('tipo') is-invalid @enderror">
                <option value="">-</option>
                @foreach (\App\Models\Mezzo::TIPI as $valore => $etichetta)
                    <option value="{{ $valore }}" @selected($campo('tipo') === $valore)>{{ $etichetta }}</option>
                @endforeach
            </select>
            @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Sottotipo</label>
            <select name="sottotipo" class="form-select">
                <option value="">-</option>
                @foreach (\App\Models\Mezzo::SOTTOTIPI as $valore => $etichetta)
                    <option value="{{ $valore }}" @selected($campo('sottotipo') === $valore)>{{ $etichetta }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Portata (t)</label>
            <input name="portata_t" value="{{ $campo('portata_t') }}"
                   class="form-control @error('portata_t') is-invalid @enderror" placeholder="es. 26.5">
            @error('portata_t') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Marca</label>
            <input name="marca" value="{{ $campo('marca') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Modello</label>
            <input name="modello" value="{{ $campo('modello') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Anno</label>
            <input name="anno" value="{{ $campo('anno') }}"
                   class="form-control @error('anno') is-invalid @enderror">
            @error('anno') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Numero di telaio</label>
            <input name="telaio" value="{{ $campo('telaio') }}" class="form-control">
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="adr" value="1" id="adr"
                       @checked(old('adr', $mezzo->adr))>
                <label class="form-check-label" for="adr">Mezzo abilitato ADR</label>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label">Note</label>
            <textarea name="note" rows="3" class="form-control">{{ $campo('note') }}</textarea>
        </div>

        <div class="col-12">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="attivo" value="1" id="attivo"
                       @checked(old('attivo', $mezzo->attivo))>
                <label class="form-check-label" for="attivo">Attivo</label>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button class="btn btn-primary" type="submit">Salva</button>
    <a href="{{ route('mezzi.index') }}" class="btn btn-outline-secondary">Annulla</a>
</div>
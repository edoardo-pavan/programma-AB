@extends('layouts.gestionale')

@section('titolo', 'Modifica Offerta #' . $offerta->id)

@section('contenuto')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Modifica Offerta #{{ $offerta->id }}</h1>
        <a href="{{ route('offerte.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Torna indietro
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">Controlla i campi evidenziati.</div>
    @endif

    <form method="POST" action="{{ route('offerte.update', $offerta) }}" class="bovi-card p-4">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Cliente *</label>
                <select name="soggetto_id" class="form-select @error('soggetto_id') is-invalid @enderror">
                    <option value="">- seleziona -</option>
                    @foreach ($clienti as $c)
                        <option value="{{ $c->id }}" @selected(old('soggetto_id', $offerta->soggetto_id) === $c->id)>{{ $c->ragione_sociale }}</option>
                    @endforeach
                </select>
                @error('soggetto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Origine *</label>
                <input name="origine" value="{{ old('origine', $offerta->origine) }}"
                       class="form-control @error('origine') is-invalid @enderror">
                @error('origine') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Destinazione *</label>
                <input name="destinazione" value="{{ old('destinazione', $offerta->destinazione) }}"
                       class="form-control @error('destinazione') is-invalid @enderror">
                @error('destinazione') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Merce</label>
                <input name="merce" value="{{ old('merce', $offerta->merce) }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Tipo mezzo</label>
                <input name="tipo_mezzo" value="{{ old('tipo_mezzo', $offerta->tipo_mezzo) }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Peso min (t)</label>
                <input name="peso_min" value="{{ old('peso_min', $offerta->peso_min) }}"
                       class="form-control @error('peso_min') is-invalid @enderror" placeholder="es. 10.00">
                @error('peso_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Peso max (t)</label>
                <input name="peso_max" value="{{ old('peso_max', $offerta->peso_max) }}"
                       class="form-control @error('peso_max') is-invalid @enderror" placeholder="es. 26.00">
                @error('peso_max') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Peso minimo fatturabile (t) *</label>
                <input name="peso_minimo_fatturabile" value="{{ old('peso_minimo_fatturabile', $offerta->peso_minimo_fatturabile) }}"
                       class="form-control @error('peso_minimo_fatturabile') is-invalid @enderror">
                @error('peso_minimo_fatturabile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Prezzo base *</label>
                <input name="prezzo_base" value="{{ old('prezzo_base', $offerta->prezzo_base) }}"
                       class="form-control @error('prezzo_base') is-invalid @enderror" placeholder="es. 150.00">
                @error('prezzo_base') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Tipo prezzo *</label>
                <select name="tipo_prezzo" class="form-select @error('tipo_prezzo') is-invalid @enderror">
                    @foreach ($tipiPrezzo as $chiave => $etichetta)
                        <option value="{{ $chiave }}" @selected(old('tipo_prezzo', $offerta->tipo_prezzo) === $chiave)>{{ $etichetta }}</option>
                    @endforeach
                </select>
                @error('tipo_prezzo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Fermo macchina (ore)</label>
                <input name="fermo_macchina_ore" value="{{ old('fermo_macchina_ore', $offerta->fermo_macchina_ore) }}"
                       class="form-control @error('fermo_macchina_ore') is-invalid @enderror" placeholder="es. 2.5">
                @error('fermo_macchina_ore') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Prezzo fermo macchina (€/ora)</label>
                <input name="prezzo_fermo_macchina_ora" value="{{ old('prezzo_fermo_macchina_ora', $offerta->prezzo_fermo_macchina_ora) }}"
                       class="form-control @error('prezzo_fermo_macchina_ora') is-invalid @enderror" placeholder="es. 40.00">
                @error('prezzo_fermo_macchina_ora') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Supplementi (€)</label>
                <input name="supplementi" value="{{ old('supplementi', $offerta->supplementi) }}"
                       class="form-control @error('supplementi') is-invalid @enderror" placeholder="es. 25.00">
                @error('supplementi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Data validità *</label>
                <input type="date" name="data_validita" value="{{ old('data_validita', $offerta->data_validita?->format('Y-m-d')) }}"
                       class="form-control @error('data_validita') is-invalid @enderror">
                @error('data_validita') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Stato *</label>
                <select name="stato" class="form-select @error('stato') is-invalid @enderror">
                    <option value="bozza" @selected(old('stato', $offerta->stato) === 'bozza')>Bozza</option>
                    <option value="inviata" @selected(old('stato', $offerta->stato) === 'inviata')>Inviata</option>
                    <option value="rifiutata" @selected(old('stato', $offerta->stato) === 'rifiutata')>Rifiutata</option>
                </select>
                @error('stato') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Note</label>
                <textarea name="note" rows="3" class="form-control">{{ old('note', $offerta->note) }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-primary" type="submit">Salva Modifiche</button>
            <a href="{{ route('offerte.show', $offerta) }}" class="btn btn-outline-secondary">Annulla</a>
            @if ($offerta->stato !== 'accettata' && $offerta->stato !== 'rifiutata')
                <form method="POST" action="{{ route('offerte.destroy', $offerta) }}"
                      onsubmit="return confirm('Eliminare questa offerta? Non si può annullare.');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit">Elimina</button>
                </form>
            @endif
        </div>
    </form>
@endsection

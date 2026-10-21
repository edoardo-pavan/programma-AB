@extends('layouts.gestionale')

@section('titolo', 'Nuova Offerta')

@section('contenuto')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Nuova Offerta</h1>
        <a href="{{ route('offerte.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Torna indietro
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">Controlla i campi evidenziati.</div>
    @endif

    <form method="POST" action="{{ route('offerte.store') }}" class="bovi-card p-4">
        @csrf

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Cliente *</label>
                <select name="soggetto_id" class="form-select @error('soggetto_id') is-invalid @enderror">
                    <option value="">- seleziona -</option>
                    @foreach ($clienti as $c)
                        <option value="{{ $c->id }}" @selected(old('soggetto_id') === $c->id)>{{ $c->ragione_sociale }}</option>
                    @endforeach
                </select>
                @error('soggetto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Origine *</label>
                <input name="origine" value="{{ old('origine') }}"
                       class="form-control @error('origine') is-invalid @enderror">
                @error('origine') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Destinazione *</label>
                <input name="destinazione" value="{{ old('destinazione') }}"
                       class="form-control @error('destinazione') is-invalid @enderror">
                @error('destinazione') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Merce</label>
                <input name="merce" value="{{ old('merce') }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Tipo mezzo</label>
                <input name="tipo_mezzo" value="{{ old('tipo_mezzo') }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Peso min (t)</label>
                <input name="peso_min" value="{{ old('peso_min') }}"
                       class="form-control @error('peso_min') is-invalid @enderror" placeholder="es. 10.00">
                @error('peso_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Peso max (t)</label>
                <input name="peso_max" value="{{ old('peso_max') }}"
                       class="form-control @error('peso_max') is-invalid @enderror" placeholder="es. 26.00">
                @error('peso_max') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Peso minimo fatturabile (t) *</label>
                <input name="peso_minimo_fatturabile" value="{{ old('peso_minimo_fatturabile', 0) }}"
                       class="form-control @error('peso_minimo_fatturabile') is-invalid @enderror">
                @error('peso_minimo_fatturabile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">Sotto questo peso si fattura comunque questo minimo.</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Prezzo base *</label>
                <input name="prezzo_base" value="{{ old('prezzo_base') }}"
                       class="form-control @error('prezzo_base') is-invalid @enderror" placeholder="es. 150.00">
                @error('prezzo_base') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Tipo prezzo *</label>
                <select name="tipo_prezzo" class="form-select @error('tipo_prezzo') is-invalid @enderror">
                    @foreach ($tipiPrezzo as $chiave => $etichetta)
                        <option value="{{ $chiave }}" @selected(old('tipo_prezzo') === $chiave)>{{ $etichetta }}</option>
                    @endforeach
                </select>
                @error('tipo_prezzo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Fermo macchina (ore)</label>
                <input name="fermo_macchina_ore" value="{{ old('fermo_macchina_ore', 0) }}"
                       class="form-control @error('fermo_macchina_ore') is-invalid @enderror" placeholder="es. 2.5">
                @error('fermo_macchina_ore') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Prezzo fermo macchina (€/ora)</label>
                <input name="prezzo_fermo_macchina_ora" value="{{ old('prezzo_fermo_macchina_ora', 0) }}"
                       class="form-control @error('prezzo_fermo_macchina_ora') is-invalid @enderror" placeholder="es. 40.00">
                @error('prezzo_fermo_macchina_ora') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Supplementi (€)</label>
                <input name="supplementi" value="{{ old('supplementi', 0) }}"
                       class="form-control @error('supplementi') is-invalid @enderror" placeholder="es. 25.00">
                @error('supplementi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Data validità *</label>
                <input type="date" name="data_validita" value="{{ old('data_validita') }}"
                       class="form-control @error('data_validita') is-invalid @enderror">
                @error('data_validita') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Stato *</label>
                <select name="stato" class="form-select @error('stato') is-invalid @enderror">
                    <option value="bozza" @selected(old('stato') === 'bozza')>Bozza</option>
                    <option value="inviata" @selected(old('stato') === 'inviata')>Inviata</option>
                </select>
                @error('stato') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Note</label>
                <textarea name="note" rows="3" class="form-control">{{ old('note') }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-primary" type="submit">Salva Offerta</button>
            <a href="{{ route('offerte.index') }}" class="btn btn-outline-secondary">Annulla</a>
        </div>
    </form>
@endsection

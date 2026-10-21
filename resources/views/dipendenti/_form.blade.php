@php
    $campo = fn ($nome) => old($nome, $dipendente->$nome);
    $data = fn ($nome) => old($nome, $dipendente->$nome?->format('Y-m-d'));
@endphp

@if ($errors->any())
    <div class="alert alert-danger">Controlla i campi evidenziati.</div>
@endif

<div class="bovi-card mb-3">
    <h2 class="h6 mb-3">Dati personali</h2>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Cognome *</label>
            <input name="cognome" value="{{ $campo('cognome') }}"
                   class="form-control @error('cognome') is-invalid @enderror">
            @error('cognome') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Nome *</label>
            <input name="nome" value="{{ $campo('nome') }}"
                   class="form-control @error('nome') is-invalid @enderror">
            @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Codice fiscale</label>
            <input name="codice_fiscale" value="{{ $campo('codice_fiscale') }}" maxlength="16"
                   class="form-control text-uppercase @error('codice_fiscale') is-invalid @enderror">
            @error('codice_fiscale') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Data di nascita</label>
            <input type="date" name="data_nascita" value="{{ $data('data_nascita') }}"
                   class="form-control @error('data_nascita') is-invalid @enderror">
            @error('data_nascita') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-8">
            <label class="form-label">Luogo di nascita</label>
            <input name="luogo_nascita" value="{{ $campo('luogo_nascita') }}" class="form-control">
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

        <div class="col-md-6">
            <label class="form-label">Telefono</label>
            <input name="telefono" value="{{ $campo('telefono') }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input name="email" value="{{ $campo('email') }}"
                   class="form-control @error('email') is-invalid @enderror">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="bovi-card mb-3">
    <h2 class="h6 mb-3">Rapporto di lavoro</h2>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Mansione *</label>
            <select name="mansione" id="mansione" class="form-select @error('mansione') is-invalid @enderror">
                @foreach (\App\Models\Dipendente::MANSIONI as $valore => $etichetta)
                    <option value="{{ $valore }}" @selected($campo('mansione') === $valore)>{{ $etichetta }}</option>
                @endforeach
            </select>
            @error('mansione') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Data di assunzione</label>
            <input type="date" name="data_assunzione" value="{{ $data('data_assunzione') }}"
                   class="form-control @error('data_assunzione') is-invalid @enderror">
            @error('data_assunzione') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Data di cessazione</label>
            <input type="date" name="data_cessazione" value="{{ $data('data_cessazione') }}"
                   class="form-control @error('data_cessazione') is-invalid @enderror">
            @error('data_cessazione') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12" id="blocco-mezzi">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Trattore abituale</label>
                    <select name="trattore_id" class="form-select @error('trattore_id') is-invalid @enderror">
                        <option value="">-</option>
                        @foreach ($trattori as $t)
                            <option value="{{ $t->id }}" @selected((string) $campo('trattore_id') === (string) $t->id)>
                                {{ $t->targa }} {{ trim($t->marca.' '.$t->modello) }}{{ $t->attivo ? '' : ' (non attivo)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('trattore_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rimorchio abituale</label>
                    <select name="rimorchio_id" class="form-select @error('rimorchio_id') is-invalid @enderror">
                        <option value="">-</option>
                        @foreach ($rimorchi as $r)
                            <option value="{{ $r->id }}" @selected((string) $campo('rimorchio_id') === (string) $r->id)>
                                {{ $r->targa }} {{ \App\Models\Mezzo::SOTTOTIPI[$r->sottotipo] ?? '' }}{{ $r->attivo ? '' : ' (non attivo)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('rimorchio_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label">Note</label>
            <textarea name="note" rows="3" class="form-control">{{ $campo('note') }}</textarea>
        </div>

        <div class="col-12">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="attivo" value="1" id="attivo"
                       @checked(old('attivo', $dipendente->attivo))>
                <label class="form-check-label" for="attivo">Attivo</label>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button class="btn btn-primary" type="submit">Salva</button>
    <a href="{{ route('dipendenti.index') }}" class="btn btn-outline-secondary">Annulla</a>
</div>

<script>
    (function () {
        const mansione = document.getElementById('mansione');
        const blocco = document.getElementById('blocco-mezzi');

        function aggiorna() {
            blocco.classList.toggle('d-none', mansione.value !== 'autista');
        }

        mansione.addEventListener('change', aggiorna);
        aggiorna();
    })();
</script>
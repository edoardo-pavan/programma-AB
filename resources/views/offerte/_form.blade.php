@php
    $campo = fn ($nome) => old($nome, $offerta->$nome);
    $etichettaLuogo = fn ($l) => $l->nome . ($l->comune ? ' (' . $l->comune . ')' : '');
@endphp

@if ($errors->any())
    <div class="alert alert-danger">Controlla i campi evidenziati.</div>
@endif

@if ($offerta->offerta_origine_id && $offerta->origine)
    <div class="alert alert-info">
        Copia dell'offerta n. {{ $offerta->origine->numero_formattato }}.
        Modifica quello che cambia e poi pubblicala: riceverà un nuovo numero.
    </div>
@endif

<div class="bovi-card mb-3">
    <h2 class="h6 mb-3">Cliente e tratta</h2>
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Cliente *</label>
            <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror">
                <option value="">- scegli -</option>
                @foreach ($clienti as $c)
                    <option value="{{ $c->id }}" @selected((string) $campo('cliente_id') === (string) $c->id)>{{ $c->ragione_sociale }}</option>
                @endforeach
            </select>
            @error('cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Data dell'offerta *</label>
            <input type="date" name="data_offerta" value="{{ old('data_offerta', $offerta->data_offerta?->format('Y-m-d')) }}"
                   class="form-control @error('data_offerta') is-invalid @enderror">
            @error('data_offerta') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Partenza *</label>
            <select name="partenza_luogo_id" class="form-select @error('partenza_luogo_id') is-invalid @enderror">
                <option value="">- scegli -</option>
                @foreach ($luoghi as $l)
                    <option value="{{ $l->id }}" @selected((string) $campo('partenza_luogo_id') === (string) $l->id)>{{ $etichettaLuogo($l) }}</option>
                @endforeach
            </select>
            @error('partenza_luogo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Destinazione *</label>
            <select name="destinazione_luogo_id" class="form-select @error('destinazione_luogo_id') is-invalid @enderror">
                <option value="">- scegli -</option>
                @foreach ($luoghi as $l)
                    <option value="{{ $l->id }}" @selected((string) $campo('destinazione_luogo_id') === (string) $l->id)>{{ $etichettaLuogo($l) }}</option>
                @endforeach
            </select>
            @error('destinazione_luogo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 form-text mt-0">
            Il luogo non c'è? <a href="{{ route('luoghi.create') }}" target="_blank">Crealo in una nuova scheda</a> e poi ricarica questa pagina.
        </div>
    </div>
</div>

<div class="bovi-card mb-3">
    <h2 class="h6 mb-3">Prezzi</h2>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Tipo di mezzo *</label>
            <select name="tipo_mezzo" id="tipo_mezzo" class="form-select @error('tipo_mezzo') is-invalid @enderror">
                <option value="">- scegli -</option>
                @foreach (\App\Models\Offerta::TIPI_MEZZO as $valore => $etichetta)
                    <option value="{{ $valore }}" @selected($campo('tipo_mezzo') === $valore)>{{ $etichetta }}</option>
                @endforeach
            </select>
            @error('tipo_mezzo') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label d-block">Il prezzo è *</label>
            @foreach (\App\Models\Offerta::TIPI_PREZZO as $valore => $etichetta)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_prezzo" id="tipo_prezzo_{{ $valore }}"
                           value="{{ $valore }}" @checked($campo('tipo_prezzo') === $valore)>
                    <label class="form-check-label" for="tipo_prezzo_{{ $valore }}">{{ $etichetta }}</label>
                </div>
            @endforeach
            @error('tipo_prezzo') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label" id="etichetta-prezzo">Prezzo *</label>
            <input type="number" step="0.01" min="0" name="prezzo" value="{{ $campo('prezzo') }}"
                   class="form-control @error('prezzo') is-invalid @enderror">
            @error('prezzo') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Prezzo padroncini (€)</label>
            <input type="number" step="0.01" min="0" name="prezzo_padroncini" value="{{ $campo('prezzo_padroncini') }}"
                   class="form-control @error('prezzo_padroncini') is-invalid @enderror">
            @error('prezzo_padroncini') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4" id="blocco-minimo">
            <label class="form-label">Minimo fatturabile (tonnellate) *</label>
            <input type="number" step="0.01" min="0" name="minimo_fatturabile" id="minimo_fatturabile"
                   value="{{ $campo('minimo_fatturabile') }}"
                   class="form-control @error('minimo_fatturabile') is-invalid @enderror">
            @error('minimo_fatturabile') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="bovi-card mb-3">
    <h2 class="h6 mb-3">Condizioni</h2>
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Fermo macchina (€/ora) *</label>
            <input type="number" step="0.01" min="0" name="fermo_orario" value="{{ $campo('fermo_orario') }}"
                   class="form-control @error('fermo_orario') is-invalid @enderror">
            @error('fermo_orario') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Ore di fermo gratuite *</label>
            <input type="number" step="0.25" min="0" max="24" name="fermo_franchigia_ore"
                   value="{{ $campo('fermo_franchigia_ore') }}"
                   class="form-control @error('fermo_franchigia_ore') is-invalid @enderror">
            @error('fermo_franchigia_ore') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Pagamento *</label>
            <input name="pagamento" value="{{ $campo('pagamento') }}"
                   class="form-control @error('pagamento') is-invalid @enderror">
            @error('pagamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <div class="form-check form-switch">
                <input type="hidden" name="iva_esclusa" value="0">
                <input type="checkbox" class="form-check-input" name="iva_esclusa" value="1" id="iva_esclusa"
                       @checked(old('iva_esclusa', $offerta->iva_esclusa))>
                <label class="form-check-label" for="iva_esclusa">IVA esclusa</label>
            </div>
        </div>
    </div>
</div>

<div class="bovi-card mb-3">
    <h2 class="h6 mb-3">Materiale e note</h2>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Codici CER</label>
            <input name="codici_cer" value="{{ $campo('codici_cer') }}"
                   class="form-control @error('codici_cer') is-invalid @enderror" placeholder="es. 17 05 04, 17 09 04">
            @error('codici_cer') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Tonnellaggio stimato</label>
            <input name="tonnellaggio_stimato" value="{{ $campo('tonnellaggio_stimato') }}"
                   class="form-control @error('tonnellaggio_stimato') is-invalid @enderror" placeholder="es. 5.000 t">
            @error('tonnellaggio_stimato') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
            <label class="form-label">Descrizione del materiale</label>
            <input name="descrizione_materiale" value="{{ $campo('descrizione_materiale') }}"
                   class="form-control @error('descrizione_materiale') is-invalid @enderror">
            @error('descrizione_materiale') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
            <label class="form-label">Note</label>
            <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror">{{ $campo('note') }}</textarea>
            @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        @if ($offerta->offerta_origine_id)
            <div class="col-12">
                <label class="form-label">Note sulla copia (cosa cambia rispetto all'originale)</label>
                <textarea name="note_copia" rows="2" class="form-control @error('note_copia') is-invalid @enderror">{{ $campo('note_copia') }}</textarea>
                @error('note_copia') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        @endif
    </div>
</div>

<div class="d-flex gap-2 flex-wrap">
    <button class="btn btn-outline-primary" type="submit" name="azione" value="bozza">
        <i class="bi bi-save"></i> Salva bozza
    </button>
    <button class="btn btn-primary" type="submit" name="azione" value="pubblica"
            onclick="return confirm('Una volta pubblicata l\'offerta non si potrà più modificare. Continuare?');">
        <i class="bi bi-send-check"></i> Pubblica offerta
    </button>
    <a href="{{ route('offerte.index') }}" class="btn btn-outline-secondary">Annulla</a>
</div>

<script>
    (function () {
        const mezzo = document.getElementById('tipo_mezzo');
        const minimo = document.getElementById('minimo_fatturabile');
        const bloccoMinimo = document.getElementById('blocco-minimo');
        const etichettaPrezzo = document.getElementById('etichetta-prezzo');
        const predefiniti = @json(\App\Models\Offerta::MINIMO_PREDEFINITO);
        let modificato = {{ $offerta->exists ? 'true' : 'false' }};

        minimo.addEventListener('input', function () { modificato = true; });

        mezzo.addEventListener('change', function () {
            if (!modificato && predefiniti[mezzo.value]) {
                minimo.value = predefiniti[mezzo.value];
            }
        });

        function aggiornaPrezzo() {
            const scelto = document.querySelector('input[name="tipo_prezzo"]:checked');
            const tonnellata = !scelto || scelto.value === 'a_tonnellata';
            bloccoMinimo.classList.toggle('d-none', !tonnellata);
            etichettaPrezzo.textContent = tonnellata ? 'Prezzo (€ per tonnellata) *' : 'Prezzo (€ per viaggio) *';
        }

        document.querySelectorAll('input[name="tipo_prezzo"]').forEach(function (r) {
            r.addEventListener('change', aggiornaPrezzo);
        });
        aggiornaPrezzo();
    })();
</script>

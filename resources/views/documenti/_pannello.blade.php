@php
    $sezioni = $proprietario->documentiSuddivisi();
    $tipi = $proprietario->tipiDocumento();
    $bag = $errors->getBag('documento');

    $config = [];
    foreach (array_keys($tipi) as $chiave) {
        $config[$chiave] = \App\Models\Documento::TIPI[$chiave];
    }

    $dataImmatricolazione = $proprietario->getAttribute('data_immatricolazione');
@endphp

<div class="bovi-card mt-4" id="documenti">
    <h2 class="h6 mb-3">Documenti e scadenze</h2>

    @if (session('ok_documento'))
        <div class="alert alert-success">{{ session('ok_documento') }}</div>
    @endif

    @if ($sezioni['correnti']->isEmpty())
        <p class="text-muted">Nessun documento caricato.</p>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Scadenza</th>
                        <th>File</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sezioni['correnti'] as $d)
                        @include('documenti._riga', ['d' => $d])
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($sezioni['storico']->isNotEmpty())
        <details class="mb-3">
            <summary class="text-muted">Storico ({{ $sezioni['storico']->count() }} versioni precedenti)</summary>
            <div class="table-responsive mt-2">
                <table class="table align-middle text-muted">
                    <tbody>
                        @foreach ($sezioni['storico'] as $d)
                            @include('documenti._riga', ['d' => $d, 'storico' => true])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
    @endif

    <hr>
    <h3 class="h6">Aggiungi documento</h3>

    <form method="POST" enctype="multipart/form-data"
          action="{{ route('documenti.store', ['entita' => $entita, 'id' => $proprietario->id]) }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tipo di documento *</label>
                <select name="doc[tipo]" id="doc-tipo"
                        class="form-select @error('doc.tipo', 'documento') is-invalid @enderror">
                    <option value="">- scegli -</option>
                    @foreach ($tipi as $chiave => $etichetta)
                        <option value="{{ $chiave }}" @selected(old('doc.tipo') === $chiave)>{{ $etichetta }}</option>
                    @endforeach
                </select>
                @error('doc.tipo', 'documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-8 d-none" id="doc-aiuto">
                <div class="alert alert-info py-2 mb-0 small" id="doc-aiuto-testo"></div>
            </div>

            <div class="col-md-4 d-none" id="doc-titolo">
                <label class="form-label"><span class="etichetta">Titolo</span> <span class="req text-danger d-none">*</span></label>
                <input name="doc[titolo]" value="{{ old('doc.titolo') }}"
                       class="form-control @error('doc.titolo', 'documento') is-invalid @enderror">
                @error('doc.titolo', 'documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 d-none" id="doc-numero">
                <label class="form-label"><span class="etichetta">Numero</span> <span class="req text-danger d-none">*</span></label>
                <input name="doc[numero]" value="{{ old('doc.numero') }}"
                       class="form-control @error('doc.numero', 'documento') is-invalid @enderror">
                @error('doc.numero', 'documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 d-none" id="doc-immatricolazione">
                <label class="form-label"><span class="etichetta">Data di immatricolazione</span> <span class="req text-danger d-none">*</span></label>
                <input type="date" name="doc[data_immatricolazione]"
                       value="{{ old('doc.data_immatricolazione', $dataImmatricolazione?->format('Y-m-d')) }}"
                       class="form-control @error('doc.data_immatricolazione', 'documento') is-invalid @enderror">
                @error('doc.data_immatricolazione', 'documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 d-none" id="doc-data">
                <label class="form-label"><span class="etichetta">Data</span> <span class="req text-danger d-none">*</span></label>
                <input type="date" name="doc[data_emissione]" value="{{ old('doc.data_emissione') }}"
                       class="form-control @error('doc.data_emissione', 'documento') is-invalid @enderror">
                @error('doc.data_emissione', 'documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 d-none" id="doc-scadenza">
                <label class="form-label"><span class="etichetta">Scadenza</span> <span class="req text-danger d-none">*</span></label>
                <input type="date" name="doc[data_scadenza]" value="{{ old('doc.data_scadenza') }}"
                       class="form-control @error('doc.data_scadenza', 'documento') is-invalid @enderror">
                @error('doc.data_scadenza', 'documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-8 d-none" id="doc-file">
                <label class="form-label"><span class="etichetta">File</span> <span class="req text-danger d-none">*</span></label>
                <input type="file" name="doc[file]"
                       class="form-control @error('doc.file', 'documento') is-invalid @enderror">
                @error('doc.file', 'documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 d-none" id="doc-note">
                <label class="form-label">Note</label>
                <input name="doc[note]" value="{{ old('doc.note') }}" class="form-control">
            </div>
        </div>

        <button class="btn btn-primary mt-3 d-none" id="doc-invia" type="submit">
            <i class="bi bi-paperclip"></i> Aggiungi
        </button>
    </form>
</div>

<script>
    (function () {
        const cfg = @json($config);
        const immatricolazioneObbligatoria = @json(! $dataImmatricolazione);
        const select = document.getElementById('doc-tipo');

        function imposta(id, visibile, etichetta, obbligatorio) {
            const box = document.getElementById(id);
            box.classList.toggle('d-none', !visibile);
            if (!visibile) return;
            box.querySelector('.etichetta').textContent = etichetta;
            box.querySelector('.req').classList.toggle('d-none', !obbligatorio);
        }

        function aggiorna() {
            const r = cfg[select.value];

            ['doc-titolo', 'doc-numero', 'doc-data', 'doc-scadenza', 'doc-file', 'doc-immatricolazione']
                .forEach(function (id) { imposta(id, false); });

            const aiuto = document.getElementById('doc-aiuto');
            const note = document.getElementById('doc-note');
            const invia = document.getElementById('doc-invia');

            if (!r) {
                aiuto.classList.add('d-none');
                note.classList.add('d-none');
                invia.classList.add('d-none');
                return;
            }

            imposta('doc-titolo', !!r.titolo, 'Titolo', r.titolo === 'obbligatorio');
            imposta('doc-numero', !!r.numero, r.numero, false);
            imposta('doc-immatricolazione', !!r.immatricolazione, 'Data di immatricolazione', immatricolazioneObbligatoria);
            imposta('doc-data', !!r.data, r.data, false);
            imposta('doc-scadenza', r.scadenza !== 'no', r.etichetta_scadenza, r.scadenza === 'si');
            imposta('doc-file', r.file !== 'no', 'File (PDF o immagine, max 10 MB)', r.file === 'obbligatorio');

            document.getElementById('doc-aiuto-testo').textContent = r.aiuto || '';
            aiuto.classList.toggle('d-none', !r.aiuto);
            note.classList.remove('d-none');
            invia.classList.remove('d-none');
        }

        select.addEventListener('change', aggiorna);
        aggiorna();
    })();
</script>

@if ($bag->any())
    <script>document.getElementById('documenti').scrollIntoView();</script>
@endif
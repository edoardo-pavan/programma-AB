@php
    $senzaFile = (\App\Models\Documento::TIPI[$d->tipo]['file'] ?? 'facoltativo') === 'no';
@endphp
<tr>
    <td>
        <div class="fw-semibold">{{ $d->etichetta_tipo }}</div>
        @if ($d->numero) <small class="text-muted d-block">N. {{ $d->numero }}</small> @endif
        @if ($d->data_emissione)
            <small class="text-muted d-block">{{ $d->etichetta_data }}: {{ $d->data_emissione->format('d/m/Y') }}</small>
        @endif
        @if ($d->note) <small class="text-muted d-block">{{ $d->note }}</small> @endif
    </td>
    <td>
        @if ($d->data_scadenza)
            <div>{{ $d->data_scadenza->format('d/m/Y') }}</div>
            <small class="text-muted">{{ $d->etichetta_scadenza }}</small>
            @unless ($storico ?? false)
                <div><span class="badge {{ $d->classe_badge }}">{{ $d->testo_scadenza }}</span></div>
            @endunless
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    <td>
        @if ($d->file_path)
            <a href="{{ route('documenti.apri', $d) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-eye"></i> Apri
            </a>
            <a href="{{ route('documenti.scarica', $d) }}" class="btn btn-sm btn-outline-secondary" title="Scarica">
                <i class="bi bi-download"></i>
            </a>
        @elseif ($senzaFile)
            <span class="text-muted">-</span>
        @else
            <span class="text-muted">Nessun file</span>
        @endif
    </td>
    <td class="text-end">
        <form method="POST" action="{{ route('documenti.destroy', $d) }}"
              onsubmit="return confirm('Eliminare questo documento? Non si può annullare.');">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-outline-danger" type="submit" title="Elimina">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </td>
</tr>
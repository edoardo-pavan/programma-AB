<?php

namespace App\Http\Controllers;

use App\Models\Dipendente;
use App\Models\Documento;
use App\Models\Mezzo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentoController extends Controller
{
    private const ENTITA = [
        'mezzi' => Mezzo::class,
        'dipendenti' => Dipendente::class,
    ];

    public function store(Request $request, string $entita, int $id)
    {
        $classe = self::ENTITA[$entita];
        $proprietario = $classe::findOrFail($id);
        $ammessi = array_keys($proprietario->tipiDocumento());

        $tipo = $request->input('doc.tipo');
        $regole = in_array($tipo, $ammessi, true) ? Documento::TIPI[$tipo] : null;

        $verifiche = [
            'doc.tipo' => ['required', Rule::in($ammessi)],
            'doc.note' => ['nullable', 'string', 'max:1000'],
        ];
        $nomi = [
            'doc.tipo' => 'tipo di documento',
            'doc.note' => 'note',
        ];

        // Le regole cambiano in base al tipo di documento scelto
        if ($regole) {
            if ($regole['titolo']) {
                $verifiche['doc.titolo'] = [
                    $regole['titolo'] === 'obbligatorio' ? 'required' : 'nullable',
                    'string', 'max:150',
                ];
                $nomi['doc.titolo'] = 'titolo';
            }

            if ($regole['numero']) {
                $verifiche['doc.numero'] = ['nullable', 'string', 'max:100'];
                $nomi['doc.numero'] = mb_strtolower($regole['numero']);
            }

            if ($regole['data']) {
                $verifiche['doc.data_emissione'] = ['nullable', 'date', 'before_or_equal:today'];
                $nomi['doc.data_emissione'] = mb_strtolower($regole['data']);
            }

            if ($regole['scadenza'] !== 'no') {
                $verifiche['doc.data_scadenza'] = [
                    $regole['scadenza'] === 'si' ? 'required' : 'nullable',
                    'date',
                    Rule::when(
                        $regole['data'] && $request->filled('doc.data_emissione'),
                        'after_or_equal:doc.data_emissione'
                    ),
                ];
                $nomi['doc.data_scadenza'] = mb_strtolower($regole['etichetta_scadenza']);
            }

            if ($regole['file'] !== 'no') {
                $verifiche['doc.file'] = [
                    $regole['file'] === 'obbligatorio' ? 'required' : 'nullable',
                    'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp',
                ];
                $nomi['doc.file'] = 'file';
            }

            if ($regole['immatricolazione']) {
                $verifiche['doc.data_immatricolazione'] = [
                    $proprietario->getAttribute('data_immatricolazione') ? 'nullable' : 'required',
                    'date', 'before_or_equal:today',
                ];
                $nomi['doc.data_immatricolazione'] = 'data di immatricolazione';
            }
        }

        $dati = $request->validateWithBag('documento', $verifiche, [], $nomi)['doc'];

        $datiFile = [];

        if ($regole['file'] !== 'no' && ($file = $request->file('doc.file'))) {
            $datiFile = [
                'file_nome' => $file->getClientOriginalName(),
                'file_mime' => $file->getMimeType(),
                'file_dimensione' => $file->getSize(),
                'file_path' => $file->store('documenti/' . $entita, 'local'),
            ];
        }

        $proprietario->documenti()->create([
            'tipo' => $dati['tipo'],
            'titolo' => $dati['titolo'] ?? null,
            'numero' => $dati['numero'] ?? null,
            'data_emissione' => $dati['data_emissione'] ?? null,
            'data_scadenza' => $dati['data_scadenza'] ?? null,
            'note' => $dati['note'] ?? null,
        ] + $datiFile);

        // L'immatricolazione si comunica con il libretto ma appartiene al mezzo
        if (! empty($dati['data_immatricolazione'])) {
            $proprietario->update(['data_immatricolazione' => $dati['data_immatricolazione']]);
        }

        return redirect()->to(url()->previous() . '#documenti')
            ->with('ok_documento', 'Documento aggiunto.');
    }

    public function apri(Documento $documento)
    {
        $this->controllaFile($documento);

        return Storage::disk('local')->response($documento->file_path, $documento->file_nome);
    }

    public function scarica(Documento $documento)
    {
        $this->controllaFile($documento);

        return Storage::disk('local')->download($documento->file_path, $documento->file_nome);
    }

    public function destroy(Documento $documento)
    {
        if ($documento->file_path) {
            Storage::disk('local')->delete($documento->file_path);
        }

        $documento->delete();

        return redirect()->to(url()->previous() . '#documenti')
            ->with('ok_documento', 'Documento eliminato.');
    }

    private function controllaFile(Documento $documento): void
    {
        abort_unless(
            $documento->file_path && Storage::disk('local')->exists($documento->file_path),
            404
        );
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Offerta extends Model
{
    use HasFactory;

    protected $table = 'offerte';

    protected $guarded = [];

    protected $casts = [
        'data_offerta' => 'date',
        'pubblicata_il' => 'datetime',
        'iva_esclusa' => 'boolean',
        'prezzo' => 'decimal:2',
        'prezzo_padroncini' => 'decimal:2',
        'minimo_fatturabile' => 'decimal:2',
        'fermo_orario' => 'decimal:2',
        'fermo_franchigia_ore' => 'decimal:2',
    ];

    public const TIPI_MEZZO = [
        'BILICO' => 'Bilico',
        'BILICO RIBALTABILE' => 'Bilico ribaltabile',
        '4 ASSI' => '4 assi',
    ];

    public const TIPI_PREZZO = [
        'a_tonnellata' => 'A tonnellata',
        'a_viaggio' => 'A viaggio',
    ];

    public const STATI = [
        'bozza' => 'Bozza',
        'attiva' => 'Attiva',
        'scaduta' => 'Scaduta',
        'archiviata' => 'Archiviata',
    ];

    public const MINIMO_PREDEFINITO = ['BILICO' => 30, 'BILICO RIBALTABILE' => 30, '4 ASSI' => 25];
    public const FERMO_PREDEFINITO = 75;
    public const FRANCHIGIA_PREDEFINITA = 1;
    public const PAGAMENTO_PREDEFINITO = 'RB 60+10 gg. d.f.f.m.';

    protected static function booted(): void
    {
        static::updating(function (Offerta $offerta) {
            if ($offerta->getOriginal('stato') === 'bozza') {
                return;
            }

            if (array_diff(array_keys($offerta->getDirty()), ['stato', 'updated_at'])) {
                throw new \LogicException('Un\'offerta pubblicata non si può modificare: crea una copia.');
            }
        });

        static::deleting(function (Offerta $offerta) {
            if ($offerta->stato !== 'bozza') {
                throw new \LogicException('Un\'offerta pubblicata non si può eliminare.');
            }
        });
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Soggetto::class, 'cliente_id');
    }

    public function partenza(): BelongsTo
    {
        return $this->belongsTo(Luogo::class, 'partenza_luogo_id');
    }

    public function destinazione(): BelongsTo
    {
        return $this->belongsTo(Luogo::class, 'destinazione_luogo_id');
    }

    public function origine(): BelongsTo
    {
        return $this->belongsTo(Offerta::class, 'offerta_origine_id');
    }

    public function derivate(): HasMany
    {
        return $this->hasMany(Offerta::class, 'offerta_origine_id');
    }

    public function autore(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creata_da');
    }

    public function isBozza(): bool
    {
        return $this->stato === 'bozza';
    }

    public function pubblica(): void
    {
        DB::transaction(function () {
            $contatore = DB::table('contatori')->where('chiave', 'offerte')->lockForUpdate()->first();
            $numero = $contatore->valore + 1;

            DB::table('contatori')->where('chiave', 'offerte')->update([
                'valore' => $numero,
                'updated_at' => now(),
            ]);

            $this->numero = $numero;
            $this->stato = 'attiva';
            $this->pubblicata_il = now();
            $this->save();
        });
    }

    public function getNumeroFormattatoAttribute(): string
    {
        return $this->numero ? str_pad((string) $this->numero, 5, '0', STR_PAD_LEFT) : 'Bozza';
    }

    public function getEtichettaStatoAttribute(): string
    {
        return self::STATI[$this->stato] ?? $this->stato;
    }

    public function getClasseStatoAttribute(): string
    {
        return match ($this->stato) {
            'bozza' => 'text-bg-secondary',
            'attiva' => 'text-bg-success',
            'scaduta' => 'text-bg-warning',
            default => 'text-bg-dark',
        };
    }

    public function getPrezzoFormattatoAttribute(): string
    {
        return '€ ' . number_format((float) $this->prezzo, 2, ',', '.')
            . ($this->tipo_prezzo === 'a_tonnellata' ? '/t' : '/viaggio');
    }

    public function getTestoMinimoAttribute(): ?string
    {
        if ($this->minimo_fatturabile === null) {
            return null;
        }

        return rtrim(rtrim(number_format((float) $this->minimo_fatturabile, 2, ',', '.'), '0'), ',') . ' t';
    }

    public function getTestoFermoAttribute(): string
    {
        $base = '€ ' . number_format((float) $this->fermo_orario, 2, ',', '.') . '/h';
        $franchigia = (float) $this->fermo_franchigia_ore;

        if ($franchigia <= 0) {
            return $base;
        }

        if ($franchigia == 1.0) {
            return $base . ' dopo la 1ª ora';
        }

        $ore = rtrim(rtrim(number_format($franchigia, 2, ',', '.'), '0'), ',');

        return $base . ' dopo le prime ' . $ore . ' ore';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Offerta extends Model
{
    protected $guarded = [];

    protected $casts = [
        'peso_min' => 'decimal:2',
        'peso_max' => 'decimal:2',
        'peso_minimo_fatturabile' => 'decimal:2',
        'prezzo_base' => 'decimal:2',
        'fermo_macchina_ore' => 'decimal:2',
        'prezzo_fermo_macchina_ora' => 'decimal:2',
        'supplementi' => 'decimal:2',
        'totale' => 'decimal:2',
        'data_validita' => 'date',
    ];

    public function soggetto(): BelongsTo
    {
        return $this->belongsTo(Soggetto::class);
    }

    public function viaggio(): HasOne
    {
        return $this->hasOne(Viaggio::class);
    }

    public function getPuòEssereAccettataAttribute(): bool
    {
        return $this->stato === 'bozza' || $this->stato === 'inviata';
    }

    public function calcolaTotale(): float
    {
        $totale = (float) $this->prezzo_base;

        if ($this->tipo_prezzo === 'tonnellata') {
            $peso = max((float) $this->peso_minimo_fatturabile, (float) $this->peso_min ?? 0);
            $totale = $peso * (float) $this->prezzo_base;
        }

        $totale += (float) $this->fermo_macchina_ore * (float) $this->prezzo_fermo_macchina_ora;
        $totale += (float) $this->supplementi;

        return round($totale, 2);
    }
}

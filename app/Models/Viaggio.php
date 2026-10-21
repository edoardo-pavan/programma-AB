<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Viaggio extends Model
{
    protected $guarded = [];

    protected $casts = [
        'data_viaggio' => 'date',
        'km_previsti' => 'integer',
        'km_effettivi' => 'integer',
        'peso_trasportato' => 'decimal:2',
        'prezzo_concordato' => 'decimal:2',
    ];

    public function offerta(): BelongsTo
    {
        return $this->belongsTo(Offerta::class);
    }

    public function mezzo(): BelongsTo
    {
        return $this->belongsTo(Mezzo::class);
    }

    public function dipendente(): BelongsTo
    {
        return $this->belongsTo(Dipendente::class);
    }

    public function soggetto(): BelongsTo
    {
        return $this->belongsTo(Soggetto::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dipendente extends Model
{
    protected $table = 'dipendenti';

    protected $guarded = [];

    protected $casts = [
        'data_nascita' => 'date',
        'data_assunzione' => 'date',
        'data_cessazione' => 'date',
        'attivo' => 'boolean',
    ];

    public const MANSIONI = [
        'autista' => 'Autista',
        'ufficio' => 'Ufficio',
        'officina' => 'Officina',
        'altro' => 'Altro',
    ];

    public function trattore(): BelongsTo
    {
        return $this->belongsTo(Mezzo::class, 'trattore_id');
    }

    public function rimorchio(): BelongsTo
    {
        return $this->belongsTo(Mezzo::class, 'rimorchio_id');
    }

    public function getNomeCompletoAttribute(): string
    {
        return trim($this->cognome . ' ' . $this->nome);
    }
}
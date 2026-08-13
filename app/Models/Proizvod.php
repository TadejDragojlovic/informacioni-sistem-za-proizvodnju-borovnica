<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proizvod extends Model
{
    use HasFactory;

    protected $table = 'proizvods';

    protected $fillable = [
        'naziv',
        'opis',
        'sorta_id',
        'neto_kolicina_g',
        'cena',
        'aktivan',
    ];

    protected function casts(): array
    {
        return [
            'sorta_id' => 'integer',
            'neto_kolicina_g' => 'integer',
            'cena' => 'decimal:2',
            'aktivan' => 'boolean',
        ];
    }

    public function sorta(): BelongsTo
    {
        return $this->belongsTo(Sorta::class, 'sorta_id');
    }

    public function narudzbinaStavkas(): HasMany
    {
        return $this->hasMany(NarudzbinaStavka::class, 'proizvod_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NarudzbinaStavka extends Model
{
    use HasFactory;

    protected $table = 'narudzbina_stavkas';

    protected $fillable = [
        'narudzbina_id',
        'proizvod_id',
        'kolicina',
        'neto_kolicina_g',
        'cena_po_jedinici',
    ];

    protected function casts(): array
    {
        return [
            'narudzbina_id' => 'integer',
            'proizvod_id' => 'integer',
            'kolicina' => 'integer',
            'neto_kolicina_g' => 'integer',
            'cena_po_jedinici' => 'decimal:2',
        ];
    }

    public function narudzbina(): BelongsTo
    {
        return $this->belongsTo(Narudzbina::class, 'narudzbina_id');
    }

    public function proizvod(): BelongsTo
    {
        return $this->belongsTo(Proizvod::class, 'proizvod_id');
    }

    public function raspodele(): HasMany
    {
        return $this->hasMany(LotRaspodela::class, 'narudzbina_stavka_id');
    }
}

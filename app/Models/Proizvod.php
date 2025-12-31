<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proizvod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'naziv',
        'opis',
        'kolicina',
        'cena',
        'skladiste_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'cena' => 'decimal:2',
            'skladiste_id' => 'integer',
        ];
    }

    public function skladiste(): BelongsTo
    {
        return $this->belongsTo(Skladiste::class);
    }

    public function narudzbinaStavkas(): HasMany
    {
        return $this->hasMany(NarudzbinaStavka::class);
    }
}

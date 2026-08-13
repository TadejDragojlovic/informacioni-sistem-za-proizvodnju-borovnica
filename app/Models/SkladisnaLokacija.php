<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkladisnaLokacija extends Model
{
    use HasFactory;

    protected $table = 'skladisna_lokacija';

    protected $fillable = [
        'skladiste_id',
        'naziv',
        'opis',
        'aktivna',
    ];

    protected function casts(): array
    {
        return [
            'skladiste_id' => 'integer',
            'aktivna' => 'boolean',
        ];
    }

    public function skladiste(): BelongsTo
    {
        return $this->belongsTo(Skladiste::class, 'skladiste_id');
    }

    public function lotovi(): HasMany
    {
        return $this->hasMany(Lot::class, 'trenutna_skladisna_lokacija_id');
    }

    public function prethodniLotDogadjaji(): HasMany
    {
        return $this->hasMany(LotDogadjaj::class, 'prethodna_skladisna_lokacija_id');
    }

    public function noviLotDogadjaji(): HasMany
    {
        return $this->hasMany(LotDogadjaj::class, 'nova_skladisna_lokacija_id');
    }
}

<?php

namespace App\Models;

use App\Enums\KlasaKvaliteta;
use App\Enums\LotStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    use HasFactory;

    protected $table = 'lots';

    protected $fillable = [
        'oznaka',
        'sorta_id',
        'parcela_id',
        'trenutna_skladisna_lokacija_id',
        'datum_berbe',
        'pocetna_kolicina_g',
        'raspoloziva_kolicina_g',
        'status',
        'klasa_kvaliteta',
        'broj_dokumenta_kvaliteta',
        'napomena',
    ];

    protected function casts(): array
    {
        return [
            'sorta_id' => 'integer',
            'parcela_id' => 'integer',
            'trenutna_skladisna_lokacija_id' => 'integer',
            'datum_berbe' => 'date',
            'pocetna_kolicina_g' => 'integer',
            'raspoloziva_kolicina_g' => 'integer',
            'status' => LotStatus::class,
            'klasa_kvaliteta' => KlasaKvaliteta::class,
        ];
    }

    public function sorta(): BelongsTo
    {
        return $this->belongsTo(Sorta::class, 'sorta_id');
    }

    public function parcela(): BelongsTo
    {
        return $this->belongsTo(Parcela::class, 'parcela_id');
    }

    public function trenutnaSkladisnaLokacija(): BelongsTo
    {
        return $this->belongsTo(SkladisnaLokacija::class, 'trenutna_skladisna_lokacija_id');
    }

    public function raspodele(): HasMany
    {
        return $this->hasMany(LotRaspodela::class, 'lot_id');
    }

    public function dogadjaji(): HasMany
    {
        return $this->hasMany(LotDogadjaj::class, 'lot_id');
    }

    public function resursi(): HasMany
    {
        return $this->hasMany(Resurs::class, 'lot_id');
    }
}

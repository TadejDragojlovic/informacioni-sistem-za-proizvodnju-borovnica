<?php

namespace App\Models;

use App\Enums\NarudzbinaStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Narudzbina extends Model
{
    use HasFactory;

    protected $table = 'narudzbinas';

    protected $fillable = [
        'user_id',
        'status',
        'adresa_isporuke',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'status' => NarudzbinaStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function narudzbinaStavkas(): HasMany
    {
        return $this->hasMany(NarudzbinaStavka::class, 'narudzbina_id');
    }

    public function stavke(): HasMany
    {
        return $this->narudzbinaStavkas();
    }

    public function raspodele(): HasManyThrough
    {
        return $this->hasManyThrough(
            LotRaspodela::class,
            NarudzbinaStavka::class,
            'narudzbina_id',
            'narudzbina_stavka_id'
        );
    }

    public function proizvodi(): BelongsToMany
    {
        return $this->belongsToMany(
            Proizvod::class,
            'narudzbina_stavkas',
            'narudzbina_id',
            'proizvod_id'
        )->withPivot('kolicina', 'neto_kolicina_g', 'cena_po_jedinici');
    }
}

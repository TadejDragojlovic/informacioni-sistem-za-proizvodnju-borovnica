<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Narudzbina extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'datum_narudzbine',
        'status',
        'user_id',
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
            'datum_narudzbine' => 'date',
            'ukupna_cena' => 'decimal:2',
            'user_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function narudzbinaStavkas(): HasMany
    {
        return $this->hasMany(NarudzbinaStavka::class);
    }

    public function stavke()
    {
        return $this->hasMany(NarudzbinaStavka::class, 'narudzbina_id');
    }

    public function proizvodi()
    {
        return $this->belongsToMany(Proizvod::class, 'narudzbina_stavkas', 'narudzbina_id', 'proizvod_id')
                ->withPivot('kolicina', 'cena');
    }
}

<?php

namespace App\Models;

use App\Enums\LotRaspodelaStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LotRaspodela extends Model
{
    use HasFactory;

    protected $table = 'lot_raspodela';

    protected $fillable = [
        'lot_id',
        'narudzbina_stavka_id',
        'broj_pakovanja',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'lot_id' => 'integer',
            'narudzbina_stavka_id' => 'integer',
            'broj_pakovanja' => 'integer',
            'status' => LotRaspodelaStatus::class,
        ];
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }

    public function narudzbinaStavka(): BelongsTo
    {
        return $this->belongsTo(NarudzbinaStavka::class, 'narudzbina_stavka_id');
    }

    public function dogadjaji(): HasMany
    {
        return $this->hasMany(LotDogadjaj::class, 'lot_raspodela_id');
    }
}

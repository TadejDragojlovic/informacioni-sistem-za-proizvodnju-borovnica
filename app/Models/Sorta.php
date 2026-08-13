<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sorta extends Model
{
    use HasFactory;

    protected $table = 'sortas';

    protected $fillable = [
        'naziv',
        'opis',
    ];

    public function proizvodi(): HasMany
    {
        return $this->hasMany(Proizvod::class, 'sorta_id');
    }

    public function lotovi(): HasMany
    {
        return $this->hasMany(Lot::class, 'sorta_id');
    }
}
